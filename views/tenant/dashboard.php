<h1 class="text-3xl font-bold mb-8">Find Your Next Home</h1>

<?php if(!empty($escrowItems)): ?>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-shield-alt text-green-500"></i> My Secured Payments
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($escrowItems as $item): ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-green-50 text-green-600 p-3 rounded-2xl">
                            <i class="fas fa-lock text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full 
                            <?= $item['status'] == 'escrow_hold' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-blue-50 text-blue-600 border border-blue-100' ?>">
                            <?= str_replace('_', ' ', $item['status']) ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($item['property_title']) ?></h3>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-1"><?= htmlspecialchars($item['property_address']) ?></p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Secured Amount</p>
                            <p class="font-black text-gray-900">₦<?= number_format($item['amount'], 2) ?></p>
                        </div>
                        <?php if($item['status'] == 'escrow_hold'): ?>
                            <div class="text-right flex flex-col items-end gap-2">
                                <div>
                                    <span class="block text-[10px] font-black text-green-600 uppercase">Funds Secured</span>
                                    <span class="text-[9px] text-gray-400">Awaiting move-in</span>
                                </div>
                                <?php if($item['request_status'] === 'disputed'): ?>
                                    <a href="<?= APP_URL ?>/dispute/portal?request_id=<?= $item['request_id'] ?>" class="px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold hover:bg-amber-100 transition-all flex items-center gap-1">
                                        <i class="fas fa-gavel"></i> View Dispute
                                    </a>
                                <?php else: ?>
                                    <button onclick="openDisputeModal(<?= $item['request_id'] ?>, '<?= htmlspecialchars($item['property_title']) ?>')" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold hover:bg-red-100 transition-all flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i> Dispute
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

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
</div>

<!-- Dispute Modal -->
<div id="dispute-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-300">
        <div class="p-8">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mb-6">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Raise a Dispute</h3>
            <p class="text-slate-500 text-sm mb-6">Tell us why you're disputing the payment for <span id="modal-property-title" class="font-bold text-slate-900"></span>. This will freeze the funds in escrow.</p>
            
            <form action="<?= APP_URL ?>/dispute/raise" method="POST">
                <input type="hidden" name="request_id" id="modal-request-id">
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Reason for Dispute</label>
                    <textarea name="reason" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 outline-none transition-all placeholder:text-slate-300" placeholder="e.g. Property doesn't match description or landlord is unresponsive..." required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeDisputeModal()" class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-4 bg-red-600 text-white font-bold rounded-2xl shadow-lg shadow-red-600/20 hover:bg-red-700 transition-all">
                        Submit Dispute
                    </button>
                </div>
            </form>
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
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(p.latitude), lng: parseFloat(p.longitude) },
                    map: map,
                    title: p.title,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                        fillColor: "#2563eb",
                        fillOpacity: 1,
                        strokeWeight: 2,
                        strokeColor: "#ffffff",
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="p-2">
                            <h4 class="font-bold text-gray-900">${p.title}</h4>
                            <p class="text-blue-600 font-bold mb-2">₦${parseFloat(p.price).toLocaleString()}</p>
                            <a href="<?= APP_URL ?>/tenant/property?id=${p.id}" class="text-xs bg-blue-600 text-white px-2 py-1 rounded block text-center">View Details</a>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            }
        });
    }

    function openDisputeModal(requestId, title) {
        document.getElementById('modal-request-id').value = requestId;
        document.getElementById('modal-property-title').innerText = title;
        document.getElementById('dispute-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDisputeModal() {
        document.getElementById('dispute-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    window.onload = initMap;
</script>
