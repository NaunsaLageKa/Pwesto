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
                    <label class="text-white font-medium">Time:</label>
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
                        <p class="text-gray-800 font-medium">₱50</p>
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

    <!-- Booking Confirmation Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-96 mx-4">
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Booking Confirmed!</h3>
                <div class="text-left space-y-2 mb-6 text-base">
                    <p><strong>Seat:</strong> <span id="modal-seat"></span></p>
                    <p><strong>Date:</strong> <span id="modal-date"></span></p>
                    <p><strong>Time:</strong> <span id="modal-time"></span></p>
                    <p><strong>Booking ID:</strong> <span id="modal-booking-id"></span></p>
                </div>
                <button id="modal-ok-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-blue-700 transition-colors">
                    OK
                </button>
            </div>
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
                    // Show modal instead of alert
                    showBookingModal(selectedSeat.dataset.seatNumber, bookingDate, bookingTime, data.booking_id);
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

    // Modal functions
    function showBookingModal(seat, date, time, bookingId) {
        document.getElementById('modal-seat').textContent = seat;
        document.getElementById('modal-date').textContent = date;
        document.getElementById('modal-time').textContent = time;
        document.getElementById('modal-booking-id').textContent = bookingId;
        document.getElementById('booking-modal').classList.remove('hidden');
    }

    function hideBookingModal() {
        document.getElementById('booking-modal').classList.add('hidden');
    }

    // Modal event listeners
    document.getElementById('modal-ok-btn').addEventListener('click', function() {
        hideBookingModal();
        window.location.href = '{{ route("booking-history") }}';
    });

    // Add event listeners for date and time changes
    if (dateInput) {
        dateInput.addEventListener('change', updateChairColors);
    }
    
    const timeSelect = document.getElementById('booking-time');
    if (timeSelect) {
        timeSelect.addEventListener('change', updateChairColors);
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
