@extends('layouts.app')

@section('content')
@php
    $hourlyRate = $hourlyRate ?? 150;
    $bookingBackRoute = $bookingBackRoute ?? route('services.booking');
    $floorPlanFloors = $floorPlanFloors ?? [];
    $selectedFloorId = $selectedFloorId ?? ($floorPlanFloors[0]['id'] ?? 1);
    $layoutItems = $layoutItems ?? [];
@endphp
<div class="min-h-screen bg-gray-800">
    @include('partials.dashboard-navbar', ['active' => 'services'])

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
        <div class="flex items-center justify-between">
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
        </div>

        <!-- Floor Plan Container -->
        <div class="bg-white rounded-lg shadow-xl p-6 mb-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Floor Plan</h2>
                <p class="text-gray-600">Click on a <strong>chair</strong> to select it for booking</p>
                @if(count($floorPlanFloors) > 1)
                    <div id="customer-floor-tabs" class="mt-4 flex flex-wrap justify-center gap-2">
                        @foreach($floorPlanFloors as $floor)
                            <button type="button"
                                    class="customer-floor-tab px-4 py-2 rounded-full text-sm font-semibold border-2 transition-colors {{ (int) $selectedFloorId === (int) $floor['id'] ? 'bg-teal-600 border-teal-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:border-teal-500' }}"
                                    data-floor-id="{{ $floor['id'] }}">
                                {{ $floor['name'] }}
                            </button>
                        @endforeach
                    </div>
                @endif
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
                        <p id="selection-price-display" class="text-gray-800 font-medium">₱{{ number_format($hourlyRate, 0) }}</p>
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
                    <h2>Pay with GCash</h2>
                    <p>Complete your booking using GCash secure checkout.</p>
                </div>
                <button id="payment-modal-close" type="button" aria-label="Close payment modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="pm-gcash-panel" role="region" aria-label="GCash payment">
                <p class="pm-gcash-title">GCash</p>
                <p class="pm-gcash-desc">After you tap checkout, you&rsquo;ll be redirected to finish payment with GCash.</p>
            </div>

            <input type="hidden" id="payment-method-input" value="gcash">

            <button id="payment-checkout-btn" class="pm-checkout-btn" type="button" data-amount="{{ number_format($hourlyRate, 2, '.', '') }}">Checkout - Php {{ number_format($hourlyRate, 2) }}</button>
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
    transition: box-shadow 0.2s ease, filter 0.2s ease;
    min-width: 20px;
    min-height: 20px;
    box-sizing: border-box;
    overflow: visible;
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
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    filter: brightness(1.03);
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

/* Label under furniture (same placement as hub editor; readable on white grid) */
.floor-plan-item-label {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    font-weight: bold;
    color: #111827;
    text-shadow: 0 1px 0 #fff;
    white-space: nowrap;
    pointer-events: none;
    z-index: 5;
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

.pm-gcash-panel {
    padding: 1.25rem 1rem;
    border-radius: 16px;
    border: 2px solid #3b82f6;
    background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 100%);
    text-align: center;
    margin-bottom: 4px;
}

.pm-gcash-title {
    margin: 0 0 0.35rem;
    font-size: 1.35rem;
    font-weight: 800;
    color: #1d4ed8;
    letter-spacing: -0.02em;
}

.pm-gcash-desc {
    margin: 0;
    font-size: 0.9rem;
    line-height: 1.45;
    color: #64748b;
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
window.__bookingHourlyRatePhp = {{ json_encode((float) $hourlyRate) }};
window.__serverTodayYmd = @json(now()->format('Y-m-d'));

/**
 * When the selected date is today, disable time options at or before the current clock
 * (matches server rules for new bookings).
 */
function refreshTimeOptionsForPastDay() {
    const dateInput = document.getElementById('booking-date');
    const todayYmd = window.__serverTodayYmd;
    const selected = dateInput?.value;
    const startSelect = document.getElementById('booking-time');
    const endSelect = document.getElementById('booking-end-time');
    const now = new Date();
    const isToday = !!(selected && todayYmd && selected === todayYmd);

    function slotOnSelectedDate(hm) {
        if (!selected || !hm) return null;
        const hms = normalizeTimeToHms(hm);
        return localDateTimeFromYmdAndHms(selected, hms);
    }

    if (startSelect) {
        startSelect.querySelectorAll('option[value]').forEach((opt) => {
            const v = opt.value;
            if (!v) {
                opt.disabled = false;
                return;
            }
            if (!isToday) {
                opt.disabled = false;
                return;
            }
            const slot = slotOnSelectedDate(v);
            opt.disabled = !!(slot && !isNaN(slot.getTime()) && slot <= now);
        });
        const so = startSelect.options[startSelect.selectedIndex];
        if (startSelect.value && so && so.disabled) {
            startSelect.value = '';
        }
    }

    let startSlot = null;
    if (startSelect?.value) {
        const hms = normalizeTimeToHms(startSelect.value);
        startSlot = localDateTimeFromYmdAndHms(selected, hms);
    }

    if (endSelect) {
        endSelect.querySelectorAll('option[value]').forEach((opt) => {
            const v = opt.value;
            if (!v) {
                opt.disabled = false;
                return;
            }
            if (!isToday) {
                opt.disabled = false;
                return;
            }
            const endSlot = slotOnSelectedDate(v);
            if (!endSlot || isNaN(endSlot.getTime())) {
                opt.disabled = false;
                return;
            }
            let dis = endSlot <= now;
            if (!dis && startSlot && !isNaN(startSlot.getTime()) && endSlot <= startSlot) {
                dis = true;
            }
            opt.disabled = dis;
        });
        const eo = endSelect.options[endSelect.selectedIndex];
        if (endSelect.value && eo && eo.disabled) {
            endSelect.value = '';
        }
    }
}

/**
 * Match ServiceController::queryOccupancyInterval (default +1h when end missing or not after start).
 */
function normalizeTimeToHms(timeVal) {
    const s = String(timeVal || '').trim();
    if (!s) return '';
    const parts = s.split(':');
    const h = String(Math.max(0, Math.min(23, parseInt(parts[0], 10) || 0))).padStart(2, '0');
    const m = String(Math.max(0, Math.min(59, parseInt(parts[1] ?? '0', 10) || 0))).padStart(2, '0');
    const sec = String(Math.max(0, Math.min(59, parseInt(parts[2] ?? '0', 10) || 0))).padStart(2, '0');
    return h + ':' + m + ':' + sec;
}

function localDateTimeFromYmdAndHms(dateYmd, hms) {
    const dp = dateYmd.split('-').map((x) => parseInt(x, 10));
    const tp = hms.split(':').map((x) => parseInt(x, 10));
    if (dp.length < 3 || !Number.isFinite(dp[0])) return null;
    return new Date(dp[0], dp[1] - 1, dp[2], tp[0] || 0, tp[1] || 0, tp[2] || 0, 0);
}

function bookingWindowHoursAndTotal() {
    const hourly = Number(window.__bookingHourlyRatePhp) || 0;
    const dateStr = document.getElementById('booking-date')?.value;
    const startRaw = document.getElementById('booking-time')?.value;
    const endRaw = document.getElementById('booking-end-time')?.value;

    if (!hourly) {
        return { hours: 0, total: 0 };
    }

    if (!dateStr || !startRaw) {
        const t = Math.round(hourly * 100) / 100;
        return { hours: 1, total: t };
    }

    const startHms = normalizeTimeToHms(startRaw);
    const startDt = localDateTimeFromYmdAndHms(dateStr, startHms);
    if (!startDt || isNaN(startDt.getTime())) {
        const t = Math.round(hourly * 100) / 100;
        return { hours: 1, total: t };
    }

    let endDt = null;
    if (endRaw) {
        const endHms = normalizeTimeToHms(endRaw);
        endDt = localDateTimeFromYmdAndHms(dateStr, endHms);
    }

    if (!endDt || isNaN(endDt.getTime()) || endDt <= startDt) {
        endDt = new Date(startDt.getTime());
        endDt.setHours(endDt.getHours() + 1);
    }

    const ms = endDt - startDt;
    const hours = Math.max(ms / 3600000, 1 / 60);
    const total = Math.round(hours * hourly * 100) / 100;
    return { hours, total };
}

function updateBookingPriceUi() {
    const { total } = bookingWindowHoursAndTotal();
    const priceEl = document.getElementById('selection-price-display');
    if (priceEl) {
        const rounded = Math.round(total);
        priceEl.textContent = '₱' + rounded.toLocaleString('en-PH');
    }
    const btn = document.getElementById('payment-checkout-btn');
    if (btn) {
        const formatted = total.toFixed(2);
        btn.setAttribute('data-amount', formatted);
        btn.textContent =
            'Checkout - Php ' +
            Number(total).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}

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

    window.__floorPlanFloors = @json($floorPlanFloors);
    window.__selectedFloorId = {{ (int) $selectedFloorId }};

    function loadCustomerFloor(floorId) {
        const floor = (window.__floorPlanFloors || []).find(function (f) {
            return parseInt(f.id, 10) === parseInt(floorId, 10);
        });
        const items = floor && Array.isArray(floor.items) ? floor.items : [];
        if (items.length > 0) {
            createFloorPlanItems(items);
        } else {
            createDefaultFloorPlan();
        }
        window.__selectedFloorId = parseInt(floorId, 10);
        document.querySelectorAll('.customer-floor-tab').forEach(function (btn) {
            const active = parseInt(btn.dataset.floorId, 10) === parseInt(floorId, 10);
            btn.classList.toggle('bg-teal-600', active);
            btn.classList.toggle('border-teal-600', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('border-gray-300', !active);
            btn.classList.toggle('text-gray-700', !active);
        });
        if (typeof updateChairColors === 'function') {
            updateChairColors();
        }
    }

    // Load floor plan from database
    function loadFloorPlanFromDatabase() {
        @if($floorPlan && (count($layoutItems) > 0 || count($floorPlanFloors) > 0))
            if (window.__floorPlanFloors && window.__floorPlanFloors.length > 0) {
                loadCustomerFloor(window.__selectedFloorId);
            } else {
                createDefaultFloorPlan();
            }
        @else
            createDefaultFloorPlan();
        @endif
    }

    document.querySelectorAll('.customer-floor-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            loadCustomerFloor(btn.dataset.floorId);
        });
    });

function bookingStatusForItem(bookingStatuses, id) {
    if (bookingStatuses[id] !== undefined && bookingStatuses[id] !== null) {
        return bookingStatuses[id];
    }
    const key = String(id);
    if (bookingStatuses[key] !== undefined && bookingStatuses[key] !== null) {
        return bookingStatuses[key];
    }
    return 'available';
}

function appendFloorPlanLabel(el, text) {
    const lab = document.createElement('div');
    lab.className = 'floor-plan-item-label';
    lab.textContent = text;
    el.appendChild(lab);
}

function addTableLegs(itemEl) {
    const legs = [
        { left: '6px', top: '0' },
        { right: '6px', top: '0' },
        { left: '6px', bottom: '-14px' },
        { right: '6px', bottom: '-14px' },
    ];
    legs.forEach((pos) => {
        const leg = document.createElement('div');
        const posStr = Object.entries(pos).map(([k, v]) => k + ':' + v).join(';');
        leg.style.cssText = 'position:absolute;width:5px;height:14px;background:#8B4513;border:1px solid #654321;' + posStr;
        itemEl.appendChild(leg);
    });
}

function addSofaDecor(itemEl, sofaBg) {
    const sofaBack = document.createElement('div');
    sofaBack.style.cssText = 'position:absolute;top:-14px;left:0;right:0;height:14px;background-color:' + sofaBg + ';border:2px solid #333;border-radius:8px 8px 0 0';
    itemEl.appendChild(sofaBack);
    const leftArm = document.createElement('div');
    leftArm.style.cssText = 'position:absolute;left:-8px;top:0;width:8px;height:100%;background-color:' + sofaBg + ';border:2px solid #333;border-radius:8px 0 0 8px';
    itemEl.appendChild(leftArm);
    const rightArm = document.createElement('div');
    rightArm.style.cssText = 'position:absolute;right:-8px;top:0;width:8px;height:100%;background-color:' + sofaBg + ';border:2px solid #333;border-radius:0 8px 8px 0';
    itemEl.appendChild(rightArm);
}

/** Booking UI star color (violet), kept in sync with hub floor plan `shapes.star.bg`. */
const FLOOR_PLAN_STAR_VIOLET = '#7C3AED';

/** Match hub-owner floor plan editor visuals (star clip-path, table legs, sofa back/arms). */
function decorateNonChairViewer(el, item) {
    const shape = item.shape;
    const sc = getShapeConfig(shape);
    const fill = item.backgroundColor || sc.bg;

    if (shape === 'star') {
        el.style.backgroundColor = 'transparent';
        el.style.border = 'none';
        el.style.boxSizing = 'border-box';
        // SVG scales with the container (same polygon as hub editor sidebar); clip-path was unreliable at tiny sizes.
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 32 32');
        svg.setAttribute('preserveAspectRatio', 'xMidYMid meet');
        svg.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;display:block;pointer-events:none';
        const poly = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
        poly.setAttribute('points', '16,2 20,12 30,12 22,20 26,30 16,24 6,30 10,20 2,12 12,12');
        poly.setAttribute('fill', FLOOR_PLAN_STAR_VIOLET);
        poly.setAttribute('stroke', '#333');
        poly.setAttribute('stroke-width', '2');
        svg.appendChild(poly);
        el.appendChild(svg);
        return;
    }

    el.style.border = '2px solid #333';

    if (shape === 'table') {
        el.style.backgroundColor = fill;
        addTableLegs(el);
        return;
    }

    if (shape === 'sofa') {
        el.style.backgroundColor = fill;
        addSofaDecor(el, fill);
        return;
    }

    if (shape === 'desk') {
        el.style.backgroundColor = fill;
        return;
    }

    if (shape === 'door') {
        el.style.backgroundColor = fill;
        const handle = document.createElement('div');
        handle.style.cssText = 'position:absolute;right:6px;top:50%;transform:translateY(-50%);width:8px;height:8px;background:#FFD700;border:1px solid #333;border-radius:50%';
        el.appendChild(handle);
        return;
    }

    if (shape === 'window') {
        el.style.border = '3px solid #1F2937';
        el.style.backgroundColor = '#BFDBFE';
        const pane = document.createElement('div');
        pane.style.cssText = 'position:absolute;top:3px;left:3px;right:3px;bottom:3px;background:#E0F2FE;border:1px solid #1F2937';
        el.appendChild(pane);
        return;
    }

    if (shape === 'toilet') {
        el.style.backgroundColor = '#FFFFFF';
        const seat = document.createElement('div');
        seat.style.cssText = 'position:absolute;top:5px;left:5px;right:5px;bottom:14px;background:#FFF;border:2px solid #6B7280;border-radius:50%';
        el.appendChild(seat);
        return;
    }

    if (shape === 'wall') {
        el.style.backgroundColor = '#000000';
        return;
    }

    el.style.backgroundColor = fill;
}

function applyChairSeatStyle(el, status) {
    el.classList.remove('available', 'booked', 'confirmed', 'pending');
    switch (status) {
        case 'available':
            el.style.backgroundColor = '#10B981';
            el.classList.add('available');
            el.onclick = function () { selectSeat(el); };
            el.style.cursor = 'pointer';
            el.style.pointerEvents = 'auto';
            el.style.opacity = '1';
            el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
            el.title = 'Click to book this chair';
            break;
        case 'confirmed':
            el.style.backgroundColor = '#DC2626';
            el.classList.add('booked', 'confirmed');
            el.onclick = null;
            el.style.cursor = 'not-allowed';
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.7';
            el.title = 'This chair is confirmed';
            break;
        case 'pending':
            el.style.backgroundColor = '#DC2626';
            el.classList.add('booked', 'pending');
            el.onclick = null;
            el.style.cursor = 'not-allowed';
            el.style.pointerEvents = 'none';
            el.style.opacity = '0.7';
            el.title = 'This chair is not available';
            break;
        default:
            el.style.backgroundColor = '#10B981';
            el.classList.add('available');
            el.onclick = function () { selectSeat(el); };
            el.style.cursor = 'pointer';
            el.style.pointerEvents = 'auto';
            el.style.opacity = '1';
            el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
            el.title = 'Click to book this chair';
    }
    const back = el.querySelector('.chair-seat-back');
    if (back) {
        back.style.backgroundColor = el.style.backgroundColor;
    }
}

// Create floor plan items from database data
function createFloorPlanItems(items) {
    const canvasItems = document.getElementById('canvas-items');

    canvasItems.innerHTML = '';

    const bookingStatuses = @json($bookingStatuses ?? []);

    items.forEach((item) => {
        const newItem = document.createElement('div');
        newItem.className = 'canvas-item';
        newItem.dataset.id = item.id;
        newItem.dataset.seatNumber = item.label || `Seat ${item.id}`;
        newItem.dataset.shape = item.shape;
        newItem.setAttribute('data-id', item.id);

        const shapeConfig = getShapeConfig(item.shape);

        newItem.style.position = 'absolute';
        newItem.style.left = item.x + 'px';
        newItem.style.top = item.y + 'px';

        let dw = parseFloat(item.width);
        let dh = parseFloat(item.height);
        if (!Number.isFinite(dw) || !Number.isFinite(dh) || dw <= 0 || dh <= 0) {
            dw = shapeConfig.width;
            dh = shapeConfig.height;
        }
        // Saved JSON sometimes has tiny dimensions; stars vanish if box is only a few px.
        if (item.shape === 'star') {
            const starMin = 44;
            dw = Math.max(dw, starMin);
            dh = Math.max(dh, starMin);
        }
        newItem.style.width = dw + 'px';
        newItem.style.height = dh + 'px';

        if (item.rotation) {
            newItem.style.transform = `rotate(${item.rotation}deg)`;
        }

        newItem.style.zIndex = '10';
        newItem.style.display = 'block';
        newItem.style.overflow = 'visible';

        const labelText = item.label || (item.shape === 'chair' ? `Seat ${item.id}` : shapeConfig.label);

        if (item.shape === 'chair') {
            const status = bookingStatusForItem(bookingStatuses, item.id);
            applyChairSeatStyle(newItem, status);

            newItem.style.borderRadius = '50%';
            const w = parseInt(newItem.style.width, 10) || 40;
            const backW = Math.max(14, Math.min(22, Math.round(w * 0.45)));
            const backH = Math.max(6, Math.round(backW * 0.45));

            const chairBack = document.createElement('div');
            chairBack.className = 'chair-seat-back';
            chairBack.style.cssText =
                'position:absolute;top:-' +
                backH +
                'px;left:50%;transform:translateX(-50%);width:' +
                backW +
                'px;height:' +
                backH +
                'px;background-color:' +
                newItem.style.backgroundColor +
                ';border:2px solid #333;border-radius:' +
                backW +
                'px ' +
                backW +
                'px 0 0;pointer-events:none';
            newItem.appendChild(chairBack);

            appendFloorPlanLabel(newItem, labelText);
        } else {
            decorateNonChairViewer(newItem, item);
            newItem.style.cursor = 'default';
            newItem.style.pointerEvents = 'none';
            newItem.title = '';
            appendFloorPlanLabel(newItem, labelText);
        }

        canvasItems.appendChild(newItem);
    });

    initializeSeatSelection();
}

// Create default floor plan if no database data (matches hub-owner editor reference layout; chairs stay green on render)
function createDefaultFloorPlan() {
    const canvasItems = document.getElementById('canvas-items');
    
    // Clear existing items
    canvasItems.innerHTML = '';
    
    const defaultItems = [
        { shape: 'desk', id: 'desk-1', x: 80, y: 60, width: 80, height: 60, label: 'Desk' },
        { shape: 'table', id: 'table-1', x: 360, y: 50, width: 100, height: 100, label: 'Table' },
        { shape: 'chair', id: 1, x: 90, y: 140, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 2, x: 150, y: 140, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 3, x: 330, y: 170, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 4, x: 450, y: 170, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 5, x: 60, y: 280, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 6, x: 120, y: 420, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 7, x: 280, y: 320, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 8, x: 300, y: 500, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 9, x: 580, y: 240, width: 40, height: 40, label: 'Chair' },
        { shape: 'chair', id: 10, x: 640, y: 400, width: 40, height: 40, label: 'Chair' },
        { shape: 'sofa', id: 'sofa-1', x: 220, y: 240, width: 60, height: 120, label: 'Sofa' },
        { shape: 'sofa', id: 'sofa-2', x: 140, y: 480, width: 120, height: 60, label: 'Sofa' },
        { shape: 'sofa', id: 'sofa-3', x: 300, y: 480, width: 120, height: 60, label: 'Sofa' },
        { shape: 'sofa', id: 'sofa-4', x: 200, y: 560, width: 120, height: 60, label: 'Sofa' },
        { shape: 'star', id: 'star-1', x: 40, y: 200, width: 60, height: 60, label: 'Star' },
        { shape: 'star', id: 'star-2', x: 240, y: 420, width: 60, height: 60, label: 'Star' },
        { shape: 'wall', id: 'wall-1', x: 520, y: 200, width: 220, height: 20, label: 'Wall' },
        { shape: 'wall', id: 'wall-2', x: 180, y: 360, width: 120, height: 20, label: 'Wall' },
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
        star: { width: 60, height: 60, bg: '#7C3AED', label: 'Star' },
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
        updateBookingPriceUi();
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

    const paymentMethodInput = document.getElementById('payment-method-input');
    if (paymentMethodInput) {
        paymentMethodInput.value = 'gcash';
    }

    const paymentCheckoutBtn = document.getElementById('payment-checkout-btn');
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

            const selectedPaymentMethod = paymentMethodInput?.value || 'gcash';
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
                updateBookingPriceUi();
                const detailedError = data?.details?.errors?.[0]?.detail || data?.error || data?.details;
                alert('Unable to start payment: ' + (detailedError || data.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Error:', error);
                paymentCheckoutBtn.disabled = false;
                updateBookingPriceUi();
                alert('Error starting payment. Please try again.');
            });
        });
    }

    function onBookingWindowInputsChange() {
        refreshTimeOptionsForPastDay();
        updateBookingPriceUi();
        updateChairColors();
    }

    // Add event listeners for date and time changes
    if (dateInput) {
        dateInput.addEventListener('change', onBookingWindowInputsChange);
        dateInput.addEventListener('input', onBookingWindowInputsChange);
    }
    
    const timeSelect = document.getElementById('booking-time');
    if (timeSelect) {
        timeSelect.addEventListener('change', onBookingWindowInputsChange);
    }
    
    const endTimeSelect = document.getElementById('booking-end-time');
    if (endTimeSelect) {
        endTimeSelect.addEventListener('change', onBookingWindowInputsChange);
    }

    refreshTimeOptionsForPastDay();
    updateBookingPriceUi();

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
                end_time: document.getElementById('booking-end-time')?.value || null,
                service_type: '{{ $serviceType }}',
                floor: window.__selectedFloorId || null
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
                const status = bookingStatusForItem(bookingStatuses, itemId);

                applyChairSeatStyle(item, status);

                // If currently selected chair becomes unavailable, clear selection immediately.
                if (item.classList.contains('selected') && (status === 'confirmed' || status === 'pending')) {
                    item.classList.remove('selected');
                    if (selectedSeatElement) {
                        selectedSeatElement.textContent = 'No seat selected';
                    }
                    selectedSeat = null;
                    if (confirmBookingBtn) {
                        confirmBookingBtn.disabled = true;
                    }
                }
            } else {
                // For non-chair items, restore original colors and make them non-clickable
                item.onclick = null;
                item.style.cursor = 'default';
                item.style.pointerEvents = 'none';
                item.style.opacity = '1';
                item.title = '';
                
                // Restore original colors based on shape (skip star: clip-path child holds color)
                switch (itemShape) {
                    case 'star':
                        break;
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
                    case 'door':
                        item.style.backgroundColor = '#D2691E';
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
