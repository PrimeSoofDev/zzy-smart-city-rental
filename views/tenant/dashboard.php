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
                            <span class="text-xl font-extrabold text-blue-600">$<?= number_format($p['price'], 2) ?></span>
                            <a href="<?= APP_URL ?>/tenant/request-rental?id=<?= $p['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700 transition">Request</a>
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
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&libraries=places"></script>
<script>
    let map;
    let markers = [];
    let autocomplete;

    function initMap() {
        // Default center (Example: New York)
        const defaultCenter = { lat: 40.7128, lng: -74.0060 };

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: defaultCenter,
            mapTypeControl: false,
            streetViewControl: false
        });

        // Setup Autocomplete
        const input = document.getElementById('map-search');
        autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            map.setCenter(place.geometry.location);
            map.setZoom(14);
            updateProperties();
        });

        document.getElementById('search-btn').addEventListener('click', () => {
            const query = input.value;
            if (!query) return;

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: query }, (results, status) => {
                if (status === 'OK') {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(14);
                    updateProperties();
                }
            });
        });

        // Initial load: only update if we have a specific search or center.
        // Instead of immediately calling updateProperties() which filters by a default center,
        // we let the initial PHP-rendered list stay until the user moves the map or searches.
        // map.addListener('bounds_changed', () => {
        //     updateProperties();
        // });

        // To prevent the list from disappearing immediately on load,
        // we only trigger updateProperties on intentional movement or search.
        map.addListener('dragend', () => {
            updateProperties();
        });

        map.addListener('zoom_changed', () => {
            updateProperties();
        });
    }

    async function updateProperties() {
        const bounds = map.getBounds();
        const north = bounds.getNorthEast().lat();
        const south = bounds.getSouthWest().lat();
        const east = bounds.getNorthEast().lng();
        const west = bounds.getSouthWest().lng();

        try {
            const response = await fetch(`<?= APP_URL ?>/property/searchMap?north=${north}&south=${south}&east=${east}&west=${west}`);
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
        markers.forEach(m => m.setMap(null));
        markers = [];

        if (properties.length === 0) {
            listContainer.innerHTML = '<div class="text-center py-12 bg-white rounded-2xl shadow-sm border"><p class="text-gray-500">No properties found in this area.</p></div>';
            return;
        }

        // Render List
        listContainer.innerHTML = properties.map(p => `
            <div class="property-item bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition p-4" data-id="${p.id}">
                <h3 class="text-lg font-bold mb-1">${p.title}</h3>
                <p class="text-gray-600 text-sm mb-2 line-clamp-2">${p.description}</p>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xl font-extrabold text-blue-600">$${parseFloat(p.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
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
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(p.latitude), lng: parseFloat(p.longitude) },
                    map: map,
                    title: p.title
                });
                markers.push(marker);
            }
        });
    }

    window.onload = initMap;
</script>


