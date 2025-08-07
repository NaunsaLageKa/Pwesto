<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FloorPlanController extends Controller
{
    public function index()
    {
        $floorPlan = FloorPlan::where('hub_owner_id', auth()->id())
            ->where('is_active', true)
            ->first();
            
        return view('hub-owner.floor-plan', compact('floorPlan'));
    }

    public function save(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'layout_data' => 'required|array',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            $floorPlan = FloorPlan::updateOrCreate(
                [
                    'hub_owner_id' => auth()->id(),
                    'is_active' => true,
                ],
                [
                    'name' => $request->input('name', 'My Floor Plan'),
                    'layout_data' => $request->input('layout_data'),
                    'description' => $request->input('description'),
                ]
            );

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

    public function load(): JsonResponse
    {
        try {
            $floorPlan = FloorPlan::where('hub_owner_id', auth()->id())
                ->where('is_active', true)
                ->first();

            if (!$floorPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No floor plan found.',
                    'layout_data' => [],
                ]);
            }

            return response()->json([
                'success' => true,
                'layout_data' => $floorPlan->layout_data,
                'name' => $floorPlan->name,
                'description' => $floorPlan->description,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading floor plan: ' . $e->getMessage(),
                'layout_data' => [],
            ], 500);
        }
    }

    public function export(): JsonResponse
    {
        try {
            $floorPlan = FloorPlan::where('hub_owner_id', auth()->id())
                ->where('is_active', true)
                ->first();

            if (!$floorPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No floor plan found to export.',
                ]);
            }

            return response()->json([
                'success' => true,
                'floor_plan' => $floorPlan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting floor plan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
