<?php

namespace App\Http\Controllers\HubOwner;

use App\Http\Controllers\Controller;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * FloorPlanController handles all floor plan operations for hub owners
 * This controller manages creating, saving, loading, and exporting floor plans
 */
class FloorPlanController extends Controller
{
    /**
     * Display the floor plan editor page
     * Loads the active floor plan for the current hub owner
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the active floor plan for the current authenticated hub owner
        $floorPlan = FloorPlan::where('hub_owner_id', auth()->id())
            ->where('is_active', true)
            ->first();
            
        // Return the floor plan editor view with the floor plan data
        return view('hub-owner.floor-plan', compact('floorPlan'));
    }

    /**
     * Save floor plan data to database
     * Accepts layout data (items, positions, etc.) and stores it
     * 
     * @param Request $request Contains layout_data, name, and description
     * @return JsonResponse Success or error response
     */
    public function save(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'layout_data' => 'required|array',  // Array of floor plan items
                'name' => 'nullable|string|max:255',  // Optional floor plan name
                'description' => 'nullable|string',  // Optional description
            ]);

            // Debug: Log the current user and company
            \Log::info('Floor plan save attempt', [
                'user_id' => auth()->id(),
                'user_company' => auth()->user()->company ?? 'No company',
                'layout_data_count' => count($request->input('layout_data', []))
            ]);

            // First, deactivate all existing floor plans for this hub owner
            FloorPlan::where('hub_owner_id', auth()->id())->update(['is_active' => false]);
            
            // Debug: Log the data being saved
            \Log::info('Floor plan data to save', [
                'layout_data' => $request->input('layout_data'),
                'count' => count($request->input('layout_data', []))
            ]);
            
            // Create new floor plan for this hub owner
            $floorPlan = FloorPlan::create([
                'hub_owner_id' => auth()->id(),
                'name' => $request->input('name', 'My Floor Plan'),
                'layout_data' => $request->input('layout_data'),
                'description' => $request->input('description'),
                'is_active' => true,
            ]);

            // Debug: Log successful save
            \Log::info('Floor plan saved successfully', [
                'floor_plan_id' => $floorPlan->id,
                'hub_owner_id' => $floorPlan->hub_owner_id
            ]);

            // Return success response with floor plan ID
            return response()->json([
                'success' => true,
                'message' => 'Floor plan saved successfully!',
                'floor_plan_id' => $floorPlan->id,
            ]);
        } catch (\Exception $e) {
            // Debug: Log the error
            \Log::error('Floor plan save failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'user_company' => auth()->user()->company ?? 'No company'
            ]);

            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Error saving floor plan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Load existing floor plan data from database
     * Returns the layout data for the active floor plan
     * 
     * @return JsonResponse Floor plan data or empty if none found
     */
    public function load(): JsonResponse
    {
        try {
            // Get the active floor plan for the current hub owner
            $floorPlan = FloorPlan::where('hub_owner_id', auth()->id())
                ->where('is_active', true)
                ->first();

            // If no floor plan exists, return empty data
            if (!$floorPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'No floor plan found.',
                    'layout_data' => [],
                ]);
            }

            // Return the floor plan data including layout, name, and description
            return response()->json([
                'success' => true,
                'layout_data' => $floorPlan->layout_data,  // Array of floor plan items
                'name' => $floorPlan->name,
                'description' => $floorPlan->description,
            ]);
        } catch (\Exception $e) {
            // Return error response if loading fails
            return response()->json([
                'success' => false,
                'message' => 'Error loading floor plan: ' . $e->getMessage(),
                'layout_data' => [],
            ], 500);
        }
    }

}
