<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location - PWESTO!</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5faff;
        }
        
        .header {
            background: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #19c2b8;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #19c2b8;
        }
        
        .nav-links a.active {
            color: #19c2b8;
            text-decoration: underline;
        }
        
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .location-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .location-title {
            font-size: 3rem;
            font-weight: bold;
            color: #222;
            margin-bottom: 1rem;
        }
        
        .location-subtitle {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .maps-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 2rem;
            height: 600px;
            position: relative;
        }
        
        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .custom-marker {
            background: transparent !important;
            border: none !important;
            cursor: move !important;
        }

        .custom-marker:hover {
            transform: scale(1.1);
            transition: transform 0.2s;
        }

        /* Make marker cursor indicate it's draggable */
        .leaflet-marker-draggable {
            cursor: move !important;
        }
        
        .location-info {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: #19c2b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .info-text h3 {
            margin: 0 0 0.5rem 0;
            color: #222;
            font-size: 1.1rem;
        }
        
        .info-text p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .cta-section {
            text-align: center;
            background: #19c2b8;
            color: white;
            padding: 3rem 2rem;
            border-radius: 12px;
            margin: 2rem 0;
        }
        
        .cta-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .cta-button {
            background: white;
            color: #19c2b8;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                gap: 1rem;
            }
            
            .location-title {
                font-size: 2rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .maps-container {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="nav-container">
            <a href="{{ route('welcome') }}" class="logo">PWESTO!</a>
            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('dashboard') }}">Home</a></li>
                    <li><a href="{{ route('booking-history') }}">Booking History</a></li>
                    <li><a href="{{ route('services.index') }}">Services</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('location') }}" class="active">Location</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="location-header">
            <h1 class="location-title">Workspaces Location</h1>
            <p class="location-subtitle">Find here in the map and discover the amazing workspaces</p>
        </div>

        <div class="maps-container">
            <!-- Leaflet Map Container -->
            <div id="map"></div>
            
            <!-- Interactive Workspace Selector Overlay -->
            <div style="position: absolute; top: 20px; right: 20px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-width: 300px; z-index: 1000;">
                <h3 style="margin: 0 0 15px 0; color: #222; font-size: 18px;">Select Workspace</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <button onclick="showWorkspaceInfo('produktiv-osmena'); focusMarker('produktiv-osmena');" onmouseover="this.style.background='#f0fdfd'; this.style.transform='translateX(-2px)';" onmouseout="this.style.background='white'; this.style.transform='translateX(0)';" style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 2px solid #19c2b8; background: white; border-radius: 8px; cursor: pointer; text-align: left; transition: all 0.2s;">
                        <div style="width: 20px; height: 20px; background: #19c2b8; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">P</div>
                        <div>
                            <div style="font-weight: 600; color: #222;">Produktiv - Osmeña</div>
                            <div style="font-size: 12px; color: #666;">Osmeña Blvd, Cebu City</div>
                        </div>
                    </button>
                    
                    <button onclick="showWorkspaceInfo('nest-itpark'); focusMarker('nest-itpark');" onmouseover="this.style.background='#fff5f5'; this.style.transform='translateX(-2px)';" onmouseout="this.style.background='white'; this.style.transform='translateX(0)';" style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 2px solid #ff6b6b; background: white; border-radius: 8px; cursor: pointer; text-align: left; transition: all 0.2s;">
                        <div style="width: 20px; height: 20px; background: #ff6b6b; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">N</div>
                        <div>
                            <div style="font-weight: 600; color: #222;">Nest - IT Park</div>
                            <div style="font-size: 12px; color: #666;">Mango Avenue</div>
                        </div>
                    </button>
                    
                    <button onclick="showWorkspaceInfo('mesh-ayala'); focusMarker('mesh-ayala');" onmouseover="this.style.background='#f0fdfc'; this.style.transform='translateX(-2px)';" onmouseout="this.style.background='white'; this.style.transform='translateX(0)';" style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 2px solid #4ecdc4; background: white; border-radius: 8px; cursor: pointer; text-align: left; transition: all 0.2s;">
                        <div style="width: 20px; height: 20px; background: #4ecdc4; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">M</div>
                        <div>
                            <div style="font-weight: 600; color: #222;">Mesh Media - Mango</div>
                            <div style="font-size: 12px; color: #666;">Ramos Street</div>
                        </div>
                    </button>
                    
                </div>
            </div>
        </div>

        <div class="location-info">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #222; font-size: 2rem;">Workspaces</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon" style="background: #19c2b8;">P</div>
                    <div class="info-text">
                        <h3>Produktiv - Osmeña Workspace</h3>
                        <p>Osmeña Blvd, Cebu City</p>
                        <p>Open 24 hours</p>
                        <a href="{{ route('services.booking') }}" style="color: #19c2b8; text-decoration: none; font-weight: 600;">Book Now →</a>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon" style="background: #ff6b6b;">N</div>
                    <div class="info-text">
                        <h3>Nest - IT Park Workspace</h3>
                        <p>Basement 1, Horizons 101, General Maxilom Avenue, Cebu City.</p>
                        <p>Open 24 hours</p>
                        <a href="{{ route('services.nest-booking') }}" style="color: #ff6b6b; text-decoration: none; font-weight: 600;">Book Now →</a>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon" style="background: #4ecdc4;">M</div>
                    <div class="info-text">
                        <h3>Mesh Media - Ayala Workspace</h3>
                        <p>F. Ramos St. Cebu City</p>
                        <p>6:00 AM - 10:00 PM</p>
                        <span style="color: #666; font-style: italic;">Coming Soon</span>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="cta-section">
            <h2 class="cta-title">Ready to Book Your Workspace?</h2>
            <p>Experience our premium coworking spaces with all the amenities you need.</p>
            <a href="{{ route('dashboard') }}" class="cta-button">Book Now</a>
        </div>
    </main>

    <script>
        // Initialize Leaflet Map
        let map;
        let markers = {};

        // Workspace data with coordinates
        const workspaces = {
            'produktiv-osmena': {
                name: "Produktiv - Osmeña Workspace",
                address: "Osmeña Blvd, Cebu City, 6000 Cebu",
                Social Media: "https://www.facebook.com/produktivph/",
                rating: "4.9 (174 reviews)",
                hours: "Open 24 hours",
                bookingLink: "{{ route('services.booking') }}",
                lat: 10.3157,
                lng: 123.8854,
                color: '#19c2b8',
                label: 'P'
            },
            'nest-itpark': {
                name: "Nest - Workspace",
                address: "Basement 1, Horizons 101, General Maxilom Avenue, Cebu City.",
                Social Media: "https://www.facebook.com/nestworkspacesph",
                rating: "4.8 (142 reviews)",
                hours: "Open 24 hours",
                bookingLink: "{{ route('services.nest-booking') }}",
                lat: 10.3200,
                lng: 123.9000,
                color: '#ff6b6b',
                label: 'N'
            },
            'mesh-ayala': {
                name: "Mesh Media - Mango Avenue ",
                address: "F. Ramos St. Cebu City",
                Social Media: "https://www.instagram.com/meshmedia.space",
                rating: "4.7 (98 reviews)",
                hours: "6:00 AM - 10:00 PM",
                bookingLink: "#",
                lat: 10.3100,
                lng: 123.8700,
                color: '#4ecdc4',
                label: 'M'
            },
        };

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map centered on Cebu City
            map = L.map('map').setView([10.3157, 123.8854], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Add markers for each workspace
            Object.keys(workspaces).forEach(workspaceId => {
                const workspace = workspaces[workspaceId];
                
                // Create custom icon
                const customIcon = L.divIcon({
                    className: 'custom-marker',
                    html: `<div style="background-color: ${workspace.color}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">${workspace.label}</div>`,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20],
                    popupAnchor: [0, -20]
                });

                // Create draggable marker
                const marker = L.marker([workspace.lat, workspace.lng], { 
                    icon: customIcon,
                    draggable: true
                })
                    .addTo(map)
                    .bindPopup(`
                        <div style="min-width: 200px;">
                            <h3 style="margin: 0 0 10px 0; color: #222; font-size: 16px;">${workspace.name}</h3>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">${workspace.address}</p>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">${workspace.hours}</p>
                            <p style="margin: 5px 0; color: #666; font-size: 14px;">${workspace.phone}</p>
                            <p style="margin: 5px 0; color: #999; font-size: 12px; font-style: italic;"> Drag marker to reposition</p>
                            <div style="margin-top: 10px; display: flex; gap: 8px;">
                                <button onclick="getDirections('${workspace.address}')" style="background: #19c2b8; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px;">Directions</button>
                                <button onclick="bookWorkspace('${workspace.bookingLink}')" style="background: #ff6b6b; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px;">Book Now</button>
                            </div>
                        </div>
                    `);

                // Store marker reference
                markers[workspaceId] = marker;

                // Add click event to marker
                marker.on('click', function() {
                    showWorkspaceInfo(workspaceId);
                });

                // Add drag event listeners to update coordinates
                marker.on('dragstart', function() {
                    // Optional: Add visual feedback when dragging starts
                    marker.setOpacity(0.7);
                });

                marker.on('dragend', function() {
                    // Update workspace coordinates when marker is moved
                    const newPosition = marker.getLatLng();
                    workspaces[workspaceId].lat = newPosition.lat;
                    workspaces[workspaceId].lng = newPosition.lng;
                    
                    // Reset opacity
                    marker.setOpacity(1);
                    
                    // Show updated coordinates
                    console.log(`${workspace.name} moved to:`, newPosition.lat, newPosition.lng);
                    
                    // Optional: Show a notification
                    const popup = marker.getPopup();
                    if (popup) {
                        marker.openPopup();
                    }
                });
            });
        });

        // Function to focus on a specific marker
        function focusMarker(workspaceId) {
            const workspace = workspaces[workspaceId];
            if (workspace && markers[workspaceId]) {
                map.setView([workspace.lat, workspace.lng], 15);
                markers[workspaceId].openPopup();
            }
        }

        // Function to show workspace information
        function showWorkspaceInfo(workspaceId) {
            const workspace = workspaces[workspaceId];
            if (!workspace) return;

            // Create modal or alert with workspace info
            const infoHtml = `
                <div style="padding: 20px; max-width: 400px;">
                    <h3 style="margin: 0 0 15px 0; color: #222; font-size: 20px;">${workspace.name}</h3>
                    <div style="display: flex; align-items: center; margin-bottom: 10px;">
                        <span style="color: #ffa500; font-size: 16px;">★★★★★</span>
                        <span style="margin-left: 8px; color: #666; font-size: 14px;">${workspace.rating}</span>
                    </div>
                    <p style="margin: 8px 0; color: #666; font-size: 14px;"> ${workspace.address}</p>
                    <p style="margin: 8px 0; color: #666; font-size: 14px;"> ${workspace.hours}</p>
                    <p style="margin: 8px 0; color: #666; font-size: 14px;"> ${workspace.phone}</p>
                    <p style="margin: 8px 0; color: #666; font-size: 14px;"> ${workspace.website}</p>
                    <div style="margin-top: 15px; display: flex; gap: 10px;">
                        <button onclick="getDirections('${workspace.address}')" style="background: #19c2b8; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px;">Get Directions</button>
                        <button onclick="bookWorkspace('${workspace.bookingLink}')" style="background: #ff6b6b; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px;">Book Now</button>
                    </div>
                </div>
            `;

            // Create and show modal
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                background: rgba(0,0,0,0.5); display: flex; align-items: center; 
                justify-content: center; z-index: 1000;
            `;
            modal.innerHTML = `
                <div style="background: white; border-radius: 12px; max-width: 500px; margin: 20px; position: relative;">
                    <button onclick="this.closest('.modal').remove()" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
                    ${infoHtml}
                </div>
            `;
            modal.className = 'modal';
            document.body.appendChild(modal);

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        // Function to get directions for any workspace
        function getDirections(address) {
            const url = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(address)}`;
            window.open(url, '_blank');
        }

        // Function to book a workspace
        function bookWorkspace(bookingLink) {
            if (bookingLink && bookingLink !== '#') {
                window.location.href = bookingLink;
            } else {
                alert('Booking for this workspace is coming soon!');
            }
        }
    </script>
</body>
</html>
