@extends('layouts.app')

@section('content')
@php
    $bookingAmount = $bookingAmount ?? 150;
    $bookingBackRoute = $bookingBackRoute ?? route('services.booking');
@endphp
<div class="min-h-screen bg-gray-800">
    <!-- Navigation Header -->
    <div class="bg-white shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-6">
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="admin-button">
                        Admin Panel
                    </a>
                    @endif
                    <div class="text-2xl font-bold text-teal-600 tracking-wider">PWESTO!</div>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
                    <a href="{{ route('booking-history') }}" class="nav-link">Booking History</a>
                    <a href="{{ route('services.index') }}" class="nav-link">Services</a>
                    <a href="{{ route('about') }}" class="nav-link">About</a>
                    <a href="{{ route('location') }}" class="nav-link">Location</a>
                    <x-profile-dropdown />
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-yellow-500 mb-2">
                    {{ $floorPlan && $floorPlan->hubOwner ? strtoupper($floorPlan->hubOwner->company) : 'WORKSPACE' }}
                </h1>
                <p class="text-xl text-white">Select Seat</p>
            </div>
            
            <!-- Booking Controls -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-white font-medium">Date:</label>
                    <input type="date" id="booking-date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-white font-medium">Time Start:</label>
                    <select id="booking-time" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                        <option value="19:00">7:00 PM</option>
                        <option value="20:00">8:00 PM</option>
                        <option value="21:00">9:00 PM</option>
                        <option value="22:00">10:00 PM</option>
                        <option value="23:00">11:00 PM</option>
                        <option value="00:00">12:00 AM</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-white font-medium">End Time:</label>
                    <select id="booking-end-time" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select End Time</option>
                        <option value="08:00">8:00 AM</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                        <option value="18:00">6:00 PM</option>
                        <option value="19:00">7:00 PM</option>
                        <option value="20:00">8:00 PM</option>
                        <option value="21:00">9:00 PM</option>
                        <option value="22:00">10:00 PM</option>
                        <option value="23:00">11:00 PM</option>
                        <option value="00:00">12:00 AM</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Floor Plan Container -->
        <div class="bg-white rounded-lg shadow-xl p-6 mb-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Floor Plan</h2>
                                 <p class="text-gray-600">Click on a <strong>chair</strong> to select it for booking</p>
            </div>
            
                         <!-- Floor Plan Canvas -->
             <div class="flex justify-center">
                 <div id="floor-plan-canvas" class="bg-white border-2 border-gray-300 relative h-[3000px] w-[4000px] mx-auto overflow-auto" style="position: relative; padding: 200px;">
                    <!-- Grid lines -->
                    <div class="absolute inset-0 grid-pattern"></div>
                    
                    <!-- Loading indicator -->
                    <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-20">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading floor plan...</p>
                        </div>
                    </div>
                    
                     <!-- Floor plan items will be loaded here -->
                                          <div id="canvas-items" class="relative z-10 w-full h-full" style="position: relative; width: 100%; height: 100%; min-height: 500px;"></div>
                     
                </div>
            </div>
        </div>

        <!-- Selection Summary -->
        <div class="bg-white rounded-lg shadow-xl p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Your Selection</h3>
            <div id="selection-summary" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-800 font-medium">₱{{ number_format($bookingAmount, 0) }}</p>
                        <p id="selected-seat" class="text-gray-600">No seat selected</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ $bookingBackRoute }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                Back
            </a>
            <button id="confirm-booking" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Confirm Booking
            </button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[99999] hidden p-4">
        <div class="pm-modal-card">
            <div class="pm-modal-header">
                <div>
                    <h2>Add Payment Method</h2>
                    <p>Choose your preferred way to pay securely.</p>
                </div>
                <button id="payment-modal-close" type="button" aria-label="Close payment modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="pm-payment-options">
                <button id="method-paypal" type="button" class="pm-option-btn">PayPal</button>
                <button id="method-applepay" type="button" class="pm-option-btn">Apple Pay</button>
                <button id="method-gcash" type="button" class="pm-option-btn">GCash</button>
            </div>

            <div class="pm-separator">
                <hr class="line">
                <p>or pay using credit card</p>
                <hr class="line">
            </div>

            <button id="method-card" type="button" class="pm-card-pill">Credit Card</button>

            <div class="pm-credit-form">
                <div class="pm-input-group">
                    <label>Card holder full name</label>
                    <input type="text" placeholder="Enter your full name" readonly>
                </div>
                <div class="pm-input-group">
                    <label>Card Number</label>
                    <input type="text" placeholder="0000 0000 0000 0000" readonly>
                </div>
                <div class="pm-split">
                    <div class="pm-input-group">
                        <label>Expiry Date</label>
                        <input type="text" placeholder="01/30" readonly>
                    </div>
                    <div class="pm-input-group">
                        <label>CVV</label>
                        <input type="text" placeholder="CVV" readonly>
                    </div>
                </div>
            </div>

            <input type="hidden" id="payment-method-input" value="card">

            <button id="payment-checkout-btn" class="pm-checkout-btn" type="button" data-amount="{{ number_format($bookingAmount, 2, '.', '') }}">Checkout - Php {{ number_format($bookingAmount, 2) }}</button>
        </div>
    </div>
</div>

<style>
.nav-link {
    @apply text-gray-700 hover:text-teal-600 font-medium transition-colors;
}

.nav-link.active {
    @apply text-teal-600 border-b-2 border-teal-600 pb-1;
}

.admin-button {
    @apply bg-teal-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-teal-700 transition-colors;
}

.grid-pattern {
    background-image: 
        linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

.canvas-item {
    position: absolute;
    cursor: pointer;
    user-select: none;
    z-index: 20;
    border: 2px solid #333;
    transition: all 0.2s ease;
    min-width: 20px;
    min-height: 20px;
    box-sizing: border-box;
}

#floor-plan-canvas {
    position: relative;
    /* Temporarily remove overflow hidden to see if items are being cut off */
    /* overflow: hidden; */
}

#canvas-items {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 600px;
    min-width: 900px;
}

.canvas-item:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.canvas-item.selected {
    outline: 3px solid #10B981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
}

.canvas-item.booked {
    background-color: #EF4444 !important;
    cursor: not-allowed;
    opacity: 0.7;
}

.canvas-item.available {
    /* Remove the forced green color to allow individual item colors */
}

.canvas-item-label {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    color: #374151;
    font-weight: bold;
    text-align: center;
    white-space: nowrap;
}

.pm-modal-card {
    position: relative;
    z-index: 100000;
    width: 100%;
    max-width: 460px;
    background: #ffffff;
    opacity: 1;
    border-radius: 24px;
    box-shadow: 0 32px 80px rgba(0, 0, 0, 0.22);
    padding: 20px;
}

#payment-modal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 99999 !important;
    background: rgba(0, 0, 0, 0.82) !important;
}

.pm-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 14px;
}

.pm-modal-header h2 {
    font-size: 1.6rem;
    font-weight: 800;
    color: #1f2937;
}

.pm-modal-header p {
    color: #6b7280;
    font-size: 0.9rem;
}

.pm-modal-header button {
    color: #9ca3af;
}

.pm-payment-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 16px;
}

.pm-option-btn {
    height: 46px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f3f4f6;
    font-weight: 700;
    color: #4b5563;
}

.pm-option-btn.active {
    border-color: #2563eb;
    background: #eff6ff;
    color: #1d4ed8;
}

.pm-separator {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 10px;
    align-items: center;
    margin-bottom: 14px;
}

.pm-separator p {
    font-size: 11px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
}

.pm-separator .line {
    border: 0;
    height: 1px;
    background-color: #e5e7eb;
}

.pm-card-pill {
    width: 100%;
    height: 42px;
    margin-bottom: 14px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #374151;
    font-weight: 700;
}

.pm-card-pill.active {
    border-color: #2563eb;
    background: #eff6ff;
    color: #1d4ed8;
}

.pm-credit-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.pm-input-group label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 4px;
}

.pm-input-group input {
    width: 100%;
    height: 40px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #f3f4f6;
    padding: 0 12px;
    color: #374151;
}

.pm-split {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 10px;
}

.pm-checkout-btn {
    width: 100%;
    margin-top: 16px;
    height: 50px;
    border-radius: 12px;
    border: 0;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(180deg, #333 0%, #111 100%);
}
</style>

<script>
// Load floor plan from hub owner's database
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the canvas container
    const canvasItems = document.getElementById('canvas-items');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    
    if (!canvasItems) {
        console.error('Canvas items container not found!');
        return;
    }
    
    // Hide loading indicator immediately
    if (loadingIndicator) {
        loadingIndicator.style.display = 'none';
    }
    
    // Load floor plan from database
    loadFloorPlanFromDatabase();
});

    // Load floor plan from database
    function loadFloorPlanFromDatabase() {
        const canvasItems = document.getElementById('canvas-items');
        
        
        // Check if we have floor plan data from the controller
        @if($floorPlan && $floorPlan->layout_data)
            const floorPlanData = @json($floorPlan->layout_data);
            
            if (floorPlanData && floorPlanData.length > 0) {
                createFloorPlanItems(floorPlanData);
            } else {
                createDefaultFloorPlan();
            }
        @else
            createDefaultFloorPlan();
        @endif
    }

// Create floor plan items from database data
function createFloorPlanItems(items) {
    const canvasItems = document.getElementById('canvas-items');
    
    // Clear existing items
    canvasItems.innerHTML = '';
    
    
    // Get booking statuses from the controller
    const bookingStatuses = @json($bookingStatuses ?? []);
    
    items.forEach((item, index) => {
        // Show ALL items from the floor plan
        const newItem = document.createElement('div');
        newItem.className = 'canvas-item available';
        newItem.dataset.id = item.id;
        newItem.dataset.seatNumber = item.label || `Seat ${item.id}`;
        newItem.dataset.shape = item.shape;
        
        // Set styles based on shape type
        const shapeConfig = getShapeConfig(item.shape);
        
        newItem.style.position = 'absolute';
        // Use the exact original positions from the database
        newItem.style.left = item.x + 'px';
        newItem.style.top = item.y + 'px';
        
        
        // ALWAYS use the dimensions from the database to preserve exact orientation
        if (item.width && item.height) {
            newItem.style.width = item.width + 'px';
            newItem.style.height = item.height + 'px';
        } else {
            // Fallback to shape config only if no dimensions in database
            newItem.style.width = shapeConfig.width + 'px';
            newItem.style.height = shapeConfig.height + 'px';
        }
        
        // Apply color coding based on booking status for chairs only
        if (item.shape === 'chair') {
            // Initially set chairs as available (will be updated when date/time is selected)
            newItem.style.backgroundColor = '#10B981'; // Green for available
            newItem.classList.add('available');
        } else {
            // For non-chair items (desks, tables, etc.), always use their original colors
            if (item.backgroundColor) {
                newItem.style.backgroundColor = item.backgroundColor;
            } else {
                newItem.style.backgroundColor = shapeConfig.bg;
            }
        }
        
        newItem.style.border = '2px solid #333';
        newItem.style.cursor = 'pointer';
        newItem.style.zIndex = '10';
        newItem.style.display = 'flex';
        newItem.style.alignItems = 'center';
        newItem.style.justifyContent = 'center';
        newItem.style.color = 'white';
        newItem.style.fontSize = '10px';
        newItem.style.fontWeight = 'bold';
        newItem.style.textAlign = 'center';
        
        // Apply rotation if the item has rotation data
        if (item.rotation) {
            newItem.style.transform = `rotate(${item.rotation}deg)`;
        }
        
        // Add label text - use the proper label from database
        newItem.textContent = item.label || `Seat ${item.id}`;
        
        // Add data-id attribute for booking status lookup
        newItem.setAttribute('data-id', item.id);
        
        // Add click handler for chairs only (chairs are the only bookable items)
        if (item.shape === 'chair') {
            const bookingStatus = bookingStatuses[item.id] || 'available';
            
            // Apply initial color based on booking status
            switch (bookingStatus) {
                case 'available':
                    newItem.style.backgroundColor = '#10B981'; // Green
                    newItem.classList.add('available');
                    newItem.addEventListener('click', function() {
                        selectSeat(newItem);
                    });
                    newItem.style.cursor = 'pointer';
                    newItem.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                    newItem.title = 'Click to book this chair';
                    break;
                case 'confirmed':
                    newItem.style.backgroundColor = '#DC2626'; // Dark red - very distinct from orange
                    newItem.classList.add('booked', 'confirmed');
                    newItem.style.cursor = 'not-allowed';
                    newItem.style.opacity = '0.7';
                    newItem.title = 'This chair is confirmed';
                    break;
                case 'pending':
                    newItem.style.backgroundColor = '#FFA500'; // Bright orange - very distinct from red
                    newItem.classList.add('booked', 'pending');
                    newItem.style.cursor = 'not-allowed';
                    newItem.style.opacity = '0.7';
                    newItem.title = 'This chair is pending';
                    break;
                default:
                    newItem.style.backgroundColor = '#9CA3AF'; // Default chair color
                    newItem.classList.add('available');
                    newItem.addEventListener('click', function() {
                        selectSeat(newItem);
                    });
                    newItem.style.cursor = 'pointer';
                    newItem.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                    newItem.title = 'Click to book this chair';
                    break;
                }
            } else {
                // For non-chair items, use original colors and make them non-clickable
                newItem.style.cursor = 'default';
                newItem.style.opacity = '1';
                newItem.title = '';
                
                // Use original colors based on shape
                switch (item.shape) {
                    case 'desk':
                        newItem.style.backgroundColor = '#8B4513'; // Brown
                        break;
                    case 'table':
                        newItem.style.backgroundColor = '#A0522D'; // Brown
                        break;
                    case 'sofa':
                        newItem.style.backgroundColor = '#FBBF24'; // Yellow
                        break;
                    case 'wall':
                        newItem.style.backgroundColor = '#000000'; // Black
                        break;
                    case 'window':
                        newItem.style.backgroundColor = '#BFDBFE'; // Light blue
                        break;
                    case 'toilet':
                        newItem.style.backgroundColor = '#FFFFFF'; // White
                        break;
                    default:
                        newItem.style.backgroundColor = '#6B7280'; // Gray
                        break;
                }
            }
        
        // Add to canvas
        canvasItems.appendChild(newItem);
        
        
        
    });
    
    
    // Initialize seat selection functionality
    initializeSeatSelection();
}

// Create default floor plan if no database data
function createDefaultFloorPlan() {
    const canvasItems = document.getElementById('canvas-items');
    
    // Clear existing items
    canvasItems.innerHTML = '';
    
    // Default items
    const defaultItems = [
        { shape: 'desk', x: 50, y: 50, id: 1, width: 80, height: 60, label: 'Desk 1' },
        { shape: 'desk', x: 150, y: 50, id: 2, width: 80, height: 60, label: 'Desk 2' },
        { shape: 'desk', x: 250, y: 50, id: 3, width: 80, height: 60, label: 'Desk 3' },
        { shape: 'desk', x: 350, y: 50, id: 4, width: 80, height: 60, label: 'Desk 4' },
        { shape: 'chair', x: 70, y: 70, id: 5, width: 40, height: 40, label: 'Chair 1' },
        { shape: 'chair', x: 170, y: 70, id: 6, width: 40, height: 40, label: 'Chair 2' },
        { shape: 'chair', x: 270, y: 70, id: 7, width: 40, height: 40, label: 'Chair 3' },
        { shape: 'chair', x: 370, y: 70, id: 8, width: 40, height: 40, label: 'Chair 4' }
    ];
    
    createFloorPlanItems(defaultItems);
}

// Get shape configuration
function getShapeConfig(shapeType) {
    const shapes = {
        desk: { width: 80, height: 60, bg: '#8B4513', label: 'Desk' },
        chair: { width: 40, height: 40, bg: '#9CA3AF', label: 'Chair' },
        table: { width: 80, height: 80, bg: '#A0522D', label: 'Table' },
        sofa: { width: 120, height: 60, bg: '#FBBF24', label: 'Sofa' },
        wall: { width: 80, height: 20, bg: '#000000', label: 'Wall' },
        door: { width: 60, height: 40, bg: '#D2691E', label: 'Door' },
        window: { width: 60, height: 30, bg: '#BFDBFE', label: 'Window' },
        toilet: { width: 40, height: 50, bg: '#FFFFFF', label: 'Toilet' }
    };
    
    return shapes[shapeType] || { width: 60, height: 40, bg: '#6B7280', label: 'Item' };
}

// Center all items within the canvas
function centerItemsInCanvas() {
    const canvasItems = document.getElementById('canvas-items');
    const canvas = document.getElementById('floor-plan-canvas');
    
    if (!canvasItems || !canvas) return;
    
    // Get all items
    const items = canvasItems.querySelectorAll('.canvas-item');
    if (items.length === 0) return;
    
    // Find the bounds of all items
    let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
    
    items.forEach(item => {
        const rect = item.getBoundingClientRect();
        const canvasRect = canvas.getBoundingClientRect();
        
        // Convert to relative coordinates
        const relativeX = rect.left - canvasRect.left;
        const relativeY = rect.top - canvasRect.top;
        
        minX = Math.min(minX, relativeX);
        minY = Math.min(minY, relativeY);
        maxX = Math.max(maxX, relativeX + rect.width);
        maxY = Math.max(maxY, relativeY + rect.height);
    });
    
    // Calculate center offset
    const canvasWidth = canvas.offsetWidth;
    const canvasHeight = canvas.offsetHeight;
    const itemsWidth = maxX - minX;
    const itemsHeight = maxY - minY;
    
    const offsetX = (canvasWidth - itemsWidth) / 2 - minX;
    const offsetY = (canvasHeight - itemsHeight) / 2 - minY;
    
    // Apply offset to center all items
    items.forEach(item => {
        const currentLeft = parseFloat(item.style.left) || 0;
        const currentTop = parseFloat(item.style.top) || 0;
        
        item.style.left = (currentLeft + offsetX) + 'px';
        item.style.top = (currentTop + offsetY) + 'px';
    });
    
}

// Seat selection functionality
function initializeSeatSelection() {
    const selectedSeatElement = document.getElementById('selected-seat');
    const confirmBookingBtn = document.getElementById('confirm-booking');
    let selectedSeat = null;
    
    // Select a seat
    function selectSeat(item) {
        
        // Remove previous selection
        document.querySelectorAll('.canvas-item').forEach(i => i.classList.remove('selected'));
        
        // Select new item
        item.classList.add('selected');
        selectedSeat = item;
        
        // Update selection summary
        if (selectedSeatElement) {
            selectedSeatElement.textContent = item.dataset.seatNumber;
        }
        
        // Enable confirm button
        if (confirmBookingBtn) {
            confirmBookingBtn.disabled = false;
        }
    }
    
    // Make selectSeat function globally available
    window.selectSeat = selectSeat;
    
    // Confirm booking button handler
    if (confirmBookingBtn) {
        confirmBookingBtn.addEventListener('click', function() {
            if (!selectedSeat) {
                alert('Please select a seat first.');
                return;
            }
            
            const bookingDate = document.getElementById('booking-date')?.value;
            const bookingTime = document.getElementById('booking-time')?.value;
            const bookingEndTime = document.getElementById('booking-end-time')?.value;
            
            
            if (!bookingDate || !bookingTime) {
                alert('Please select both date and time start.');
                return;
            }
            
            showPaymentModal();
        });
    }
    
    // Set default date to today (using server date to avoid timezone issues)
    const today = '{{ date("Y-m-d") }}';
    const dateInput = document.getElementById('booking-date');
    if (dateInput) {
        dateInput.value = today;
    }

    // Update chair colors on page load
    setTimeout(() => {
        updateChairColors();
    }, 1000);

    // Payment modal functions
    function showPaymentModal() {
        document.getElementById('payment-modal').classList.remove('hidden');
    }

    function hidePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
    }

    const paymentModalClose = document.getElementById('payment-modal-close');
    if (paymentModalClose) {
        paymentModalClose.addEventListener('click', hidePaymentModal);
    }

    const paymentModal = document.getElementById('payment-modal');
    if (paymentModal) {
        paymentModal.addEventListener('click', function(event) {
            if (event.target === paymentModal) {
                hidePaymentModal();
            }
        });
    }

    const methodCardBtn = document.getElementById('method-card');
    const methodGcashBtn = document.getElementById('method-gcash');
    const methodPaypalBtn = document.getElementById('method-paypal');
    const methodApplePayBtn = document.getElementById('method-applepay');
    const paymentMethodInput = document.getElementById('payment-method-input');

    function setPaymentMethod(method) {
        if (!paymentMethodInput) {
            return;
        }

        paymentMethodInput.value = method;
        methodCardBtn?.classList.remove('active');
        methodGcashBtn?.classList.remove('active');
        methodPaypalBtn?.classList.remove('active');
        methodApplePayBtn?.classList.remove('active');

        if (method === 'card') {
            methodCardBtn?.classList.add('active');
        } else if (method === 'gcash') {
            methodGcashBtn?.classList.add('active');
        }
    }

    methodCardBtn?.addEventListener('click', function() {
        setPaymentMethod('card');
    });

    methodGcashBtn?.addEventListener('click', function() {
        setPaymentMethod('gcash');
    });

    methodPaypalBtn?.addEventListener('click', function() {
        methodPaypalBtn.classList.add('active');
        alert('PayPal is not available yet. Please use Card or GCash.');
        setPaymentMethod('card');
    });

    methodApplePayBtn?.addEventListener('click', function() {
        methodApplePayBtn.classList.add('active');
        alert('Apple Pay is not available yet. Please use Card or GCash.');
        setPaymentMethod('card');
    });

    setPaymentMethod('card');

    const paymentCheckoutBtn = document.getElementById('payment-checkout-btn');
    const defaultCheckoutBtnLabel = paymentCheckoutBtn ? paymentCheckoutBtn.textContent.trim() : '';
    if (paymentCheckoutBtn) {
        paymentCheckoutBtn.addEventListener('click', function() {
            if (!selectedSeat) {
                alert('Please select a seat first.');
                return;
            }

            const bookingDate = document.getElementById('booking-date')?.value;
            const bookingTime = document.getElementById('booking-time')?.value;
            const bookingEndTime = document.getElementById('booking-end-time')?.value;

            if (!bookingDate || !bookingTime) {
                alert('Please select both date and time start.');
                return;
            }

            const selectedPaymentMethod = paymentMethodInput?.value || 'card';
            const bookingData = {
                service_type: '{{ $serviceType }}',
                seat_id: selectedSeat.dataset.id,
                seat_label: selectedSeat.dataset.seatNumber,
                booking_date: bookingDate,
                booking_time: bookingTime,
                end_time: bookingEndTime || null,
                payment_method: selectedPaymentMethod,
                _token: '{{ csrf_token() }}'
            };

            paymentCheckoutBtn.disabled = true;
            paymentCheckoutBtn.textContent = 'Processing...';

            fetch('{{ route("services.create-booking-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(bookingData)
            })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    return response.json();
                }

                const text = await response.text();
                return {
                    success: false,
                    message: `Server error (${response.status}).`,
                    details: text ? text.substring(0, 180) : 'No response body',
                };
            })
            .then(data => {
                if (data.success && data.checkout_url) {
                    window.location.href = data.checkout_url;
                    return;
                }

                paymentCheckoutBtn.disabled = false;
                paymentCheckoutBtn.textContent = defaultCheckoutBtnLabel;
                const detailedError = data?.details?.errors?.[0]?.detail || data?.error || data?.details;
                alert('Unable to start payment: ' + (detailedError || data.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Error:', error);
                paymentCheckoutBtn.disabled = false;
                paymentCheckoutBtn.textContent = defaultCheckoutBtnLabel;
                alert('Error starting payment. Please try again.');
            });
        });
    }

    // Add event listeners for date and time changes
    if (dateInput) {
        dateInput.addEventListener('change', updateChairColors);
    }
    
    const timeSelect = document.getElementById('booking-time');
    if (timeSelect) {
        timeSelect.addEventListener('change', updateChairColors);
    }
    
    const endTimeSelect = document.getElementById('booking-end-time');
    if (endTimeSelect) {
        endTimeSelect.addEventListener('change', updateChairColors);
    }

    // Function to update chair colors based on selected date and time
    function updateChairColors() {
        const selectedDate = dateInput?.value;
        const selectedTime = timeSelect?.value;
        
        if (!selectedDate || !selectedTime) {
            return; // Don't update if both date and time aren't selected
        }
        
        
        // Fetch booking statuses for the selected date and time
        fetch('{{ route("services.check-booking-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                date: selectedDate,
                time: selectedTime,
                service_type: '{{ $serviceType }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                applyBookingStatuses(data.bookingStatuses);
            }
        })
        .catch(error => {
            console.error('Error checking booking status:', error);
        });
    }

    // Function to apply booking statuses to chairs only
    function applyBookingStatuses(bookingStatuses) {
        // Only apply booking statuses to chairs, not other items
        const allItems = document.querySelectorAll('.canvas-item[data-id]');
        
        allItems.forEach(item => {
            const itemId = item.dataset.id;
            const itemShape = item.dataset.shape;
            
            // Only apply booking statuses to chairs
            if (itemShape === 'chair') {
                const status = bookingStatuses[itemId];
                
                // Remove existing status classes
                item.classList.remove('available', 'booked', 'confirmed', 'pending');
                
                // Apply new status
                switch (status) {
                    case 'available':
                        item.style.backgroundColor = '#10B981'; // Green
                        item.classList.add('available');
                        item.style.cursor = 'pointer';
                        item.style.opacity = '1';
                        item.title = 'Click to book this chair';
                        break;
                    case 'confirmed':
                        item.style.backgroundColor = '#DC2626'; // Dark red - very distinct from orange
                        item.classList.add('booked', 'confirmed');
                        item.style.cursor = 'not-allowed';
                        item.style.opacity = '0.7';
                        item.title = 'This chair is confirmed';
                        break;
                    case 'pending':
                        item.style.backgroundColor = '#FFA500'; // Bright orange - very distinct from red
                        item.classList.add('booked', 'pending');
                        item.style.cursor = 'not-allowed';
                        item.style.opacity = '0.7';
                        item.title = 'This chair is pending';
                        break;
                    default:
                        // Default chair color if no booking status
                        item.style.backgroundColor = '#9CA3AF'; // Default chair color
                        item.classList.add('available');
                        item.style.cursor = 'pointer';
                        item.style.opacity = '1';
                        item.title = 'Click to book this chair';
                        break;
                }
            } else {
                // For non-chair items, restore original colors and make them non-clickable
                item.style.cursor = 'default';
                item.style.opacity = '1';
                item.title = '';
                
                // Restore original colors based on shape
                switch (itemShape) {
                    case 'desk':
                        item.style.backgroundColor = '#8B4513'; // Brown
                        break;
                    case 'table':
                        item.style.backgroundColor = '#A0522D'; // Brown
                        break;
                    case 'sofa':
                        item.style.backgroundColor = '#FBBF24'; // Yellow
                        break;
                    case 'wall':
                        item.style.backgroundColor = '#000000'; // Black
                        break;
                    case 'window':
                        item.style.backgroundColor = '#BFDBFE'; // Light blue
                        break;
                    case 'toilet':
                        item.style.backgroundColor = '#FFFFFF'; // White
                        break;
                    default:
                        item.style.backgroundColor = '#6B7280'; // Gray
                        break;
                }
            }
        });
    }
}
</script>
@endsection
