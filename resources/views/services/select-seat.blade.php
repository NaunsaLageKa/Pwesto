@extends('layouts.app')

@section('content')
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
                    <a href="#" class="nav-link">About</a>
                    <a href="#" class="nav-link">Location</a>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <img 
                            src="{{ Auth::user()->profile_image ? asset('storage/' . Auth::user()->profile_image) : asset('images/avatar.svg') }}" 
                            alt="Profile" 
                            class="w-10 h-10 rounded-full object-cover border-2 border-gray-200 {{ !Auth::user()->profile_image ? 'bg-gray-100 p-2' : '' }}"
                        >
                    </div>
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
                    {{ ucfirst(str_replace('-', ' ', $serviceType)) }}
                </h1>
                <p class="text-xl text-white">Select Seat</p>
            </div>
            
            <!-- Booking Controls -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-white font-medium">Date:</label>
                    <input type="date" id="booking-date" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-white font-medium">Time:</label>
                    <select id="booking-time" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="12:00">12:00 PM</option>
                        <option value="13:00">1:00 PM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Floor Plan Container -->
        <div class="bg-white rounded-lg shadow-xl p-6 mb-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Floor Plan</h2>
                                 <p class="text-gray-600">Click on a <strong>desk or chair</strong> to select it for booking</p>
            </div>
            
                         <!-- Floor Plan Canvas -->
             <div class="flex justify-center">
                 <div id="floor-plan-canvas" class="bg-white border-2 border-gray-300 relative h-[600px] w-[900px] mx-auto overflow-auto" style="position: relative;">
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
                    
                                         <!-- Room labels -->
                     <div class="absolute top-4 left-4 text-sm text-gray-500">
                         <div>Conference Room</div>
                         <div>Open Area</div>
                         <div>Kitchen</div>
                         <div>Offices</div>
                         <div>Restrooms</div>
                     </div>
                     
                     <!-- Legend -->
                     <div class="absolute top-4 right-4 text-sm text-gray-500 bg-white bg-opacity-90 p-2 rounded border">
                         <div class="font-semibold mb-1">Legend:</div>
                         <div class="flex items-center space-x-2 mb-1">
                             <div class="w-4 h-3 bg-brown-500 border border-gray-300"></div>
                             <span>Desk (Clickable)</span>
                         </div>
                         <div class="flex items-center space-x-2 mb-1">
                             <div class="w-4 h-4 bg-gray-400 border border-gray-300 rounded-full"></div>
                             <span>Chair (Clickable)</span>
                         </div>
                         <div class="flex items-center space-x-2 mb-1">
                             <div class="w-4 h-4 bg-brown-400 border border-gray-300"></div>
                             <span>Table (Not Clickable)</span>
                         </div>
                         <div class="flex items-center space-x-2 mb-1">
                             <div class="w-4 h-2 bg-black border border-gray-300"></div>
                             <span>Wall (Not Clickable)</span>
                         </div>
                     </div>
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
                        <p class="text-gray-800 font-medium">{{ ucfirst(str_replace('-', ' ', $serviceType)) }}</p>
                        <p id="selected-seat" class="text-gray-600">No seat selected</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('services.booking') }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                Back to Services
            </a>
            <button id="confirm-booking" class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Confirm Booking
            </button>
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
</style>

<script>
// Load floor plan from hub owner's database
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, loading floor plan from database...');
    
    // Get the canvas container
    const canvasItems = document.getElementById('canvas-items');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    console.log('Canvas items container:', canvasItems);
    
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
        console.log('Floor plan data from database:', floorPlanData);
        console.log('Floor plan object:', @json($floorPlan));
        
        if (floorPlanData && floorPlanData.length > 0) {
            console.log('Loading floor plan from database...');
            createFloorPlanItems(floorPlanData);
        } else {
            console.log('No valid floor plan data, creating default...');
            createDefaultFloorPlan();
        }
    @else
        console.log('No floor plan from controller, creating default...');
        createDefaultFloorPlan();
    @endif
}

// Create floor plan items from database data
function createFloorPlanItems(items) {
    const canvasItems = document.getElementById('canvas-items');
    
    // Clear existing items
    canvasItems.innerHTML = '';
    
    console.log('Creating', items.length, 'items from database...');
    
    items.forEach((item, index) => {
        // Show ALL items from the floor plan
        console.log('Processing item:', item);
        const newItem = document.createElement('div');
        newItem.className = 'canvas-item available';
        newItem.dataset.id = item.id;
        newItem.dataset.seatNumber = item.label || `Seat ${item.id}`;
        
        // Set styles based on shape type
        const shapeConfig = getShapeConfig(item.shape);
        
        newItem.style.position = 'absolute';
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
        
        // Use the exact background color from the database if available
        if (item.backgroundColor) {
            newItem.style.backgroundColor = item.backgroundColor;
        } else {
            newItem.style.backgroundColor = shapeConfig.bg;
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
        
        // Add click handler for clickable items (desks and chairs only)
        if (item.shape === 'desk' || item.shape === 'chair') {
            newItem.addEventListener('click', function() {
                selectSeat(newItem);
            });
            newItem.style.cursor = 'pointer';
            // Add a subtle indicator that this is clickable
            newItem.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
        } else {
            newItem.style.cursor = 'default';
            // Make non-clickable items slightly transparent to indicate they're not interactive
            newItem.style.opacity = '0.8';
        }
        
        // Add to canvas
        canvasItems.appendChild(newItem);
        console.log(`Added ${item.shape} at position ${item.x}, ${item.y}`);
        
        // Special debugging for walls to see their dimensions
        if (item.shape === 'wall') {
            console.log(`WALL DEBUG - Original dimensions: ${item.width}x${item.height}, Applied dimensions: ${newItem.style.width}x${newItem.style.height}`);
            console.log(`WALL DEBUG - Item data:`, item);
        }
        
        // Debug all items to see their exact data from database
        console.log(`ITEM DEBUG - ID: ${item.id}, Shape: ${item.shape}, Dimensions: ${item.width}x${item.height}, Color: ${item.backgroundColor || 'default'}`);
        console.log(`ITEM DEBUG - Full item data:`, item);
        
        console.log('Item element:', newItem);
        console.log('Item styles:', {
            position: newItem.style.position,
            left: newItem.style.left,
            top: newItem.style.top,
            width: newItem.style.width,
            height: newItem.style.height,
            backgroundColor: newItem.style.backgroundColor,
            display: newItem.style.display,
            zIndex: newItem.style.zIndex
        });
        
        // Debug: Check if item is within canvas bounds
        const canvasRect = document.getElementById('floor-plan-canvas').getBoundingClientRect();
        const itemRect = newItem.getBoundingClientRect();
        console.log(`Canvas bounds: ${canvasRect.width}x${canvasRect.height}`);
        console.log(`Item bounds: ${itemRect.width}x${itemRect.height} at (${itemRect.left}, ${itemRect.top})`);
        console.log(`Item is within canvas: ${itemRect.left >= canvasRect.left && itemRect.top >= canvasRect.top && itemRect.right <= canvasRect.right && itemRect.bottom <= canvasRect.bottom}`);
    });
    
    console.log('Database floor plan loaded with', items.length, 'items');
    
    // Center all items within the canvas
    centerItemsInCanvas();
    
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
    
    console.log('Creating default floor plan with', defaultItems.length, 'items...');
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
        sink: { width: 60, height: 40, bg: '#9CA3AF', label: 'Sink' },
        refrigerator: { width: 60, height: 80, bg: '#FFFFFF', label: 'Fridge' },
        toilet: { width: 40, height: 50, bg: '#FFFFFF', label: 'Toilet' },
        plant: { width: 40, height: 40, bg: '#10B981', label: 'Plant' },
        lamp: { width: 30, height: 50, bg: '#FCD34D', label: 'Lamp' }
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
    
    console.log('Items centered in canvas');
}

// Seat selection functionality
function initializeSeatSelection() {
    const selectedSeatElement = document.getElementById('selected-seat');
    const confirmBookingBtn = document.getElementById('confirm-booking');
    let selectedSeat = null;
    
    // Select a seat
    function selectSeat(item) {
        console.log('Selecting seat:', item.dataset.seatNumber);
        
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
            
            if (!bookingDate || !bookingTime) {
                alert('Please select both date and time.');
                return;
            }
            
            // Create booking data
            const bookingData = {
                service_type: '{{ $serviceType }}',
                seat_id: selectedSeat.dataset.id,
                seat_label: selectedSeat.dataset.seatNumber,
                booking_date: bookingDate,
                booking_time: bookingTime,
                _token: '{{ csrf_token() }}'
            };
            
            console.log('Sending booking data:', bookingData);
            
            // Send booking request to backend
            fetch('{{ route("services.create-booking") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(bookingData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Booking confirmed!\nSeat: ${selectedSeat.dataset.seatNumber}\nDate: ${bookingDate}\nTime: ${bookingTime}\n\nBooking ID: ${data.booking_id}`);
                    // Redirect to booking history
                    window.location.href = '{{ route("booking-history") }}';
                } else {
                    alert('Error creating booking: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating booking. Please try again.');
            });
        });
    }
    
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('booking-date');
    if (dateInput) {
        dateInput.value = today;
    }
}
</script>
@endsection
