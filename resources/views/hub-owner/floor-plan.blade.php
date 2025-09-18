@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-lg">
        <div class="p-4 border-b">
            <h2 class="text-xl font-bold text-gray-800">Floor Plan Editor</h2>
        </div>
        
        <!-- Tools Panel -->
        <div class="p-4">
            <!-- Search Shapes -->
            <div class="mb-4">
                <input type="text" placeholder="Search shapes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Test Button -->
            <div class="mb-4">
                <button id="test-btn" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Test: Add Desk
                </button>
            </div>
            
            <!-- Categories -->
            <div class="space-y-4">
                <!-- Furniture -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Furniture</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="shape-item" data-shape="desk" draggable="true">
                            <div class="w-8 h-6 bg-brown-500 border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">D</div>
                            </div>
                            <span class="text-xs text-gray-600">Desk</span>
                        </div>
                        <div class="shape-item" data-shape="chair" draggable="true">
                            <div class="w-6 h-6 bg-gray-400 border border-gray-300 rounded-full cursor-grab relative">
                                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-3 h-3 bg-gray-400 border border-gray-300 rounded-full"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">C</div>
                            </div>
                            <span class="text-xs text-gray-600">Chair</span>
                        </div>
                        <div class="shape-item" data-shape="table" draggable="true">
                            <div class="w-8 h-8 bg-brown-400 border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute -bottom-1 left-1 w-1 h-1 bg-brown-600"></div>
                                <div class="absolute -bottom-1 right-1 w-1 h-1 bg-brown-600"></div>
                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-brown-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">T</div>
                            </div>
                            <span class="text-xs text-gray-600">Table</span>
                        </div>
                        <div class="shape-item" data-shape="sofa" draggable="true">
                            <div class="w-10 h-4 bg-yellow-400 border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute -top-1 left-0 right-0 h-1 bg-yellow-400 border border-gray-300 rounded-t"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">S</div>
                            </div>
                            <span class="text-xs text-gray-600">Sofa</span>
                        </div>
                    </div>
                </div>
                
                <!-- Walls & Doors -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Walls & Doors</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="shape-item" data-shape="wall" draggable="true">
                            <div class="w-8 h-2 bg-gray-800 border border-gray-300 cursor-grab"></div>
                            <span class="text-xs text-gray-600">Wall</span>
                        </div>
                        <div class="shape-item" data-shape="door" draggable="true">
                            <div class="w-6 h-4 bg-brown-300 border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute right-1 top-1/2 transform -translate-y-1/2 w-1 h-1 bg-yellow-400 rounded-full"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">D</div>
                            </div>
                            <span class="text-xs text-gray-600">Door</span>
                        </div>
                        <div class="shape-item" data-shape="window" draggable="true">
                            <div class="w-6 h-3 bg-blue-200 border-2 border-gray-700 cursor-grab relative">
                                <div class="absolute inset-1 bg-blue-100 border border-gray-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-blue-800 font-bold">W</div>
                            </div>
                            <span class="text-xs text-gray-600">Window</span>
                        </div>
                    </div>
                </div>
                
                <!-- Appliances -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Appliances</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="shape-item" data-shape="sink" draggable="true">
                            <div class="w-6 h-4 bg-gray-300 border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute inset-1 bg-gray-200 border border-gray-400 rounded-full"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-700 font-bold">S</div>
                            </div>
                            <span class="text-xs text-gray-600">Sink</span>
                        </div>
                        <div class="shape-item" data-shape="refrigerator" draggable="true">
                            <div class="w-6 h-8 bg-white border border-gray-300 cursor-grab relative">
                                <div class="absolute right-1 top-1/2 transform -translate-y-1/2 w-1 h-3 bg-gray-400"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-700 font-bold">F</div>
                            </div>
                            <span class="text-xs text-gray-600">Fridge</span>
                        </div>
                        <div class="shape-item" data-shape="toilet" draggable="true">
                            <div class="w-4 h-5 bg-white border border-gray-300 rounded cursor-grab relative">
                                <div class="absolute inset-1 bg-white border border-gray-400 rounded-t-full"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-700 font-bold">T</div>
                            </div>
                            <span class="text-xs text-gray-600">Toilet</span>
                        </div>
                    </div>
                </div>
                
                <!-- Decorations -->
                <div>
                    <h3 class="font-semibold text-gray-700 mb-2">Decorations</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="shape-item" data-shape="plant" draggable="true">
                            <div class="w-4 h-4 bg-green-500 border border-gray-300 rounded-full cursor-grab relative">
                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-1 bg-brown-600 rounded-b"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-white font-bold">P</div>
                            </div>
                            <span class="text-xs text-gray-600">Plant</span>
                        </div>
                        <div class="shape-item" data-shape="lamp" draggable="true">
                            <div class="w-3 h-5 bg-yellow-300 border border-gray-300 cursor-grab relative">
                                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-3 h-1 bg-yellow-200 border border-yellow-400 rounded-t"></div>
                                <div class="absolute inset-0 flex items-center justify-center text-xs text-yellow-800 font-bold">L</div>
                            </div>
                            <span class="text-xs text-gray-600">Lamp</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Canvas Area -->
    <div class="flex-1 flex flex-col">
        <!-- Toolbar -->
        <div class="bg-white border-b px-4 py-2 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('hub-owner.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    ← Back to Dashboard
                </a>
                <button id="save-btn" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium text-lg font-bold">
                    SAVE
                </button>
                <button id="clear-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Clear All
                </button>
            </div>
            
            <div class="flex items-center space-x-2">
                <button id="zoom-out" class="px-3 py-1 bg-gray-200 rounded-md hover:bg-gray-300">-</button>
                <span id="zoom-level" class="px-2">100%</span>
                <button id="zoom-in" class="px-3 py-1 bg-gray-200 rounded-md hover:bg-gray-300">+</button>
            </div>
        </div>
        
        <!-- Canvas Container -->
        <div class="flex-1 bg-gray-100 overflow-auto p-4">
            <div id="floor-plan-canvas" class="bg-white border-2 border-gray-300 relative min-h-[600px] min-w-[800px] mx-auto">
                <!-- Grid lines -->
                <div class="absolute inset-0 grid-pattern"></div>
                
                <!-- Dragged items will be placed here -->
                <div id="canvas-items" class="relative z-10"></div>
            </div>
        </div>
    </div>
</div>

<style>
.grid-pattern {
    background-image: 
        linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

.shape-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 4px;
    border: 1px solid transparent;
    border-radius: 4px;
    transition: all 0.2s;
    cursor: grab;
    user-select: none;
}

.shape-item:hover {
    border-color: #3b82f6;
    background-color: #f0f9ff;
}

.shape-item:active {
    cursor: grabbing;
}

.canvas-item {
    position: absolute;
    cursor: move;
    user-select: none;
    z-index: 20;
    border: 2px solid #333;
    transition: all 0.1s ease;
    min-width: 20px;
    min-height: 20px;
    will-change: transform, width, height;
}

.canvas-item.dragging {
    opacity: 0.8;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    transform-origin: center;
}

.canvas-item.selected {
    outline: 2px solid #3b82f6;
}

.canvas-item.selected .delete-btn,
.canvas-item.selected .rotate-btn,
.canvas-item.selected .copy-btn,
.canvas-item.selected .resize-handle {
    opacity: 1;
    transform: scale(1);
    animation: buttonPulse 0.6s ease-out;
}

@keyframes buttonPulse {
    0% { transform: scale(0.8); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.canvas-item.resizing {
    opacity: 0.95;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    transform: scale(1.01);
    transition: none !important;
    border-color: #3b82f6;
    border-width: 3px;
}

.canvas-item .delete-btn {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 24px;
    height: 24px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 40;
    border: 2px solid #fff;
    box-shadow: 0 3px 6px rgba(0,0,0,0.4);
    user-select: none;
    transform: scale(0.8);
}

.canvas-item .rotate-btn {
    position: absolute;
    top: -15px;
    left: -15px;
    width: 24px;
    height: 24px;
    background: #3b82f6;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 40;
    border: 2px solid #fff;
    box-shadow: 0 3px 6px rgba(0,0,0,0.4);
    user-select: none;
    transform: scale(0.8);
}

.canvas-item .copy-btn {
    position: absolute;
    top: -15px;
    left: 15px;
    width: 24px;
    height: 24px;
    background: #10b981;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 40;
    border: 2px solid #fff;
    box-shadow: 0 3px 6px rgba(0,0,0,0.4);
    user-select: none;
    transform: scale(0.8);
}

.canvas-item .resize-handle {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #10b981;
    border: 2px solid #fff;
    border-radius: 50%;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 30;
    cursor: pointer;
    box-shadow: 0 3px 6px rgba(0,0,0,0.3);
    user-select: none;
    transform: scale(0.8);
}

.canvas-item .resize-handle.nw { top: -7px; left: -7px; cursor: nw-resize; }
.canvas-item .resize-handle.ne { top: -7px; right: -7px; cursor: ne-resize; }
.canvas-item .resize-handle.sw { bottom: -7px; left: -7px; cursor: sw-resize; }
.canvas-item .resize-handle.se { bottom: -7px; right: -7px; cursor: se-resize; }
.canvas-item .resize-handle.n { top: -7px; left: 50%; transform: translateX(-50%); cursor: n-resize; }
.canvas-item .resize-handle.s { bottom: -7px; left: 50%; transform: translateX(-50%); cursor: s-resize; }
.canvas-item .resize-handle.w { left: -7px; top: 50%; transform: translateY(-50%); cursor: w-resize; }
.canvas-item .resize-handle.e { right: -7px; top: 50%; transform: translateY(-50%); cursor: e-resize; }

.canvas-item:hover .delete-btn,
.canvas-item:hover .rotate-btn,
.canvas-item:hover .copy-btn,
.canvas-item:hover .resize-handle {
    opacity: 1;
    transform: scale(1);
}

.canvas-item .delete-btn:hover {
    background: #dc2626;
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
}

.canvas-item .rotate-btn:hover {
    background: #2563eb;
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
}

.canvas-item .copy-btn:hover {
    background: #059669;
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
}

.canvas-item .resize-handle:hover {
    background: #059669;
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
}

.drag-ghost {
    position: fixed;
    pointer-events: none;
    z-index: 1000;
    opacity: 0.7;
    transform: rotate(5deg);
}

/* Drawing wall styles */
.canvas-item[data-is-drawing-wall="true"] {
    cursor: crosshair;
}

.canvas-item.drawing-wall-active {
    border: 2px dashed #FF4444 !important;
    background-color: rgba(255, 68, 68, 0.1) !important;
}

.canvas-item .stretch-handle {
    position: absolute;
    width: 22px;
    height: 22px;
    background-color: #FF4444;
    border: 2px solid #333;
    border-radius: 50%;
    cursor: crosshair;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    color: white;
    opacity: 0;
    transition: opacity 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.canvas-item:hover .stretch-handle {
    opacity: 1;
}

.canvas-item.drawing-wall-active .stretch-handle {
    opacity: 1;
    background-color: #FF6666;
}

/* Special positioning for drawing wall buttons to avoid overlap */
.canvas-item[data-is-drawing-wall="true"] .delete-btn {
    top: -15px;
    right: -15px;
    width: 22px;
    height: 22px;
    font-size: 13px;
}

.canvas-item[data-is-drawing-wall="true"] .stretch-handle {
    right: -18px;
    width: 24px;
    height: 24px;
    font-size: 13px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('floor-plan-canvas');
    const canvasItems = document.getElementById('canvas-items');
    let selectedItem = null;
    let isDragging = false;
    let isResizing = false;
    let dragOffset = { x: 0, y: 0 };
    let resizeHandle = null;
    let originalSize = { width: 0, height: 0 };
    let originalPosition = { x: 0, y: 0 };
    let itemCounter = 0;
    let dragGhost = null;
    let currentDragShape = null;
    let clipboard = null; // Store copied item data
    let isProcessingDrop = false; // Flag to prevent duplicate drops
    let resizeThrottle = null; // For smooth resizing
    
    // Shape definitions
    const shapes = {
        desk: { width: 80, height: 60, bg: '#8B4513', type: 'rectangle', label: 'Desk' },
        chair: { width: 40, height: 40, bg: '#9CA3AF', type: 'chair', label: 'Chair' },
        table: { width: 80, height: 80, bg: '#A0522D', type: 'table', label: 'Table' },
        sofa: { width: 120, height: 60, bg: '#FBBF24', type: 'sofa', label: 'Sofa' },
        wall: { width: 80, height: 20, bg: '#000000', type: 'drawing-wall', label: 'Wall' },
        door: { width: 60, height: 40, bg: '#D2691E', type: 'door', label: 'Door' },
        window: { width: 60, height: 30, bg: '#BFDBFE', type: 'window', label: 'Window' },
        sink: { width: 60, height: 40, bg: '#9CA3AF', type: 'sink', label: 'Sink' },
        refrigerator: { width: 60, height: 80, bg: '#FFFFFF', type: 'refrigerator', label: 'Fridge' },
        toilet: { width: 40, height: 50, bg: '#FFFFFF', type: 'toilet', label: 'Toilet' },
        plant: { width: 40, height: 40, bg: '#10B981', type: 'plant', label: 'Plant' },
        lamp: { width: 30, height: 50, bg: '#FCD34D', type: 'lamp', label: 'Lamp' }
    };
    
    // Make shape items draggable
    document.querySelectorAll('.shape-item').forEach(item => {
        // Remove existing listeners to prevent duplicates
        item.removeEventListener('dragstart', handleDragStart);
        item.removeEventListener('dragend', handleDragEnd);
        
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });
    
    // Canvas drop zone
    canvas.removeEventListener('dragover', handleDragOver);
    canvas.removeEventListener('drop', handleDrop);
    canvas.removeEventListener('dragenter', handleDragEnter);
    canvas.removeEventListener('dragleave', handleDragLeave);
    canvas.removeEventListener('click', handleCanvasClick);
    
    canvas.addEventListener('dragover', handleDragOver);
    canvas.addEventListener('drop', handleDrop);
    canvas.addEventListener('dragenter', handleDragEnter);
    canvas.addEventListener('dragleave', handleDragLeave);
    canvas.addEventListener('click', handleCanvasClick);
    
    // Also add drop listeners to the canvas container
    const canvasContainer = canvas.parentElement;
    canvasContainer.removeEventListener('dragover', handleDragOver);
    canvasContainer.removeEventListener('drop', handleDrop);
    canvasContainer.removeEventListener('dragenter', handleDragEnter);
    canvasContainer.removeEventListener('dragleave', handleDragLeave);
    
    canvasContainer.addEventListener('dragover', handleDragOver);
    canvasContainer.addEventListener('drop', handleDrop);
    canvasContainer.addEventListener('dragenter', handleDragEnter);
    canvasContainer.addEventListener('dragleave', handleDragLeave);
    
    // Prevent default drag behavior on document
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    document.addEventListener('drop', function(e) {
        e.preventDefault();
    });
    
    // Toolbar buttons
    document.getElementById('save-btn').addEventListener('click', saveFloorPlan);
    document.getElementById('clear-btn').addEventListener('click', clearCanvas);
    document.getElementById('test-btn').addEventListener('click', testAddShape);
    
    // Zoom controls
    document.getElementById('zoom-in').addEventListener('click', () => adjustZoom(1.1));
    document.getElementById('zoom-out').addEventListener('click', () => adjustZoom(0.9));
    
    // Mouse wheel zoom support
    canvas.addEventListener('wheel', handleWheelZoom);
    
    function handleWheelZoom(e) {
        e.preventDefault();
        
        // Determine zoom direction based on scroll
        const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1;
        
        // Adjust zoom
        adjustZoom(zoomFactor);
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeyDown);
    
    function handleKeyDown(e) {
        if (selectedItem) {
            if (e.key === 'r' || e.key === 'R') {
                e.preventDefault();
                rotateItem(selectedItem);
            }
            if (e.key === 'c' || e.key === 'C') {
                e.preventDefault();
                copyItem(selectedItem);
            }
            if (e.key === 'v' || e.key === 'V') {
                e.preventDefault();
                pasteItem();
            }
        }
    }
    
    function rotateItem(item) {
        const currentRotation = parseInt(item.dataset.rotation || '0');
        const newRotation = (currentRotation + 90) % 360;
        item.dataset.rotation = newRotation;
        item.style.transform = `rotate(${newRotation}deg)`;
        console.log('Rotated item to:', newRotation + '°');
    }
    
    function startResizing(e, item, handle) {
        isResizing = true;
        resizeHandle = handle;
        selectedItem = item;
        
        // Store original size and position
        originalSize.width = item.offsetWidth;
        originalSize.height = item.offsetHeight;
        originalPosition.x = parseInt(item.style.left) || 0;
        originalPosition.y = parseInt(item.style.top) || 0;
        
        // Add resizing class for visual feedback
        item.classList.add('resizing');
        
        // Ensure the resizing item stays on top
        item.style.zIndex = '1000';
        
        // Add cursor feedback
        document.body.style.cursor = 'crosshair';
        
        document.addEventListener('mousemove', handleResize);
        document.addEventListener('mouseup', stopResizing);
        
        e.preventDefault();
    }
    
    function handleResize(e) {
        if (!isResizing || !selectedItem) return;
        
        // Throttle resize updates for smoother performance
        if (resizeThrottle) {
            clearTimeout(resizeThrottle);
        }
        
        resizeThrottle = setTimeout(() => {
            const canvasRect = canvas.getBoundingClientRect();
            const mouseX = e.clientX - canvasRect.left;
            const mouseY = e.clientY - canvasRect.top;
        
        let newWidth, newHeight, newX, newY;
        
        switch(resizeHandle) {
            case 'se': // Bottom-right corner
                newWidth = Math.max(20, mouseX - originalPosition.x);
                newHeight = Math.max(20, mouseY - originalPosition.y);
                newX = originalPosition.x;
                newY = originalPosition.y;
                break;
            case 'sw': // Bottom-left corner
                newWidth = Math.max(20, originalPosition.x + originalSize.width - mouseX);
                newHeight = Math.max(20, mouseY - originalPosition.y);
                newX = mouseX;
                newY = originalPosition.y;
                break;
            case 'ne': // Top-right corner
                newWidth = Math.max(20, mouseX - originalPosition.x);
                newHeight = Math.max(20, originalPosition.y + originalSize.height - mouseY);
                newX = originalPosition.x;
                newY = mouseY;
                break;
            case 'nw': // Top-left corner
                newWidth = Math.max(20, originalPosition.x + originalSize.width - mouseX);
                newHeight = Math.max(20, originalPosition.y + originalSize.height - mouseY);
                newX = mouseX;
                newY = mouseY;
                break;
            case 'e': // Right edge
                newWidth = Math.max(20, mouseX - originalPosition.x);
                newHeight = originalSize.height;
                newX = originalPosition.x;
                newY = originalPosition.y;
                break;
            case 'w': // Left edge
                newWidth = Math.max(20, originalPosition.x + originalSize.width - mouseX);
                newHeight = originalSize.height;
                newX = mouseX;
                newY = originalPosition.y;
                break;
            case 's': // Bottom edge
                newWidth = originalSize.width;
                newHeight = Math.max(20, mouseY - originalPosition.y);
                newX = originalPosition.x;
                newY = originalPosition.y;
                break;
            case 'n': // Top edge
                newWidth = originalSize.width;
                newHeight = Math.max(20, originalPosition.y + originalSize.height - mouseY);
                newX = originalPosition.x;
                newY = mouseY;
                break;
        }
        
        // Apply new size and position with smooth transitions
        selectedItem.style.transition = 'none'; // Disable transitions during resize for smoothness
        selectedItem.style.width = newWidth + 'px';
        selectedItem.style.height = newHeight + 'px';
        selectedItem.style.left = newX + 'px';
        selectedItem.style.top = newY + 'px';
        
        // Re-enable transitions after resize
        requestAnimationFrame(() => {
            selectedItem.style.transition = '';
        });
        }, 16); // ~60fps for smooth resizing
    }
    
    function stopResizing() {
        if (selectedItem) {
            // Reset z-index and remove resizing class
            selectedItem.style.zIndex = '20';
            selectedItem.classList.remove('resizing');
        }
        
        // Reset cursor
        document.body.style.cursor = '';
        
        // Clear throttle
        if (resizeThrottle) {
            clearTimeout(resizeThrottle);
            resizeThrottle = null;
        }
        
        isResizing = false;
        resizeHandle = null;
        selectedItem = null;
        document.removeEventListener('mousemove', handleResize);
        document.removeEventListener('mouseup', stopResizing);
    }
    
    function testAddShape() {
        console.log('Test button clicked - adding a desk');
        createShape('desk', 100, 100);
    }
    
    function handleDragStart(e) {
        const shapeItem = e.target.closest('.shape-item');
        if (!shapeItem) return;
        
        const shapeType = shapeItem.dataset.shape;
        currentDragShape = shapeType;
        
        e.dataTransfer.setData('text/plain', shapeType);
        e.dataTransfer.effectAllowed = 'copy';
        
        // Create drag ghost
        createDragGhost(shapeItem, shapeType);
        
        // Add visual feedback
        shapeItem.style.opacity = '0.5';
        
        console.log('Drag started for:', shapeType);
    }
    
    function createDragGhost(shapeItem, shapeType) {
        // Remove existing ghost
        if (dragGhost) {
            document.body.removeChild(dragGhost);
        }
        
        // Create new ghost
        dragGhost = document.createElement('div');
        dragGhost.className = 'drag-ghost';
        dragGhost.style.width = '60px';
        dragGhost.style.height = '40px';
        dragGhost.style.backgroundColor = shapes[shapeType].bg;
        dragGhost.style.border = '2px solid #3b82f6';
        dragGhost.style.borderRadius = shapes[shapeType].type === 'circle' ? '50%' : '4px';
        dragGhost.style.left = '-100px';
        dragGhost.style.top = '-100px';
        
        document.body.appendChild(dragGhost);
    }
    
    function handleDragEnd(e) {
        // Remove visual feedback
        e.target.style.opacity = '1';
        
        // Remove drag ghost
        if (dragGhost) {
            document.body.removeChild(dragGhost);
            dragGhost = null;
        }
        
        currentDragShape = null;
        console.log('Drag ended');
    }
    
    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        
        // Update ghost position
        if (dragGhost) {
            dragGhost.style.left = (e.clientX - 30) + 'px';
            dragGhost.style.top = (e.clientY - 20) + 'px';
        }
    }
    
    function handleDragEnter(e) {
        e.preventDefault();
        console.log('Drag entered canvas area');
    }
    
    function handleDragLeave(e) {
        e.preventDefault();
        console.log('Drag left canvas area');
    }
    
    function handleDrop(e) {
        e.preventDefault();
        console.log('Drop event triggered!', new Date().getTime());
        
        // Prevent duplicate drops
        if (isProcessingDrop) {
            console.log('Drop already processing, skipping');
            return;
        }
        
        isProcessingDrop = true;
        
        // Get shape type from data transfer or current drag shape
        let shapeType = e.dataTransfer.getData('text/plain');
        if (!shapeType && currentDragShape) {
            shapeType = currentDragShape;
        }
        
        if (!shapeType) {
            console.log('No shape type found for drop');
            isProcessingDrop = false;
            return;
        }
        
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        console.log('Dropping shape:', shapeType, 'at position:', x, y);
        
        // Create the shape
        createShape(shapeType, x, y);
        
        // Remove drag ghost
        if (dragGhost) {
            document.body.removeChild(dragGhost);
            dragGhost = null;
        }
        
        // Reset the flag after a short delay
        setTimeout(() => {
            isProcessingDrop = false;
        }, 100);
        
        console.log('Shape created successfully');
    }
    
    function createShape(shapeType, x, y) {
        const shape = shapes[shapeType];
        if (!shape) {
            console.log('Shape not found:', shapeType);
            return;
        }
        
        console.log('Creating shape:', shapeType, 'with properties:', shape, 'at time:', new Date().getTime());
        console.log('Position coordinates:', x, y);
        
        const item = document.createElement('div');
        item.className = 'canvas-item';
        item.dataset.shape = shapeType;
        item.dataset.id = ++itemCounter;
        item.dataset.createdAt = new Date().getTime();
        
        // Set position BEFORE adding any child elements
        item.style.position = 'absolute';
        item.style.left = x + 'px';
        item.style.top = y + 'px';
        item.style.width = shape.width + 'px';
        item.style.height = shape.height + 'px';
        item.style.backgroundColor = shape.bg;
        item.style.border = '2px solid #333';
        item.style.zIndex = '20';
        
        console.log('Item position set to:', item.style.left, item.style.top);
        
        // Render shape based on type
        switch(shape.type) {
            case 'circle':
                item.style.borderRadius = '50%';
                break;
            case 'chair':
                item.style.borderRadius = '50%';
                // Add chair back
                const chairBack = document.createElement('div');
                chairBack.style.position = 'absolute';
                chairBack.style.top = '-10px';
                chairBack.style.left = '50%';
                chairBack.style.transform = 'translateX(-50%)';
                chairBack.style.width = '20px';
                chairBack.style.height = '20px';
                chairBack.style.backgroundColor = shape.bg;
                chairBack.style.border = '2px solid #333';
                chairBack.style.borderRadius = '50%';
                item.appendChild(chairBack);
                break;
            case 'table':
                // Add table legs
                for (let i = 0; i < 4; i++) {
                    const leg = document.createElement('div');
                    leg.style.position = 'absolute';
                    leg.style.width = '6px';
                    leg.style.height = '15px';
                    leg.style.backgroundColor = '#654321';
                    leg.style.border = '1px solid #333';
                    leg.style.bottom = '-15px';
                    
                    if (i === 0) leg.style.left = '5px';
                    else if (i === 1) leg.style.right = '5px';
                    else if (i === 2) leg.style.left = '5px';
                    else leg.style.right = '5px';
                    
                    if (i < 2) leg.style.top = '0px';
                    else leg.style.bottom = '-15px';
                    
                    item.appendChild(leg);
                }
                break;
            case 'sofa':
                // Add sofa back and arms
                const sofaBack = document.createElement('div');
                sofaBack.style.position = 'absolute';
                sofaBack.style.top = '-15px';
                sofaBack.style.left = '0';
                sofaBack.style.right = '0';
                sofaBack.style.height = '15px';
                sofaBack.style.backgroundColor = shape.bg;
                sofaBack.style.border = '2px solid #333';
                sofaBack.style.borderRadius = '8px 8px 0 0';
                item.appendChild(sofaBack);
                
                // Add arms
                const leftArm = document.createElement('div');
                leftArm.style.position = 'absolute';
                leftArm.style.left = '-8px';
                leftArm.style.top = '0';
                leftArm.style.width = '8px';
                leftArm.style.height = '100%';
                leftArm.style.backgroundColor = shape.bg;
                leftArm.style.border = '2px solid #333';
                leftArm.style.borderRadius = '8px 0 0 8px';
                item.appendChild(leftArm);
                
                const rightArm = document.createElement('div');
                rightArm.style.position = 'absolute';
                rightArm.style.right = '-8px';
                rightArm.style.top = '0';
                rightArm.style.width = '8px';
                rightArm.style.height = '100%';
                rightArm.style.backgroundColor = shape.bg;
                rightArm.style.border = '2px solid #333';
                rightArm.style.borderRadius = '0 8px 8px 0';
                item.appendChild(rightArm);
                break;
            case 'door':
                // Add door handle
                const handle = document.createElement('div');
                handle.style.position = 'absolute';
                handle.style.right = '5px';
                handle.style.top = '50%';
                handle.style.transform = 'translateY(-50%)';
                handle.style.width = '8px';
                handle.style.height = '8px';
                handle.style.backgroundColor = '#FFD700';
                handle.style.border = '1px solid #333';
                handle.style.borderRadius = '50%';
                item.appendChild(handle);
                break;
            case 'window':
                // Add window frame
                item.style.border = '3px solid #1F2937';
                const windowPane = document.createElement('div');
                windowPane.style.position = 'absolute';
                windowPane.style.top = '3px';
                windowPane.style.left = '3px';
                windowPane.style.right = '3px';
                windowPane.style.bottom = '3px';
                windowPane.style.backgroundColor = '#E0F2FE';
                windowPane.style.border = '1px solid #1F2937';
                item.appendChild(windowPane);
                break;
            case 'sink':
                // Add sink basin
                const basin = document.createElement('div');
                basin.style.position = 'absolute';
                basin.style.top = '5px';
                basin.style.left = '5px';
                basin.style.right = '5px';
                basin.style.bottom = '5px';
                basin.style.backgroundColor = '#E5E7EB';
                basin.style.border = '2px solid #6B7280';
                basin.style.borderRadius = '50%';
                item.appendChild(basin);
                break;
            case 'refrigerator':
                // Add refrigerator door
                const fridgeDoor = document.createElement('div');
                fridgeDoor.style.position = 'absolute';
                fridgeDoor.style.top = '0';
                fridgeDoor.style.left = '0';
                fridgeDoor.style.right = '0';
                fridgeDoor.style.bottom = '0';
                fridgeDoor.style.backgroundColor = '#F3F4F6';
                fridgeDoor.style.border = '2px solid #374151';
                item.appendChild(fridgeDoor);
                
                // Add handle
                const fridgeHandle = document.createElement('div');
                fridgeHandle.style.position = 'absolute';
                fridgeHandle.style.right = '3px';
                fridgeHandle.style.top = '50%';
                fridgeHandle.style.transform = 'translateY(-50%)';
                fridgeHandle.style.width = '4px';
                fridgeHandle.style.height = '20px';
                fridgeHandle.style.backgroundColor = '#6B7280';
                fridgeHandle.style.border = '1px solid #374151';
                item.appendChild(fridgeHandle);
                break;
            case 'toilet':
                // Add toilet seat
                const seat = document.createElement('div');
                seat.style.position = 'absolute';
                seat.style.top = '5px';
                seat.style.left = '5px';
                seat.style.right = '5px';
                seat.style.bottom = '15px';
                seat.style.backgroundColor = '#FFFFFF';
                seat.style.border = '2px solid #6B7280';
                seat.style.borderRadius = '50% 50% 0 0';
                item.appendChild(seat);
                break;
            case 'plant':
                // Add plant pot
                const pot = document.createElement('div');
                pot.style.position = 'absolute';
                pot.style.bottom = '0';
                pot.style.left = '50%';
                pot.style.transform = 'translateX(-50%)';
                pot.style.width = '20px';
                pot.style.height = '10px';
                pot.style.backgroundColor = '#8B4513';
                pot.style.border = '1px solid #654321';
                pot.style.borderRadius = '0 0 10px 10px';
                item.appendChild(pot);
                break;
            case 'lamp':
                // Add lamp shade
                const shade = document.createElement('div');
                shade.style.position = 'absolute';
                shade.style.top = '-10px';
                shade.style.left = '50%';
                shade.style.transform = 'translateX(-50%)';
                shade.style.width = '25px';
                shade.style.height = '15px';
                shade.style.backgroundColor = '#FEF3C7';
                shade.style.border = '2px solid #F59E0B';
                shade.style.borderRadius = '50% 50% 0 0';
                item.appendChild(shade);
                break;
            case 'drawing-wall':
                // Make wall stretchable like a drawing line
                item.style.cursor = 'crosshair';
                item.dataset.isDrawingWall = 'true';
                item.dataset.startX = x;
                item.dataset.startY = y;
                
                // Add drawing wall visual indicator
                const wallIndicator = document.createElement('div');
                wallIndicator.style.position = 'absolute';
                wallIndicator.style.top = '0';
                wallIndicator.style.left = '0';
                wallIndicator.style.right = '0';
                wallIndicator.style.bottom = '0';
                wallIndicator.style.backgroundColor = 'transparent';
                wallIndicator.style.border = '2px dashed #666';
                wallIndicator.style.pointerEvents = 'none';
                item.appendChild(wallIndicator);
                
                // Add stretch handles for drawing wall
                const stretchHandle = document.createElement('div');
                stretchHandle.className = 'stretch-handle';
                stretchHandle.style.position = 'absolute';
                stretchHandle.style.right = '-15px';
                stretchHandle.style.top = '50%';
                stretchHandle.style.transform = 'translateY(-50%)';
                stretchHandle.style.width = '22px';
                stretchHandle.style.height = '22px';
                stretchHandle.style.backgroundColor = '#FF4444';
                stretchHandle.style.border = '2px solid #333';
                stretchHandle.style.borderRadius = '50%';
                stretchHandle.style.cursor = 'crosshair';
                stretchHandle.style.zIndex = '30';
                stretchHandle.innerHTML = '↔';
                stretchHandle.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                    startDrawingWall(e, item);
                });
                item.appendChild(stretchHandle);
                break;
        }
        
        // Add label
        const label = document.createElement('div');
        label.className = 'canvas-item-label';
        label.style.position = 'absolute';
        label.style.bottom = '-20px';
        label.style.left = '50%';
        label.style.transform = 'translateX(-50%)';
        label.style.fontSize = '10px';
        label.style.color = '#374151';
        label.style.fontWeight = 'bold';
        label.style.textAlign = 'center';
        label.style.whiteSpace = 'nowrap';
        label.textContent = shape.label;
        item.appendChild(label);
        
        // Add delete button
        const deleteBtn = document.createElement('div');
        deleteBtn.className = 'delete-btn';
        deleteBtn.innerHTML = '×';
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            item.remove();
        });
        item.appendChild(deleteBtn);
        
        // Add rotate button
        const rotateBtn = document.createElement('div');
        rotateBtn.className = 'rotate-btn';
        rotateBtn.innerHTML = '↻';
        rotateBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            rotateItem(item);
        });
        item.appendChild(rotateBtn);
        
        // Add copy button
        const copyBtn = document.createElement('div');
        copyBtn.className = 'copy-btn';
        copyBtn.innerHTML = '📋';
        copyBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            copyItem(item);
        });
        item.appendChild(copyBtn);
        
        // Add resize handles
        const resizeHandles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'w', 'e'];
        resizeHandles.forEach(handle => {
            const resizeHandle = document.createElement('div');
            resizeHandle.className = `resize-handle ${handle}`;
            resizeHandle.dataset.handle = handle;
            resizeHandle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                startResizing(e, item, handle);
            });
            item.appendChild(resizeHandle);
        });
        
        // Add click handler for selection
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            selectItem(item);
        });
        
        // Add drag functionality for moving items
        item.addEventListener('mousedown', startDragging);
        
        // Add label
        const itemLabel = document.createElement('div');
        itemLabel.className = 'canvas-item-label';
        itemLabel.style.position = 'absolute';
        itemLabel.style.bottom = '-20px';
        itemLabel.style.left = '50%';
        itemLabel.style.transform = 'translateX(-50%)';
        itemLabel.style.fontSize = '10px';
        itemLabel.style.color = 'white';
        itemLabel.style.textShadow = '1px 1px 1px rgba(0,0,0,0.8)';
        itemLabel.style.whiteSpace = 'nowrap';
        itemLabel.textContent = shape.label;
        item.appendChild(itemLabel);
        
        // Add to canvas
        canvasItems.appendChild(item);
        
        console.log('Shape added to canvas:', item);
        console.log('Final position:', item.style.left, item.style.top);
        
        // Force a visual update
        item.style.display = 'block';
        item.style.visibility = 'visible';
        item.style.opacity = '1';
    }
    
    function startDragging(e) {
        if (e.target.classList.contains('delete-btn') || 
            e.target.classList.contains('rotate-btn') || 
            e.target.classList.contains('copy-btn') ||
            e.target.classList.contains('resize-handle')) return;
        
        isDragging = true;
        selectedItem = e.currentTarget;
        
        // Get the current position relative to the canvas
        const canvasRect = canvas.getBoundingClientRect();
        
        // Get the item's actual position and size (not the rotated bounds)
        const itemLeft = parseInt(selectedItem.style.left) || 0;
        const itemTop = parseInt(selectedItem.style.top) || 0;
        const itemWidth = selectedItem.offsetWidth;
        const itemHeight = selectedItem.offsetHeight;
        
        // Calculate the mouse position relative to the item's actual position
        // This works regardless of rotation because we're using the item's actual coordinates
        dragOffset.x = e.clientX - canvasRect.left - itemLeft;
        dragOffset.y = e.clientY - canvasRect.top - itemTop;
        
        console.log('=== DRAG START ===');
        console.log('Mouse position:', e.clientX, e.clientY);
        console.log('Canvas rect:', canvasRect.left, canvasRect.top);
        console.log('Item actual position:', itemLeft, itemTop);
        console.log('Item size:', itemWidth, itemHeight);
        console.log('Drag offset:', dragOffset.x, dragOffset.y);
        
        // Ensure the dragged item stays on top during drag
        selectedItem.style.zIndex = '1000';
        
        // Add dragging class for visual feedback
        selectedItem.classList.add('dragging');
        
        document.addEventListener('mousemove', handleMouseMove);
        document.addEventListener('mouseup', stopDragging);
        
        e.preventDefault();
    }
    
    function handleMouseMove(e) {
        if (!isDragging || !selectedItem) return;
        
        const canvasRect = canvas.getBoundingClientRect();
        
        // Calculate new position by subtracting the offset from the mouse position
        // This works for both rotated and non-rotated items
        const newX = e.clientX - canvasRect.left - dragOffset.x;
        const newY = e.clientY - canvasRect.top - dragOffset.y;
        
        console.log('=== DRAG MOVE ===');
        console.log('Mouse position:', e.clientX, e.clientY);
        console.log('Canvas rect:', canvasRect.left, canvasRect.top);
        console.log('Offset:', dragOffset.x, dragOffset.y);
        console.log('New position:', newX, newY);
        
        // Update position directly
        selectedItem.style.left = newX + 'px';
        selectedItem.style.top = newY + 'px';
        
        console.log('Updated item position to:', selectedItem.style.left, selectedItem.style.top);
    }
    
    function stopDragging() {
        if (selectedItem) {
            console.log('=== DRAG STOP ===');
            console.log('Final position:', selectedItem.style.left, selectedItem.style.top);
            
            // Reset z-index and remove dragging class
            selectedItem.style.zIndex = '20';
            selectedItem.classList.remove('dragging');
        }
        
        isDragging = false;
        selectedItem = null;
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', stopDragging);
    }
    
    function selectItem(item) {
        // Remove previous selection
        document.querySelectorAll('.canvas-item').forEach(i => i.classList.remove('selected'));
        
        // Select new item
        item.classList.add('selected');
        selectedItem = item;
    }
    
    function handleCanvasClick() {
        // Deselect when clicking on canvas
        document.querySelectorAll('.canvas-item').forEach(i => i.classList.remove('selected'));
        selectedItem = null;
    }
    
    function saveFloorPlan() {
        const items = [];
        document.querySelectorAll('.canvas-item').forEach(item => {
            // Get the proper label for the shape type
            const shapeType = item.dataset.shape;
            const shapeConfig = shapes[shapeType];
            const itemLabel = shapeConfig ? shapeConfig.label : shapeType;
            
                    // Get the actual rotation from transform or dataset
        let actualRotation = parseInt(item.dataset.rotation || '0');
        
        // Check if there's a CSS transform rotation (for walls)
        if (item.style.transform && item.style.transform.includes('rotate')) {
            const transformMatch = item.style.transform.match(/rotate\(([^)]+)deg\)/);
            if (transformMatch) {
                actualRotation = parseInt(transformMatch[1]);
            }
        }
        
        items.push({
            shape: shapeType,
            x: parseInt(item.style.left),
            y: parseInt(item.style.top),
            id: item.dataset.id,
            rotation: actualRotation,
            width: parseInt(item.style.width),
            height: parseInt(item.style.height),
            label: itemLabel,
            backgroundColor: item.style.backgroundColor
        });
        });
        
        // Save to localStorage for immediate backup
        localStorage.setItem('floorPlan', JSON.stringify(items));
        
        // Save to database
        try {
            fetch('{{ route("hub-owner.floor-plan.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    layout_data: items,
                    name: 'My Floor Plan',
                    description: 'Floor plan created on ' + new Date().toLocaleDateString()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSaveModal();
                } else {
                    alert('Saved to browser storage. Database save failed.');
                }
            })
            .catch(error => {
                alert('Saved to browser storage. Database save failed.');
            });
        } catch (error) {
            alert('Saved to browser storage. Database save failed.');
        }
    }
    
    function loadFloorPlan() {
        // Try to load from database first
        fetch('{{ route("hub-owner.floor-plan.load") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.layout_data && data.layout_data.length > 0) {
                // Load from database
                loadItemsFromData(data.layout_data);
                console.log('Floor plan loaded from database');
            } else {
                // Try to load from localStorage as backup
                const savedData = localStorage.getItem('floorPlan');
                if (savedData) {
                    const items = JSON.parse(savedData);
                    loadItemsFromData(items);
                    console.log('Floor plan loaded from browser storage');
                }
            }
        })
        .catch(error => {
            // Try to load from localStorage as backup
            const savedData = localStorage.getItem('floorPlan');
            if (savedData) {
                const items = JSON.parse(savedData);
                loadItemsFromData(items);
                console.log('Floor plan loaded from browser storage');
            }
        });
    }
    
    function loadItemsFromData(items) {
        // Clear existing items
        canvasItems.innerHTML = '';
        itemCounter = 0;
        
        // Load each item
        items.forEach(itemData => {
            const newItem = document.createElement('div');
            newItem.className = 'canvas-item';
            newItem.dataset.shape = itemData.shape;
            newItem.dataset.id = itemData.id;
            newItem.dataset.rotation = itemData.rotation || '0';
            
            // Set position and size
            newItem.style.position = 'absolute';
            newItem.style.left = itemData.x + 'px';
            newItem.style.top = itemData.y + 'px';
            newItem.style.width = (itemData.width || shapes[itemData.shape].width) + 'px';
            newItem.style.height = (itemData.height || shapes[itemData.shape].height) + 'px';
            // Use saved background color if available, otherwise use default
            newItem.style.backgroundColor = itemData.backgroundColor || shapes[itemData.shape].bg;
            newItem.style.border = '2px solid #333';
            newItem.style.cursor = 'move';
            newItem.style.zIndex = '10';
            
            // Apply rotation if any
            if (itemData.rotation) {
                newItem.style.transform = `rotate(${itemData.rotation}deg)`;
                // Also update the dataset for consistency
                newItem.dataset.rotation = itemData.rotation.toString();
            }
            
            // Recreate the shape based on type
            recreateShape(newItem, itemData.shape);
            
            // Add label
            const savedItemLabel = document.createElement('div');
            savedItemLabel.className = 'canvas-item-label';
            savedItemLabel.style.position = 'absolute';
            savedItemLabel.style.bottom = '-20px';
            savedItemLabel.style.left = '50%';
            savedItemLabel.style.transform = 'translateX(-50%)';
            savedItemLabel.style.fontSize = '10px';
            savedItemLabel.style.color = 'white';
            savedItemLabel.style.textShadow = '1px 1px 1px rgba(0,0,0,0.8)';
            savedItemLabel.style.whiteSpace = 'nowrap';
            // Use saved label if available, otherwise use default
            savedItemLabel.textContent = itemData.label || shapes[itemData.shape].label;
            newItem.appendChild(savedItemLabel);
            
            // Add delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'delete-btn';
            deleteBtn.innerHTML = '×';
            deleteBtn.style.position = 'absolute';
            deleteBtn.style.top = '-8px';
            deleteBtn.style.right = '-8px';
            deleteBtn.style.width = '16px';
            deleteBtn.style.height = '16px';
            deleteBtn.style.backgroundColor = '#EF4444';
            deleteBtn.style.color = 'white';
            deleteBtn.style.border = 'none';
            deleteBtn.style.borderRadius = '50%';
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.style.fontSize = '12px';
            deleteBtn.style.fontWeight = 'bold';
            deleteBtn.style.zIndex = '40';
            deleteBtn.style.opacity = '0';
            deleteBtn.style.transition = 'opacity 0.2s';
            deleteBtn.addEventListener('click', () => deleteItem(newItem));
            newItem.appendChild(deleteBtn);
            
            // Add rotate button
            const rotateBtn = document.createElement('button');
            rotateBtn.className = 'rotate-btn';
            rotateBtn.innerHTML = '↻';
            rotateBtn.style.position = 'absolute';
            rotateBtn.style.top = '-8px';
            rotateBtn.style.left = '-8px';
            rotateBtn.style.width = '16px';
            rotateBtn.style.height = '16px';
            rotateBtn.style.backgroundColor = '#3B82F6';
            rotateBtn.style.color = 'white';
            rotateBtn.style.border = 'none';
            rotateBtn.style.borderRadius = '50%';
            rotateBtn.style.cursor = 'pointer';
            rotateBtn.style.fontSize = '10px';
            rotateBtn.style.fontWeight = 'bold';
            rotateBtn.style.zIndex = '40';
            rotateBtn.style.opacity = '0';
            rotateBtn.style.transition = 'opacity 0.2s';
            rotateBtn.addEventListener('click', () => rotateItem(newItem));
            newItem.appendChild(rotateBtn);
            
            // Add copy button
            const copyBtn = document.createElement('button');
            copyBtn.className = 'copy-btn';
            copyBtn.innerHTML = '📋';
            copyBtn.style.position = 'absolute';
            copyBtn.style.top = '-8px';
            copyBtn.style.left = '8px';
            copyBtn.style.width = '16px';
            copyBtn.style.height = '16px';
            copyBtn.style.backgroundColor = '#10B981';
            copyBtn.style.color = 'white';
            copyBtn.style.border = 'none';
            copyBtn.style.borderRadius = '50%';
            copyBtn.style.cursor = 'pointer';
            copyBtn.style.fontSize = '8px';
            copyBtn.style.fontWeight = 'bold';
            copyBtn.style.zIndex = '40';
            copyBtn.style.opacity = '0';
            copyBtn.style.transition = 'opacity 0.2s';
            copyBtn.addEventListener('click', () => copyItem(newItem));
            newItem.appendChild(copyBtn);
            
            // Add resize handles
            const handles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'w', 'e'];
            handles.forEach(handle => {
                const handleEl = document.createElement('div');
                handleEl.className = 'resize-handle';
                handleEl.dataset.handle = handle;
                handleEl.style.position = 'absolute';
                handleEl.style.width = '8px';
                handleEl.style.height = '8px';
                handleEl.style.backgroundColor = '#10B981';
                handleEl.style.border = '1px solid #047857';
                handleEl.style.borderRadius = '50%';
                handleEl.style.cursor = handle.includes('n') ? 'n-resize' : 
                                      handle.includes('s') ? 's-resize' : 
                                      handle.includes('w') ? 'w-resize' : 'e-resize';
                handleEl.style.zIndex = '30';
                handleEl.style.opacity = '0';
                handleEl.style.transition = 'opacity 0.2s';
                
                // Position handles
                if (handle === 'nw') { handleEl.style.top = '-4px'; handleEl.style.left = '-4px'; }
                else if (handle === 'ne') { handleEl.style.top = '-4px'; handleEl.style.right = '-4px'; }
                else if (handle === 'sw') { handleEl.style.bottom = '-4px'; handleEl.style.left = '-4px'; }
                else if (handle === 'se') { handleEl.style.bottom = '-4px'; handleEl.style.right = '-4px'; }
                else if (handle === 'n') { handleEl.style.top = '-4px'; handleEl.style.left = '50%'; handleEl.style.transform = 'translateX(-50%)'; }
                else if (handle === 's') { handleEl.style.bottom = '-4px'; handleEl.style.left = '50%'; handleEl.style.transform = 'translateX(-50%)'; }
                else if (handle === 'w') { handleEl.style.left = '-4px'; handleEl.style.top = '50%'; handleEl.style.transform = 'translateY(-50%)'; }
                else if (handle === 'e') { handleEl.style.right = '-4px'; handleEl.style.top = '50%'; handleEl.style.transform = 'translateY(-50%)'; }
                
                handleEl.addEventListener('mousedown', (e) => startResizing(e, newItem, handle));
                newItem.appendChild(handleEl);
            });
            
            // Add event listeners
            newItem.addEventListener('click', function() { selectItem(newItem); });
            newItem.addEventListener('mousedown', startDragging);
            
            // Add hover effects
            newItem.addEventListener('mouseenter', () => {
                newItem.querySelector('.delete-btn').style.opacity = '1';
                newItem.querySelector('.rotate-btn').style.opacity = '1';
                newItem.querySelector('.copy-btn').style.opacity = '1';
                newItem.querySelectorAll('.resize-handle').forEach(h => h.style.opacity = '1');
            });
            
            newItem.addEventListener('mouseleave', () => {
                newItem.querySelector('.delete-btn').style.opacity = '0';
                newItem.querySelector('.rotate-btn').style.opacity = '0';
                newItem.querySelector('.copy-btn').style.opacity = '0';
                newItem.querySelectorAll('.resize-handle').forEach(h => h.style.opacity = '0');
            });
            
            canvasItems.appendChild(newItem);
        });
        
        // Update item counter
        if (items.length > 0) {
            itemCounter = Math.max(...items.map(item => item.id)) + 1;
        }
    }
    
    function recreateShape(item, shapeType) {
        switch(shapeType) {
            case 'chair':
                // Add chair back
                const chairBack = document.createElement('div');
                chairBack.style.position = 'absolute';
                chairBack.style.top = '-8px';
                chairBack.style.left = '50%';
                chairBack.style.transform = 'translateX(-50%)';
                chairBack.style.width = '16px';
                chairBack.style.height = '8px';
                chairBack.style.backgroundColor = shapes[shapeType].bg;
                chairBack.style.border = '2px solid #333';
                chairBack.style.borderRadius = '8px 8px 0 0';
                item.appendChild(chairBack);
                break;
            case 'table':
                // Add table legs
                for (let i = 0; i < 4; i++) {
                    const leg = document.createElement('div');
                    leg.style.position = 'absolute';
                    leg.style.width = '4px';
                    leg.style.height = '15px';
                    leg.style.backgroundColor = '#8B4513';
                    leg.style.border = '1px solid #654321';
                    
                    if (i < 2) leg.style.left = '5px';
                    else leg.style.right = '5px';
                    
                    if (i < 2) leg.style.top = '0px';
                    else leg.style.bottom = '-15px';
                    
                    item.appendChild(leg);
                }
                break;
            case 'sofa':
                // Add sofa back and arms
                const sofaBack = document.createElement('div');
                sofaBack.style.position = 'absolute';
                sofaBack.style.top = '-15px';
                sofaBack.style.left = '0';
                sofaBack.style.right = '0';
                sofaBack.style.height = '15px';
                sofaBack.style.backgroundColor = shapes[shapeType].bg;
                sofaBack.style.border = '2px solid #333';
                sofaBack.style.borderRadius = '8px 8px 0 0';
                item.appendChild(sofaBack);
                
                // Add arms
                const leftArm = document.createElement('div');
                leftArm.style.position = 'absolute';
                leftArm.style.left = '-8px';
                leftArm.style.top = '0';
                leftArm.style.width = '8px';
                leftArm.style.height = '100%';
                leftArm.style.backgroundColor = shapes[shapeType].bg;
                leftArm.style.border = '2px solid #333';
                leftArm.style.borderRadius = '8px 0 0 8px';
                item.appendChild(leftArm);
                
                const rightArm = document.createElement('div');
                rightArm.style.position = 'absolute';
                rightArm.style.right = '-8px';
                rightArm.style.top = '0';
                rightArm.style.width = '8px';
                rightArm.style.height = '100%';
                rightArm.style.backgroundColor = shapes[shapeType].bg;
                rightArm.style.border = '2px solid #333';
                rightArm.style.borderRadius = '0 8px 8px 0';
                item.appendChild(rightArm);
                break;
            case 'door':
                // Add door handle
                const handle = document.createElement('div');
                handle.style.position = 'absolute';
                handle.style.right = '5px';
                handle.style.top = '50%';
                handle.style.transform = 'translateY(-50%)';
                handle.style.width = '8px';
                handle.style.height = '8px';
                handle.style.backgroundColor = '#FFD700';
                handle.style.border = '1px solid #333';
                handle.style.borderRadius = '50%';
                item.appendChild(handle);
                break;
            case 'window':
                // Add window frame
                item.style.border = '3px solid #1F2937';
                const windowPane = document.createElement('div');
                windowPane.style.position = 'absolute';
                windowPane.style.top = '3px';
                windowPane.style.left = '3px';
                windowPane.style.right = '3px';
                windowPane.style.bottom = '3px';
                windowPane.style.backgroundColor = '#E0F2FE';
                windowPane.style.border = '1px solid #1F2937';
                item.appendChild(windowPane);
                break;
            case 'sink':
                // Add sink basin
                const basin = document.createElement('div');
                basin.style.position = 'absolute';
                basin.style.top = '5px';
                basin.style.left = '5px';
                basin.style.right = '5px';
                basin.style.bottom = '5px';
                basin.style.backgroundColor = '#E5E7EB';
                basin.style.border = '2px solid #6B7280';
                basin.style.borderRadius = '50%';
                item.appendChild(basin);
                break;
            case 'refrigerator':
                // Add refrigerator door
                const fridgeDoor = document.createElement('div');
                fridgeDoor.style.position = 'absolute';
                fridgeDoor.style.top = '0';
                fridgeDoor.style.left = '0';
                fridgeDoor.style.right = '0';
                fridgeDoor.style.bottom = '0';
                fridgeDoor.style.backgroundColor = '#F3F4F6';
                fridgeDoor.style.border = '2px solid #374151';
                item.appendChild(fridgeDoor);
                
                // Add handle
                const fridgeHandle = document.createElement('div');
                fridgeHandle.style.position = 'absolute';
                fridgeHandle.style.right = '3px';
                fridgeHandle.style.top = '50%';
                fridgeHandle.style.transform = 'translateY(-50%)';
                fridgeHandle.style.width = '6px';
                fridgeHandle.style.height = '20px';
                fridgeHandle.style.backgroundColor = '#6B7280';
                fridgeHandle.style.border = '1px solid #374151';
                fridgeHandle.style.borderRadius = '3px';
                item.appendChild(fridgeHandle);
                break;
            case 'toilet':
                // Add toilet seat
                const seat = document.createElement('div');
                seat.style.position = 'absolute';
                seat.style.top = '5px';
                seat.style.left = '5px';
                seat.style.right = '5px';
                seat.style.bottom = '15px';
                seat.style.backgroundColor = '#FFFFFF';
                seat.style.border = '2px solid #6B7280';
                seat.style.borderRadius = '50%';
                item.appendChild(seat);
                break;
            case 'plant':
                // Add plant pot
                const pot = document.createElement('div');
                pot.style.position = 'absolute';
                pot.style.bottom = '0';
                pot.style.left = '50%';
                pot.style.transform = 'translateX(-50%)';
                pot.style.width = '20px';
                pot.style.height = '8px';
                pot.style.backgroundColor = '#8B4513';
                pot.style.border = '1px solid #654321';
                pot.style.borderRadius = '50%';
                item.appendChild(pot);
                break;
            case 'lamp':
                // Add lamp shade
                const shade = document.createElement('div');
                shade.style.position = 'absolute';
                shade.style.top = '0';
                shade.style.left = '50%';
                shade.style.transform = 'translateX(-50%)';
                shade.style.width = '20px';
                shade.style.height = '15px';
                shade.style.backgroundColor = '#FEF3C7';
                shade.style.border = '1px solid #F59E0B';
                shade.style.borderRadius = '50%';
                item.appendChild(shade);
                break;
            case 'drawing-wall':
                // Make wall stretchable like a drawing line
                item.style.cursor = 'crosshair';
                item.dataset.isDrawingWall = 'true';
                
                // Add drawing wall visual indicator
                const wallIndicator = document.createElement('div');
                wallIndicator.style.position = 'absolute';
                wallIndicator.style.top = '0';
                wallIndicator.style.left = '0';
                wallIndicator.style.right = '0';
                wallIndicator.style.bottom = '0';
                wallIndicator.style.backgroundColor = 'transparent';
                wallIndicator.style.border = '2px dashed #666';
                wallIndicator.style.pointerEvents = 'none';
                item.appendChild(wallIndicator);
                
                // Add stretch handles for drawing wall
                const stretchHandle = document.createElement('div');
                stretchHandle.className = 'stretch-handle';
                stretchHandle.style.position = 'absolute';
                stretchHandle.style.right = '-15px';
                stretchHandle.style.top = '50%';
                stretchHandle.style.transform = 'translateY(-50%)';
                stretchHandle.style.width = '22px';
                stretchHandle.style.height = '22px';
                stretchHandle.style.backgroundColor = '#FF4444';
                stretchHandle.style.border = '2px solid #333';
                stretchHandle.style.borderRadius = '50%';
                stretchHandle.style.cursor = 'crosshair';
                stretchHandle.style.zIndex = '30';
                stretchHandle.innerHTML = '↔';
                stretchHandle.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                    startDrawingWall(e, item);
                });
                item.appendChild(stretchHandle);
                break;
        }
    }
    
    function clearCanvas() {
        if (confirm('Are you sure you want to clear the entire floor plan?')) {
            canvasItems.innerHTML = '';
            itemCounter = 0;
            localStorage.removeItem('floorPlan');
        }
    }
    

    
    function adjustZoom(factor) {
        const currentZoom = parseFloat(document.getElementById('zoom-level').textContent) / 100;
        const newZoom = Math.max(0.5, Math.min(2, currentZoom * factor));
        document.getElementById('zoom-level').textContent = Math.round(newZoom * 100) + '%';
        
        canvas.style.transform = `scale(${newZoom})`;
        canvas.style.transformOrigin = 'top left';
    }
    
    function copyItem(item) {
        const labelElement = item.querySelector('div[style*="bottom: -20px"]');
        const labelText = labelElement ? labelElement.textContent : shapes[item.dataset.shape].label;
        
        clipboard = {
            shape: item.dataset.shape,
            x: parseInt(item.style.left),
            y: parseInt(item.style.top),
            id: item.dataset.id,
            rotation: parseInt(item.dataset.rotation || '0'),
            width: parseInt(item.style.width),
            height: parseInt(item.style.height),
            label: labelText
        };
        console.log('Item copied:', clipboard);
    }

    function pasteItem() {
        if (!clipboard) {
            alert('No item to paste. Please copy an item first.');
            return;
        }

        const newItem = document.createElement('div');
        newItem.className = 'canvas-item';
        newItem.dataset.shape = clipboard.shape;
        newItem.dataset.id = ++itemCounter;
        newItem.dataset.rotation = clipboard.rotation;

        // Set position BEFORE adding any child elements
        newItem.style.position = 'absolute';
        newItem.style.left = clipboard.x + 'px';
        newItem.style.top = clipboard.y + 'px';
        newItem.style.width = clipboard.width + 'px';
        newItem.style.height = clipboard.height + 'px';
        newItem.style.backgroundColor = shapes[clipboard.shape].bg;
        newItem.style.border = '2px solid #333';
        newItem.style.zIndex = '20';

        // Render shape based on type
        switch(clipboard.shape) {
            case 'circle':
                newItem.style.borderRadius = '50%';
                break;
            case 'chair':
                newItem.style.borderRadius = '50%';
                // Add chair back
                const chairBack = document.createElement('div');
                chairBack.style.position = 'absolute';
                chairBack.style.top = '-10px';
                chairBack.style.left = '50%';
                chairBack.style.transform = 'translateX(-50%)';
                chairBack.style.width = '20px';
                chairBack.style.height = '20px';
                chairBack.style.backgroundColor = shapes[clipboard.shape].bg;
                chairBack.style.border = '2px solid #333';
                chairBack.style.borderRadius = '50%';
                newItem.appendChild(chairBack);
                break;
            case 'table':
                // Add table legs
                for (let i = 0; i < 4; i++) {
                    const leg = document.createElement('div');
                    leg.style.position = 'absolute';
                    leg.style.width = '6px';
                    leg.style.height = '15px';
                    leg.style.backgroundColor = '#654321';
                    leg.style.border = '1px solid #333';
                    leg.style.bottom = '-15px';
                    
                    if (i === 0) leg.style.left = '5px';
                    else if (i === 1) leg.style.right = '5px';
                    else if (i === 2) leg.style.left = '5px';
                    else leg.style.right = '5px';
                    
                    if (i < 2) leg.style.top = '0px';
                    else leg.style.bottom = '-15px';
                    
                    newItem.appendChild(leg);
                }
                break;
            case 'sofa':
                // Add sofa back and arms
                const sofaBack = document.createElement('div');
                sofaBack.style.position = 'absolute';
                sofaBack.style.top = '-15px';
                sofaBack.style.left = '0';
                sofaBack.style.right = '0';
                sofaBack.style.height = '15px';
                sofaBack.style.backgroundColor = shapes[clipboard.shape].bg;
                sofaBack.style.border = '2px solid #333';
                sofaBack.style.borderRadius = '8px 8px 0 0';
                newItem.appendChild(sofaBack);
                
                // Add arms
                const leftArm = document.createElement('div');
                leftArm.style.position = 'absolute';
                leftArm.style.left = '-8px';
                leftArm.style.top = '0';
                leftArm.style.width = '8px';
                leftArm.style.height = '100%';
                leftArm.style.backgroundColor = shapes[clipboard.shape].bg;
                leftArm.style.border = '2px solid #333';
                leftArm.style.borderRadius = '8px 0 0 8px';
                newItem.appendChild(leftArm);
                
                const rightArm = document.createElement('div');
                rightArm.style.position = 'absolute';
                rightArm.style.right = '-8px';
                rightArm.style.top = '0';
                rightArm.style.width = '8px';
                rightArm.style.height = '100%';
                rightArm.style.backgroundColor = shapes[clipboard.shape].bg;
                rightArm.style.border = '2px solid #333';
                rightArm.style.borderRadius = '0 8px 8px 0';
                newItem.appendChild(rightArm);
                break;
            case 'door':
                // Add door handle
                const handle = document.createElement('div');
                handle.style.position = 'absolute';
                handle.style.right = '5px';
                handle.style.top = '50%';
                handle.style.transform = 'translateY(-50%)';
                handle.style.width = '8px';
                handle.style.height = '8px';
                handle.style.backgroundColor = '#FFD700';
                handle.style.border = '1px solid #333';
                handle.style.borderRadius = '50%';
                newItem.appendChild(handle);
                break;
            case 'window':
                // Add window frame
                newItem.style.border = '3px solid #1F2937';
                const windowPane = document.createElement('div');
                windowPane.style.position = 'absolute';
                windowPane.style.top = '3px';
                windowPane.style.left = '3px';
                windowPane.style.right = '3px';
                windowPane.style.bottom = '3px';
                windowPane.style.backgroundColor = '#E0F2FE';
                windowPane.style.border = '1px solid #1F2937';
                newItem.appendChild(windowPane);
                break;
            case 'sink':
                // Add sink basin
                const basin = document.createElement('div');
                basin.style.position = 'absolute';
                basin.style.top = '5px';
                basin.style.left = '5px';
                basin.style.right = '5px';
                basin.style.bottom = '5px';
                basin.style.backgroundColor = '#E5E7EB';
                basin.style.border = '2px solid #6B7280';
                basin.style.borderRadius = '50%';
                newItem.appendChild(basin);
                break;
            case 'refrigerator':
                // Add refrigerator door
                const fridgeDoor = document.createElement('div');
                fridgeDoor.style.position = 'absolute';
                fridgeDoor.style.top = '0';
                fridgeDoor.style.left = '0';
                fridgeDoor.style.right = '0';
                fridgeDoor.style.bottom = '0';
                fridgeDoor.style.backgroundColor = '#F3F4F6';
                fridgeDoor.style.border = '2px solid #374151';
                newItem.appendChild(fridgeDoor);
                
                // Add handle
                const fridgeHandle = document.createElement('div');
                fridgeHandle.style.position = 'absolute';
                fridgeHandle.style.right = '3px';
                fridgeHandle.style.top = '50%';
                fridgeHandle.style.transform = 'translateY(-50%)';
                fridgeHandle.style.width = '4px';
                fridgeHandle.style.height = '20px';
                fridgeHandle.style.backgroundColor = '#6B7280';
                fridgeHandle.style.border = '1px solid #374151';
                newItem.appendChild(fridgeHandle);
                break;
            case 'toilet':
                // Add toilet seat
                const seat = document.createElement('div');
                seat.style.position = 'absolute';
                seat.style.top = '5px';
                seat.style.left = '5px';
                seat.style.right = '5px';
                seat.style.bottom = '15px';
                seat.style.backgroundColor = '#FFFFFF';
                seat.style.border = '2px solid #6B7280';
                seat.style.borderRadius = '50% 50% 0 0';
                newItem.appendChild(seat);
                break;
            case 'plant':
                // Add plant pot
                const pot = document.createElement('div');
                pot.style.position = 'absolute';
                pot.style.bottom = '0';
                pot.style.left = '50%';
                pot.style.transform = 'translateX(-50%)';
                pot.style.width = '20px';
                pot.style.height = '10px';
                pot.style.backgroundColor = '#8B4513';
                pot.style.border = '1px solid #654321';
                pot.style.borderRadius = '0 0 10px 10px';
                newItem.appendChild(pot);
                break;
            case 'lamp':
                // Add lamp shade
                const shade = document.createElement('div');
                shade.style.position = 'absolute';
                shade.style.top = '-10px';
                shade.style.left = '50%';
                shade.style.transform = 'translateX(-50%)';
                shade.style.width = '25px';
                shade.style.height = '15px';
                shade.style.backgroundColor = '#FEF3C7';
                shade.style.border = '2px solid #F59E0B';
                shade.style.borderRadius = '50% 50% 0 0';
                newItem.appendChild(shade);
                break;
        }
        
        // Add label
        const pasteItemLabel = document.createElement('div');
        pasteItemLabel.className = 'canvas-item-label'; // Added class for easy selection
        pasteItemLabel.style.position = 'absolute';
        pasteItemLabel.style.bottom = '-20px';
        pasteItemLabel.style.left = '50%';
        pasteItemLabel.style.transform = 'translateX(-50%)';
        pasteItemLabel.style.fontSize = '10px';
        pasteItemLabel.style.color = '#374151';
        pasteItemLabel.style.fontWeight = 'bold';
        pasteItemLabel.style.textAlign = 'center';
        pasteItemLabel.style.whiteSpace = 'nowrap';
        pasteItemLabel.textContent = clipboard.label;
        newItem.appendChild(pasteItemLabel);
        
        // Add delete button
        const deleteBtn = document.createElement('div');
        deleteBtn.className = 'delete-btn';
        deleteBtn.innerHTML = '×';
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            newItem.remove();
        });
        newItem.appendChild(deleteBtn);
        
        // Add rotate button
        const rotateBtn = document.createElement('div');
        rotateBtn.className = 'rotate-btn';
        rotateBtn.innerHTML = '↻';
        rotateBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            rotateItem(newItem);
        });
        newItem.appendChild(rotateBtn);
        
        // Add copy button
        const copyBtn = document.createElement('div');
        copyBtn.className = 'copy-btn';
        copyBtn.innerHTML = '📋';
        copyBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            copyItem(newItem);
        });
        newItem.appendChild(copyBtn);
        
        // Add resize handles
        const resizeHandles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'w', 'e'];
        resizeHandles.forEach(handle => {
            const resizeHandle = document.createElement('div');
            resizeHandle.className = `resize-handle ${handle}`;
            resizeHandle.dataset.handle = handle;
            resizeHandle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                startResizing(e, newItem, handle);
            });
            newItem.appendChild(resizeHandle);
        });
        
        // Add click handler for selection
        newItem.addEventListener('click', function(e) {
            e.stopPropagation();
            selectItem(newItem);
        });
        
        // Add drag functionality for moving items
        newItem.addEventListener('mousedown', startDragging);
        
        // Add to canvas
        canvasItems.appendChild(newItem);
        
        console.log('Item pasted to canvas:', newItem);
        console.log('Final position:', newItem.style.left, newItem.style.top);
        
        // Force a visual update
        newItem.style.display = 'block';
        newItem.style.visibility = 'visible';
        newItem.style.opacity = '1';
    }
    
    // Drawing wall functionality
    let isDrawingWall = false;
    let drawingWallItem = null;
    let drawingStartX = 0;
    let drawingStartY = 0;
    
    function startDrawingWall(e, item) {
        isDrawingWall = true;
        drawingWallItem = item;
        
        const canvasRect = canvas.getBoundingClientRect();
        drawingStartX = e.clientX - canvasRect.left;
        drawingStartY = e.clientY - canvasRect.top;
        
        // Store original position
        drawingWallItem.dataset.originalX = drawingWallItem.style.left;
        drawingWallItem.dataset.originalY = drawingWallItem.style.top;
        drawingWallItem.dataset.originalWidth = drawingWallItem.style.width;
        
        // Add drawing class for visual feedback
        drawingWallItem.classList.add('drawing-wall-active');
        drawingWallItem.style.zIndex = '1000';
        
        document.addEventListener('mousemove', handleDrawingWall);
        document.addEventListener('mouseup', stopDrawingWall);
        
        e.preventDefault();
    }
    
    function handleDrawingWall(e) {
        if (!isDrawingWall || !drawingWallItem) return;
        
        const canvasRect = canvas.getBoundingClientRect();
        const mouseX = e.clientX - canvasRect.left;
        const mouseY = e.clientY - canvasRect.top;
        
        // Calculate wall length and angle
        const deltaX = mouseX - drawingStartX;
        const deltaY = mouseY - drawingStartY;
        const length = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        const angle = Math.atan2(deltaY, deltaX) * 180 / Math.PI;
        
        // Set wall dimensions
        const minLength = 20;
        const wallLength = Math.max(minLength, length);
        
        // Update wall appearance
        drawingWallItem.style.width = wallLength + 'px';
        drawingWallItem.style.height = '20px';
        drawingWallItem.style.transform = `rotate(${angle}deg)`;
        
        // Update wall position to maintain start point
        const originalX = parseInt(drawingWallItem.dataset.originalX);
        const originalY = parseInt(drawingWallItem.dataset.originalY);
        drawingWallItem.style.left = originalX + 'px';
        drawingWallItem.style.top = originalY + 'px';
        
        // Update stretch handle position
        const stretchHandle = drawingWallItem.querySelector('.stretch-handle');
        if (stretchHandle) {
            stretchHandle.style.left = (wallLength - 10) + 'px';
            stretchHandle.style.right = 'auto';
        }
        
        console.log('Drawing wall - Length:', wallLength, 'Angle:', angle, 'Position:', originalX, originalY);
    }
    
    function stopDrawingWall() {
        if (drawingWallItem) {
            drawingWallItem.classList.remove('drawing-wall-active');
            drawingWallItem.style.zIndex = '20';
        }
        
        isDrawingWall = false;
        drawingWallItem = null;
        
        document.removeEventListener('mousemove', handleDrawingWall);
        document.removeEventListener('mouseup', stopDrawingWall);
    }
    
    // Delete item function
    function deleteItem(item) {
        if (item && item.parentNode) {
            // Remove from canvas
            item.parentNode.removeChild(item);
            
            // Clear selection if this was the selected item
            if (selectedItem === item) {
                selectedItem = null;
                hideSelectionControls();
            }
            
            console.log('Item deleted:', item);
        }
    }
    
    // Load saved floor plan on page load (only once)
    if (!window.floorPlanLoaded) {
        setTimeout(() => {
            loadFloorPlan();
            window.floorPlanLoaded = true;
        }, 100);
    }
});
</script>

<!-- Save Success Modal -->
<div id="save-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-96 mx-4">
        <div class="p-6 text-center">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Floor Plan Saved!</h3>
            <p class="text-base text-gray-600 mb-6">Your work has been successfully preserved.</p>
            <button id="save-modal-ok-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-blue-700 transition-colors">
                OK
            </button>
        </div>
    </div>
</div>

<script>
// Modal functions for save confirmation
function showSaveModal() {
    document.getElementById('save-modal').classList.remove('hidden');
}

function hideSaveModal() {
    document.getElementById('save-modal').classList.add('hidden');
}

// Modal event listener
document.getElementById('save-modal-ok-btn').addEventListener('click', function() {
    hideSaveModal();
});
</script>
@endsection 