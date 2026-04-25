<div class="min-h-screen bg-gray-50 py-12 px-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Property</h1>
                <p class="text-gray-500">List your property on the marketplace</p>
            </div>
            <a href="<?= APP_URL ?>/landlord/dashboard" class="text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Side -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <form action="<?= APP_URL ?>/landlord/save-property" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Property Title</label>
                                <input type="text" name="title" required placeholder="e.g. Luxury 3-Bed Flat in Lekki" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                                <textarea name="description" required rows="4" placeholder="Describe the property, amenities, and rules..." class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Price (per Month)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">₦</span>
                                    <input type="number" name="price" step="0.01" required class="w-full pl-8 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Property Type</label>
                                <select name="property_type" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="apartment">Apartment</option>
                                    <option value="house">House</option>
                                    <option value="commercial">Commercial</option>
                                    <option value="land">Land</option>
                                </select>
                            </div>
                        </div>

                        <!-- Map Integration Area -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">Precise Location (Click on Map)</label>
                            <div id="map" class="w-full h-80 rounded-2xl border-2 border-gray-200 shadow-inner bg-gray-100 overflow-hidden">
                                <!-- Leaflet Map injected here -->
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase text-gray-400">Address</label>
                                    <input type="text" id="address" name="address" readonly class="w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm italic outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase text-gray-400">Latitude</label>
                                    <input type="text" id="lat" name="latitude" readonly class="w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase text-gray-400">Longitude</label>
                                    <input type="text" id="lng" name="longitude" readonly class="w-full px-4 py-2 bg-gray-50 border rounded-lg text-sm outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                                List Property Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Image Upload Side -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Property Photos</h3>
                    <div class="space-y-4">
                        <div class="relative border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 transition-all cursor-pointer bg-gray-50">
                            <input type="file" name="images[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImages(this)">
                            <i class="fas fa-images text-gray-400 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500">Upload high-quality photos</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 5MB each</p>
                        </div>
                        <div id="image-preview" class="grid grid-cols-3 gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([6.5244, 3.3792], 13); // Default Lagos
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker;
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        if (marker) marker.setLatLng(e.latlng);
        else marker = L.marker(e.latlng).addTo(map);

        document.getElementById('lat').value = lat.toFixed(8);
        document.getElementById('lng').value = lng.toFixed(8);

        // Reverse Geocoding using Nominatim
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('address').value = data.display_name || 'Unknown Address';
            });
    });

    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'aspect-square rounded-lg bg-cover bg-center border border-gray-200';
                    div.style.backgroundImage = `url(${e.target.result})`;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
