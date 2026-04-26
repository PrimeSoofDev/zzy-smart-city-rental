<h1 class="text-3xl font-bold mb-8">Find Your Next Home</h1>



<div class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Search and Map Section -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white p-4 rounded-2xl shadow-sm border">
            <div class="flex gap-2">
                <input type="text" id="map-search" placeholder="Search for a location..." class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <button id="search-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Search</button>
            </div>
        </div>
        <div id="map" class="h-[400px] w-full rounded-2xl shadow-sm border overflow-hidden"></div>
    </div>

    <!-- Property List Section -->
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-gray-700">Available Properties</h2>
        <div id="property-list" class="grid grid-cols-1 gap-4 overflow-y-auto max-h-[480px] pr-2">
            <?php if(empty($properties)): ?>
                <div class="text-center py-12 bg-white rounded-2xl shadow-sm border">
                    <p class="text-gray-500">No approved properties available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach($properties as $p): ?>
                    <div class="property-item bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition p-4" data-id="<?= $p['id'] ?>">
                        <h3 class="text-lg font-bold mb-1"><?= $p['title'] ?></h3>
                        <p class="text-gray-600 text-sm mb-2 line-clamp-2"><?= $p['description'] ?></p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xl font-extrabold text-blue-600">₦<?= number_format($p['price'], 2) ?></span>
                            <a href="<?= APP_URL ?>/tenant/property?id=<?= $p['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700 transition">View Details</a>
                        </div>
                        <div class="flex gap-3 text-[10px] text-gray-500 font-medium">
                            <span>🛏️ <?= $p['rooms'] ?> Rooms</span>
                            <span>🚿 <?= $p['bathrooms'] ?> Baths</span>
                            <span>📍 <?= $p['address'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map;
    let markers = [];
    
    function initMap() {
        // Initial properties from PHP
        const initialProperties = <?= json_encode($properties) ?>;
        
        // Default center: Port Harcourt, Rivers State
        const ph = [4.8156, 7.0498];
        
        map = L.map('map', {
            zoomControl: false
        }).setView(ph, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        // Render initial properties and markers
        if (initialProperties.length > 0) {
            renderProperties(initialProperties);
            
            // Fit map to markers if we have any with coords
            const markersWithCoords = initialProperties.filter(p => p.latitude && p.longitude);
            if (markersWithCoords.length > 0) {
                const group = new L.featureGroup(markers.filter(m => m.getLatLng()));
                if (group.getBounds().isValid()) {
                    map.fitBounds(group.getBounds(), { padding: [50, 50] });
                }
            }
        }

        // Map movement events - only after initial load to allow user to explore
        map.on('moveend', updateProperties);

        // Search Button Logic
        document.getElementById('search-btn').addEventListener('click', async () => {
            const query = document.getElementById('map-search').value;
            if (!query) return;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.length > 0) {
                    const { lat, lon } = data[0];
                    map.setView([lat, lon], 14);
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        });

        // Search on Enter
        document.getElementById('map-search').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') document.getElementById('search-btn').click();
        });
    }

    async function updateProperties() {
        const bounds = map.getBounds();
        const north = bounds.getNorth();
        const south = bounds.getSouth();
        const east = bounds.getEast();
        const west = bounds.getWest();
        const query = document.getElementById('map-search').value;

        try {
            const response = await fetch(`<?= APP_URL ?>/property/searchMap?north=${north}&south=${south}&east=${east}&west=${west}&q=${encodeURIComponent(query)}`);
            const properties = await response.json();

            if (properties.error) throw new Error(properties.error);

            renderProperties(properties);
        } catch (error) {
            console.error('Error fetching properties:', error);
        }
    }

    function renderProperties(properties) {
        const listContainer = document.getElementById('property-list');

        // Clear existing markers
        markers.forEach(m => map.removeLayer(m));
        markers = [];

        if (properties.length === 0) {
            listContainer.innerHTML = '<div class="text-center py-12 bg-white rounded-2xl shadow-sm border"><p class="text-gray-500">No properties found in this area.</p></div>';
            return;
        }

        // Render List
        listContainer.innerHTML = properties.map(p => `
            <div class="property-item bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition p-4 cursor-pointer" onclick="focusProperty(${p.latitude}, ${p.longitude})">
                <h3 class="text-lg font-bold mb-1">${p.title}</h3>
                <p class="text-gray-600 text-sm mb-2 line-clamp-2">${p.description}</p>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xl font-extrabold text-blue-600">₦${parseFloat(p.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                    <a href="<?= APP_URL ?>/tenant/property?id=${p.id}" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700 transition">View Details</a>
                </div>
                <div class="flex gap-3 text-[10px] text-gray-500 font-medium">
                    <span>🛏️ ${p.rooms} Rooms</span>
                    <span>🚿 ${p.bathrooms} Baths</span>
                    <span>📍 ${p.address}</span>
                </div>
            </div>
        `).join('');

        // Add Markers
        properties.forEach(p => {
            if (p.latitude && p.longitude) {
                const marker = L.marker([parseFloat(p.latitude), parseFloat(p.longitude)], {
                    icon: L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background-color: #2563eb; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    })
                }).addTo(map);

                marker.bindPopup(`
                    <div class="p-2" style="min-width: 150px;">
                        <h4 class="font-bold text-gray-900 mb-1" style="margin: 0;">${p.title}</h4>
                        <p class="text-blue-600 font-bold mb-2">₦${parseFloat(p.price).toLocaleString()}</p>
                        <a href="<?= APP_URL ?>/tenant/property?id=${p.id}" class="text-xs bg-blue-600 text-white px-2 py-1 rounded block text-center no-underline hover:bg-blue-700">View Details</a>
                    </div>
                `, {
                    closeButton: false,
                    className: 'custom-popup'
                });

                markers.push(marker);
            }
        });
    }

    function focusProperty(lat, lng) {
        map.setView([lat, lng], 16, { animate: true });
    }

    window.onload = initMap;
</script>

<style>
    .leaflet-container { font-family: inherit; z-index: 1; }
    .custom-popup .leaflet-popup-content-wrapper { border-radius: 12px; padding: 0; overflow: hidden; }
    .custom-popup .leaflet-popup-content { margin: 8px; }
    .leaflet-bar { border: none !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important; }
    .leaflet-bar a { background-color: white !important; color: #64748b !important; border: 1px solid #f1f5f9 !important; }
    .leaflet-bar a:hover { background-color: #f8fafc !important; color: #1e293b !important; }
</style>
