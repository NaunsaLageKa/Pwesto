<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\FloorPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * FloorPlanController handles all floor plan operations for hub owners
 * This controller manages creating, saving, loading, and exporting floor plans
 */
class FloorPlanController extends Controller
{
    /**
     * Display the floor plan editor page
     * Loads the active floor plan for the current hub owner
     */
    public function index()
    {
        $currentUser = auth()->user();

        $floorPlan = $this->getActiveFloorPlanForCompany($currentUser->company);

        return view('hub-owner.floor-plan', compact('floorPlan'));
    }

    /**
     * Save floor plan data to database (supports multiple floors per workspace).
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'layout_data' => 'nullable|array',
                'floors' => 'nullable|array',
                'floors.*.id' => 'required_with:floors|integer',
                'floors.*.name' => 'required_with:floors|string|max:255',
                'floors.*.items' => 'nullable|array',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            $currentUser = auth()->user();
            $companyHubOwners = $this->getHubOwnersForCompany($currentUser->company);

            if ($request->filled('floors')) {
                $layoutPayload = [
                    'version' => 2,
                    'floors' => collect($request->input('floors'))->map(function ($floor, $index) {
                        return [
                            'id' => (int) ($floor['id'] ?? ($index + 1)),
                            'name' => (string) ($floor['name'] ?? FloorPlan::defaultFloorName($index + 1)),
                            'items' => is_array($floor['items'] ?? null) ? $floor['items'] : [],
                        ];
                    })->values()->all(),
                ];
            } else {
                $items = $request->input('layout_data', []);
                $layoutPayload = FloorPlan::normalizeLayoutPayload($items);
            }

            $existing = FloorPlan::whereIn('hub_owner_id', $companyHubOwners)
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->first();

            $attributes = [
                'name' => $request->input('name', 'My Floor Plan'),
                'layout_data' => $layoutPayload,
                'description' => $request->input('description'),
                'is_active' => true,
            ];

            if ($existing) {
                $existing->update($attributes);
                $floorPlan = $existing;
            } else {
                $floorPlan = FloorPlan::create(array_merge($attributes, [
                    'hub_owner_id' => auth()->id(),
                ]));
            }

            return response()->json([
                'success' => true,
                'message' => 'Floor plan saved successfully!',
                'floor_plan_id' => $floorPlan->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving floor plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Load existing floor plan data from database (all floors).
     */
    public function load(): JsonResponse
    {
        try {
            $currentUser = auth()->user();
            $companyHubOwners = $this->getHubOwnersForCompany($currentUser->company);

            $floorPlan = FloorPlan::whereIn('hub_owner_id', $companyHubOwners)
                ->where('is_active', true)
                ->orderByDesc('updated_at')
                ->first();

            if (! $floorPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No floor plan found.',
                    'floors' => FloorPlan::emptyMultiFloorPayload()['floors'],
                    'layout_data' => [],
                ]);
            }

            $normalized = FloorPlan::normalizeLayoutPayload($floorPlan->layout_data);

            return response()->json([
                'success' => true,
                'floors' => $normalized['floors'],
                'layout_data' => $normalized['floors'][0]['items'] ?? [],
                'name' => $floorPlan->name,
                'description' => $floorPlan->description,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No Floor plan found: ' . $e->getMessage(),
                'floors' => FloorPlan::emptyMultiFloorPayload()['floors'],
                'layout_data' => [],
            ], 500);
        }
    }
}
