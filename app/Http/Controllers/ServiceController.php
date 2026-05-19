<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\FloorPlan;
use App\Models\User;
use App\Models\Booking;

class ServiceController extends Controller
{
    /**
     * Get the service to hub owner mapping
     */
    private function getServiceMapping()
    {
        return [
            'hot-desk' => 'Produktiv',
            'private-office' => 'Nest Workspaces',
            'meeting-room' => 'Mesh Media',
        ];
    }

    /**
     * Hub company name fragments to try for floor plan / owner lookup (DB naming varies).
     *
     * @return list<string>
     */
    private function hubCompanyCandidatesForService(string $serviceType): array
    {
        return match ($serviceType) {
            'private-office' => ['Nest Workspaces', 'Nest'],
            'meeting-room' => ['Mesh Media', 'Mesh'],
            default => ['Produktiv'],
        };
    }

    /**
     * Human-readable reference shown to the customer and sent to PayMongo as reference_number.
     */
    private function generateBookingTransactionNumber(): string
    {
        do {
            $txn = 'PWE-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
        } while (Booking::where('transaction_number', $txn)->exists());

        return $txn;
    }

    /**
     * Hourly rate in PHP for the service (total charge = rate × booking window hours).
     */
    private function getHourlyRateForService(string $serviceType): float
    {
        return match ($serviceType) {
            'private-office' => 200.0,
            'meeting-room' => 175.0,
            default => 150.0,
        };
    }

    /**
     * Total amount for the booking window (same interval rules as occupancy / overlap checks).
     */
    private function calculateBookingTotalAmount(string $serviceType, string $dateYmd, string $startTime, ?string $endTime): float
    {
        $hourly = $this->getHourlyRateForService($serviceType);
        [$start, $end] = $this->queryOccupancyInterval($dateYmd, $startTime, $endTime);
        $minutes = $start->diffInMinutes($end);
        $hours = max($minutes / 60, 1 / 60);

        return round($hourly * $hours, 2);
    }

    /**
     * Block bookings whose start is at or before "now" when the booking date is today.
     */
    private function jsonIfBookingStartsInPast(string $dateYmd, string $startTime): ?\Illuminate\Http\JsonResponse
    {
        if (! Carbon::parse($dateYmd)->isSameDay(Carbon::today())) {
            return null;
        }

        $start = $this->carbonOnDate($dateYmd, $startTime);
        if ($start->lte(now())) {
            return response()->json([
                'success' => false,
                'message' => 'That start time has already passed today. Please choose a later time.',
            ], 400);
        }

        return null;
    }

    /**
     * Get the active floor plan for a service type
     */
    private function getFloorPlanForService($serviceType)
    {
        foreach ($this->hubCompanyCandidatesForService($serviceType) as $company) {
            $plan = $this->getActiveFloorPlanForCompany(trim($company));
            if ($plan) {
                return $plan;
            }
        }

        return null;
    }

    /**
     * Resolve hub owner for booking/payment even when floor plan is missing.
     */
    private function getHubOwnerForService(string $serviceType, $floorPlan = null): ?User
    {
        if ($floorPlan && $floorPlan->hubOwner && $floorPlan->hubOwner->role === 'hub_owner') {
            return $floorPlan->hubOwner;
        }

        foreach ($this->hubCompanyCandidatesForService($serviceType) as $targetCompany) {
            $targetCompany = trim($targetCompany);
            $hubOwner = User::where('role', 'hub_owner')
                ->where('status', 'approved')
                ->whereRaw('LOWER(company) LIKE ?', ['%' . strtolower($targetCompany) . '%'])
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($hubOwner) {
                return $hubOwner;
            }
        }

        return null;
    }

    public function index()
    {
        return view('services.index');
    }
    
    public function booking()
    {
        if ($redirect = $this->redirectIfUserBannedForServiceType('hot-desk')) {
            return $redirect;
        }

        return view('services.booking');
    }

    public function nestBooking()
    {
        if ($redirect = $this->redirectIfUserBannedForServiceType('private-office')) {
            return $redirect;
        }

        return view('services.nest-booking');
    }

    public function meshBooking()
    {
        if ($redirect = $this->redirectIfUserBannedForServiceType('meeting-room')) {
            return $redirect;
        }

        return view('services.mesh-booking');
    }

    public function selectSeat(Request $request)
    {
        $serviceType = $request->query('service', 'hot-desk');

        if ($redirect = $this->redirectIfUserBannedForServiceType($serviceType)) {
            return $redirect;
        }

        $hourlyRate = $this->getHourlyRateForService($serviceType);
        $bookingBackRoute = match ($serviceType) {
            'private-office' => route('services.nest-booking'),
            'meeting-room' => route('services.mesh-booking'),
            default => route('services.booking'),
        };

        // Get the active floor plan for the service type
        $floorPlan = $this->getFloorPlanForService($serviceType);
        $floorPlanFloors = [];
        $selectedFloorId = 1;
        $layoutItems = [];
        if ($floorPlan) {
            $floorPlanFloors = $floorPlan->floorsList();
            $selectedFloorId = (int) $request->query('floor', $floorPlanFloors[0]['id'] ?? 1);
            $validIds = collect($floorPlanFloors)->pluck('id')->map(fn ($id) => (int) $id);
            if (! $validIds->contains($selectedFloorId)) {
                $selectedFloorId = (int) $floorPlanFloors[0]['id'];
            }
            $layoutItems = $floorPlan->layoutItemsForFloor($selectedFloorId);
        }

        // Load booking statuses for the floor plan items (overlap with selected time range)
        $bookingStatuses = [];
        if ($floorPlan && $layoutItems !== []) {
            $selectedDate = $request->query('date', date('Y-m-d'));
            $selectedTime = $request->query('time', '09:00');
            $selectedEnd = $request->query('end_time');

            [$rangeStart, $rangeEnd] = $this->queryOccupancyInterval($selectedDate, $selectedTime, $selectedEnd);

            $bookings = Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $selectedDate)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->orderByDesc('created_at')
                ->get();

            foreach ($layoutItems as $item) {
                if (isset($item['id'])) {
                    $booking = $bookings
                        ->filter(fn (Booking $b) => (string) $b->seat_id === (string) $item['id'])
                        ->first(fn (Booking $b) => $this->bookingOverlapsInterval($b, $rangeStart, $rangeEnd));

                    $bookingStatuses[$item['id']] = $booking ? $booking->status : 'available';
                }
            }
        }
        
        
        return view('services.select-seat', compact(
            'serviceType',
            'floorPlan',
            'floorPlanFloors',
            'selectedFloorId',
            'layoutItems',
            'bookingStatuses',
            'hourlyRate',
            'bookingBackRoute'
        ));
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'seat_id' => 'required|string',
            'seat_label' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'end_time' => 'nullable|string',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to book.',
            ], 401);
        }

        if ($blocked = $this->jsonIfUserBannedForServiceType($request->service_type)) {
            return $blocked;
        }

        // Additional validation to prevent past date bookings
        $bookingDate = Carbon::parse($request->booking_date);
        $today = Carbon::today();
        
        if ($bookingDate->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book for past dates. Please select today or a future date.',
            ], 400);
        }
        
        // Also check if the date is before today (not just past)
        if ($bookingDate->lt($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book for dates before today. Please select today or a future date.',
            ], 400);
        }

        if ($past = $this->jsonIfBookingStartsInPast($request->booking_date, $request->booking_time)) {
            return $past;
        }

        [$newStart, $newEnd] = $this->queryOccupancyInterval(
            $request->booking_date,
            $request->booking_time,
            $request->input('end_time')
        );

        $overlap = Booking::where('seat_id', $request->seat_id)
            ->where('booking_date', $request->booking_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->first(fn (Booking $b) => $this->bookingOverlapsInterval($b, $newStart, $newEnd));

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This seat is already booked for part of the selected time range.',
            ], 400);
        }

        $floorPlan = $this->getFloorPlanForService($request->service_type);
        $hubOwner = $this->getHubOwnerForService($request->service_type, $floorPlan);

        if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
            return response()->json([
                'success' => false,
                'message' => 'No available hub owner found for this service.',
            ], 400);
        }

        $endTimeStr = $request->input('end_time');
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'hub_owner_id' => $hubOwner->id,
            'hub_name' => $hubOwner->company ?: 'PWESTO Workspace',
            'service_type' => $request->service_type,
            'seat_id' => $request->seat_id,
            'seat_label' => $request->seat_label,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'start_time' => $request->booking_time,
            'end_time' => $endTimeStr ?: $newEnd->format('H:i:s'),
            'status' => 'pending',
            'amount' => $this->calculateBookingTotalAmount(
                $request->service_type,
                $request->booking_date,
                $request->booking_time,
                $request->input('end_time')
            ),
            'notes' => 'Booking created via floor plan selection',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully!',
            'booking_id' => $booking->id,
        ]);
    }

    public function createBookingPayment(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in again before making a payment.',
            ], 401);
        }

        $request->validate([
            'service_type' => 'required|string',
            'seat_id' => 'required|string',
            'seat_label' => 'required|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'end_time' => 'nullable|string',
            'payment_method' => 'required|in:card,gcash',
        ]);

        if ($blocked = $this->jsonIfUserBannedForServiceType($request->service_type)) {
            return $blocked;
        }

        if ($past = $this->jsonIfBookingStartsInPast($request->booking_date, $request->booking_time)) {
            return $past;
        }

        [$newStart, $newEnd] = $this->queryOccupancyInterval(
            $request->booking_date,
            $request->booking_time,
            $request->input('end_time')
        );

        $overlap = Booking::where('seat_id', $request->seat_id)
            ->where('booking_date', $request->booking_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->first(fn (Booking $b) => $this->bookingOverlapsInterval($b, $newStart, $newEnd));

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'This seat is already booked for part of the selected time range.',
            ], 400);
        }

        $floorPlan = $this->getFloorPlanForService($request->service_type);
        $hubOwner = $this->getHubOwnerForService($request->service_type, $floorPlan);

        if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
            return response()->json([
                'success' => false,
                'message' => 'No available hub owner found for this service.',
            ], 400);
        }

        $transactionNumber = $this->generateBookingTransactionNumber();

        $endTimeStr = $request->input('end_time');
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'hub_owner_id' => $hubOwner->id,
            'hub_name' => $hubOwner->company ?: 'PWESTO Workspace',
            'service_type' => $request->service_type,
            'seat_id' => $request->seat_id,
            'seat_label' => $request->seat_label,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'start_time' => $request->booking_time,
            'end_time' => $endTimeStr ?: $newEnd->format('H:i:s'),
            'status' => 'pending',
            'amount' => $this->calculateBookingTotalAmount(
                $request->service_type,
                $request->booking_date,
                $request->booking_time,
                $request->input('end_time')
            ),
            'transaction_number' => $transactionNumber,
            'notes' => 'Booking created via payment checkout',
        ]);

        $secretKey = config('services.paymongo.secret_key');
        if (!$secretKey) {
            if (app()->environment('local')) {
                return response()->json([
                    'success' => true,
                    'checkout_url' => route('booking-history') . '?payment=success&booking=' . $booking->id . '&mock=1',
                    'booking_id' => $booking->id,
                    'transaction_number' => $transactionNumber,
                ]);
            }

            $booking->delete();
            return response()->json([
                'success' => false,
                'message' => 'PayMongo secret key is not configured.',
            ], 500);
        }

        if (str_starts_with($secretKey, 'pk_')) {
            return response()->json([
                'success' => false,
                'message' => 'PayMongo secret key is invalid. Use an sk_test_ or sk_live_ key for PAYMONGO_SECRET_KEY.',
            ], 500);
        }

        $paymentMethodTypes = $request->payment_method === 'gcash' ? ['gcash'] : ['card'];
        $amountInCentavos = (int) round(((float) $booking->amount) * 100);

        $payload = [
            'data' => [
                'attributes' => [
                    'send_email_receipt' => true,
                    'show_description' => true,
                    'show_line_items' => true,
                    'description' => 'PWESTO seat booking payment',
                    'line_items' => [[
                        'currency' => 'PHP',
                        'amount' => $amountInCentavos,
                        'name' => 'Seat Booking - ' . $booking->seat_label,
                        'quantity' => 1,
                    ]],
                    'payment_method_types' => $paymentMethodTypes,
                    'reference_number' => $transactionNumber,
                    'success_url' => route('booking-history') . '?payment=success&booking=' . $booking->id,
                    'cancel_url' => route('services.select-seat', ['service' => $request->service_type]) . '?payment=cancelled',
                ],
            ],
        ];

        try {
            $httpClient = Http::withBasicAuth($secretKey, '')
                ->acceptJson()
                ->timeout(20);

            // Windows local environments can fail TLS verification if CA bundle is missing.
            // Allow insecure fallback only for local development.
            if (app()->environment('local')) {
                $httpClient = $httpClient->withOptions(['verify' => false]);
            }

            $response = $httpClient->post('https://api.paymongo.com/v1/checkout_sessions', $payload);
        } catch (\Throwable $e) {
            $booking->delete();
            Log::error('PayMongo checkout session request failed.', [
                'booking_id' => $booking->id,
                'payment_method' => $request->payment_method,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to connect to PayMongo. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }

        if (!$response->successful()) {
            $booking->delete();
            Log::warning('PayMongo checkout session creation failed.', [
                'booking_id' => $booking->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $detailMessage = data_get($response->json(), 'errors.0.detail')
                ?? data_get($response->json(), 'errors.0.code')
                ?? data_get($response->json(), 'data.attributes.message')
                ?? 'Unable to start PayMongo checkout.';

            return response()->json([
                'success' => false,
                'message' => $detailMessage,
                'status' => $response->status(),
                'details' => $response->json(),
            ], 500);
        }

        $checkoutUrl = data_get($response->json(), 'data.attributes.checkout_url');
        if (!$checkoutUrl) {
            $booking->delete();

            return response()->json([
                'success' => false,
                'message' => 'PayMongo checkout URL was not returned.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'checkout_url' => $checkoutUrl,
            'booking_id' => $booking->id,
            'transaction_number' => $transactionNumber,
        ]);
    }

    public function checkBookingStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'end_time' => 'nullable|string',
            'service_type' => 'required|string',
            'floor' => 'nullable|integer',
        ]);

        if ($blocked = $this->jsonIfUserBannedForServiceType($request->service_type)) {
            return $blocked;
        }

        // Get the active floor plan for the service type
        $floorPlan = $this->getFloorPlanForService($request->service_type);
        $floorId = $request->input('floor') ? (int) $request->input('floor') : null;
        $layoutItems = $floorPlan ? $floorPlan->layoutItemsForFloor($floorId) : [];

        $bookingStatuses = [];

        if ($floorPlan && $layoutItems !== []) {
            [$rangeStart, $rangeEnd] = $this->queryOccupancyInterval(
                $request->date,
                $request->time,
                $request->input('end_time')
            );

            $bookings = Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $request->date)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->orderByDesc('created_at')
                ->get();

            foreach ($layoutItems as $item) {
                if (isset($item['id'])) {
                    $booking = $bookings
                        ->filter(fn (Booking $b) => (string) $b->seat_id === (string) $item['id'])
                        ->first(fn (Booking $b) => $this->bookingOverlapsInterval($b, $rangeStart, $rangeEnd));

                    $bookingStatuses[$item['id']] = $booking ? $booking->status : 'available';
                }
            }
        }

        return response()->json([
            'success' => true,
            'bookingStatuses' => $bookingStatuses
        ]);
    }

    private function carbonOnDate(string $dateYmd, $time): Carbon
    {
        if ($time instanceof Carbon) {
            return Carbon::parse($dateYmd . ' ' . $time->format('H:i:s'));
        }

        return Carbon::parse($dateYmd . ' ' . trim((string) $time));
    }

    /**
     * Requested booking window as [start, end). End time is exclusive: e.g. 1:00 PM–7:00 PM
     * blocks the seat through the 6:00 PM hour; 7:00 PM onward stays available.
     */
    private function queryOccupancyInterval(string $dateYmd, string $startTime, ?string $endTime): array
    {
        $start = $this->carbonOnDate($dateYmd, $startTime);
        if (!$endTime || trim($endTime) === '') {
            return [$start, $start->copy()->addHour()];
        }
        $end = $this->carbonOnDate($dateYmd, $endTime);
        if ($end->lte($start)) {
            $end = $start->copy()->addHour();
        }

        return [$start, $end];
    }

    /**
     * Stored booking occupancy as [start, end). If end is missing or not after start, defaults to one hour.
     */
    private function bookingOccupancyInterval(Booking $b): array
    {
        $date = $b->booking_date instanceof Carbon
            ? $b->booking_date->format('Y-m-d')
            : Carbon::parse($b->booking_date)->format('Y-m-d');

        $start = $this->carbonOnDate($date, $b->start_time ?? $b->booking_time);
        $end = $this->carbonOnDate($date, $b->end_time ?? $b->booking_time);

        if ($end->lte($start)) {
            $end = $start->copy()->addHour();
        }

        return [$start, $end];
    }

    private function intervalsOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lt($bEnd) && $bStart->lt($aEnd);
    }

    private function bookingOverlapsInterval(Booking $b, Carbon $qStart, Carbon $qEnd): bool
    {
        [$bStart, $bEnd] = $this->bookingOccupancyInterval($b);

        return $this->intervalsOverlap($bStart, $bEnd, $qStart, $qEnd);
    }

    private function resolveHubOwnerForServiceType(string $serviceType): ?User
    {
        $floorPlan = $this->getFloorPlanForService($serviceType);

        return $this->getHubOwnerForService($serviceType, $floorPlan);
    }

    private function redirectIfUserBannedForServiceType(string $serviceType): ?\Illuminate\Http\RedirectResponse
    {
        if (!auth()->check()) {
            return null;
        }

        $hubOwner = $this->resolveHubOwnerForServiceType($serviceType);
        if ($hubOwner && auth()->user()->isBannedFromHubOwner($hubOwner->id)) {
            return redirect()->route('services.index')
                ->with('error', 'You are Ban!');
        }

        return null;
    }

    private function jsonIfUserBannedForServiceType(string $serviceType): ?\Illuminate\Http\JsonResponse
    {
        if (!auth()->check()) {
            return null;
        }

        $hubOwner = $this->resolveHubOwnerForServiceType($serviceType);
        if ($hubOwner && auth()->user()->isBannedFromHubOwner($hubOwner->id)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot book this workspace.',
            ], 403);
        }

        return null;
    }
}
