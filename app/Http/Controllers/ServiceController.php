<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

    private function getBookingAmountForService(string $serviceType): float
    {
        return match ($serviceType) {
            'private-office' => 200.0,
            'meeting-room' => 175.0,
            default => 150.0,
        };
    }

    /**
     * Get the active floor plan for a service type
     */
    private function getFloorPlanForService($serviceType)
    {
        $serviceMapping = $this->getServiceMapping();
        $targetCompany = $serviceMapping[$serviceType] ?? 'Produktiv';
        
        return $this->getActiveFloorPlanForCompany($targetCompany);
    }

    public function index()
    {
        return view('services.index');
    }
    
    public function booking()
    {
        return view('services.booking');
    }

    public function nestBooking()
    {
        return view('services.nest-booking');
    }

    public function meshBooking()
    {
        return view('services.mesh-booking');
    }

    public function selectSeat(Request $request)
    {
        $serviceType = $request->query('service', 'hot-desk');

        $bookingAmount = $this->getBookingAmountForService($serviceType);
        $bookingBackRoute = match ($serviceType) {
            'private-office' => route('services.nest-booking'),
            'meeting-room' => route('services.mesh-booking'),
            default => route('services.booking'),
        };

        // Get the active floor plan for the service type
        $floorPlan = $this->getFloorPlanForService($serviceType);
        
        // Load booking statuses for the floor plan items
        $bookingStatuses = [];
        if ($floorPlan && $floorPlan->layout_data) {
            $selectedDate = $request->query('date', date('Y-m-d'));
            $selectedTime = $request->query('time', '09:00');
            
            // Get all active bookings for this floor plan on the selected date (exclude cancelled)
            $bookings = \App\Models\Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $selectedDate)
                ->where('booking_time', $selectedTime)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->get();
            
            // Create status mapping for each item
            foreach ($floorPlan->layout_data as $item) {
                if (isset($item['id'])) {
                    // Get the most recent booking for this seat
                    $booking = $bookings->where('seat_id', $item['id'])->sortByDesc('created_at')->first();
                    if ($booking) {
                        $bookingStatuses[$item['id']] = $booking->status;
                    } else {
                        $bookingStatuses[$item['id']] = 'available';
                    }
                }
            }
        }
        
        
        return view('services.select-seat', compact(
            'serviceType',
            'floorPlan',
            'bookingStatuses',
            'bookingAmount',
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
        ]);

        // Additional validation to prevent past date bookings
        $bookingDate = \Carbon\Carbon::parse($request->booking_date);
        $today = \Carbon\Carbon::today();
        
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

        $existingBooking = Booking::where('seat_id', $request->seat_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This seat is already booked for the selected date and time.',
            ], 400);
        }

        // Get the active floor plan for the service type
        $floorPlan = $this->getFloorPlanForService($request->service_type);
        
        if (!$floorPlan) {
            return response()->json([
                'success' => false,
                'message' => 'No floor plan found for this service.',
            ], 400);
        }
        
        $hubOwner = $floorPlan->hubOwner;
        if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid hub owner configuration.',
            ], 400);
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'hub_owner_id' => $hubOwner->id,
            'hub_name' => $hubOwner->company ?: 'PWESTO Workspace',
            'service_type' => $request->service_type,
            'seat_id' => $request->seat_id,
            'seat_label' => $request->seat_label,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'start_time' => $request->booking_time,  // Use booking_time as start_time
            'end_time' => $request->booking_time,     // Use booking_time as end_time
            'status' => 'pending',
            'amount' => $this->getBookingAmountForService($request->service_type),
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

        $existingBooking = Booking::where('seat_id', $request->seat_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'This seat is already booked for the selected date and time.',
            ], 400);
        }

        $floorPlan = $this->getFloorPlanForService($request->service_type);

        if (!$floorPlan) {
            return response()->json([
                'success' => false,
                'message' => 'No floor plan found for this service.',
            ], 400);
        }

        $hubOwner = $floorPlan->hubOwner;
        if (!$hubOwner || $hubOwner->role !== 'hub_owner') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid hub owner configuration.',
            ], 400);
        }

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
            'end_time' => $request->end_time ?: $request->booking_time,
            'status' => 'pending',
            'amount' => $this->getBookingAmountForService($request->service_type),
            'notes' => 'Booking created via payment checkout',
        ]);

        $secretKey = config('services.paymongo.secret_key');
        if (!$secretKey) {
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
                    'reference_number' => 'BOOKING-' . $booking->id,
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
        ]);
    }

    public function checkBookingStatus(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'service_type' => 'required|string',
        ]);

        // Get the active floor plan for the service type
        $floorPlan = $this->getFloorPlanForService($request->service_type);
        
        $bookingStatuses = [];

        if ($floorPlan && $floorPlan->layout_data) {
            // Get all active bookings for this floor plan on the selected date and time (exclude cancelled)
            $bookings = Booking::where('hub_owner_id', $floorPlan->hub_owner_id)
                ->where('booking_date', $request->date)
                ->where('booking_time', $request->time)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->get();
            
            foreach ($floorPlan->layout_data as $item) {
                if (isset($item['id'])) {
                    // Get the most recent booking for this seat
                    $booking = $bookings->where('seat_id', $item['id'])->sortByDesc('created_at')->first();
                    $bookingStatuses[$item['id']] = $booking ? $booking->status : 'available';
                }
            }
        }

        return response()->json([
            'success' => true,
            'bookingStatuses' => $bookingStatuses
        ]);
    }
} 