<div class="max-w-6xl mx-auto px-4 py-12">
    <!-- Header -->
    <div class="flex justify-between items-end mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">List Your Property</h1>
            <p class="text-slate-500 font-medium">Reach thousands of verified tenants in minutes.</p>
        </div>
        <a href="<?= APP_URL ?>/landlord/dashboard" class="group flex items-center gap-2 text-slate-400 hover:text-slate-900 font-bold transition-all">
            <i class="fas fa-long-arrow-alt-left group-hover:-translate-x-1 transition-transform"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
        <div class="flex flex-col lg:flex-row">
            <!-- Sidebar / Guide -->
            <div class="lg:w-1/4 bg-slate-900 p-10 text-white">
                <div class="sticky top-10">
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-blue-900/50">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-black tracking-tight mb-6 leading-tight text-white">Listing Guide</h2>
                    
                    <div class="space-y-8">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-blue-400 border border-slate-700 shrink-0">1</div>
                            <div>
                                <h4 class="font-bold text-sm mb-1">Detailed Info</h4>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Accurate descriptions help tenants make faster decisions.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-blue-400 border border-slate-700 shrink-0">2</div>
                            <div>
                                <h4 class="font-bold text-sm mb-1">Clear Pricing</h4>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Set a competitive monthly rent to attract more requests.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-xs font-black text-blue-400 border border-slate-700 shrink-0">3</div>
                            <div>
                                <h4 class="font-bold text-sm mb-1">High Quality Photos</h4>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Properties with 5+ photos get 80% more engagement.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 p-6 bg-slate-800/50 rounded-2xl border border-slate-700/50">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Pro Tip</p>
                        <p class="text-xs text-slate-300 italic leading-relaxed">"Be sure to mark the exact location on the map. Verified pins build trust with tenants."</p>
                    </div>
                </div>
            </div>

            <!-- Main Form Content -->
            <div class="lg:w-3/4 p-8 md:p-16">
                <form action="<?= APP_URL ?>/landlord/save-property" method="POST" enctype="multipart/form-data" class="space-y-12">
                    
                    <!-- Basic Information Section -->
                    <section>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-2 h-8 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Basic Information</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2 group">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">Property Title</label>
                                <input type="text" name="title" required placeholder="e.g. Modern 3-Bedroom Apartment with Ocean View" 
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all">
                            </div>
                            
                            <div class="md:col-span-2 group">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">Detailed Description</label>
                                <textarea name="description" required rows="4" placeholder="Mention amenities like WiFi, security, power supply, and neighborhood highlights..." 
                                          class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all"></textarea>
                            </div>

                            <div class="group">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">Monthly Rent (₦)</label>
                                <div class="relative">
                                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₦</span>
                                    <input type="number" name="price" step="0.01" required placeholder="0.00" 
                                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-12 pr-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all">
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">Property Type</label>
                                <div class="relative">
                                    <select name="property_type" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all appearance-none">
                                        <option value="apartment">Apartment / Flat</option>
                                        <option value="house">Detached House</option>
                                        <option value="commercial">Commercial Space</option>
                                        <option value="land">Land / Plot</option>
                                    </select>
                                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Location Section -->
                    <section>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-2 h-8 bg-amber-500 rounded-full"></div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Location Details</h3>
                        </div>

                        <div class="space-y-6">
                            <div class="relative">
                                <input type="text" id="address-search" placeholder="Search for your property address..." 
                                       class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all shadow-sm">
                                <button type="button" id="search-loc-btn" class="absolute right-4 top-1/2 -translate-y-1/2 bg-blue-600 text-white px-4 py-2 rounded-xl text-xs font-black uppercase hover:bg-blue-700 transition-all">Search</button>
                            </div>
                            <div id="map" class="w-full h-96 rounded-3xl border-4 border-slate-100 shadow-inner bg-slate-50 overflow-hidden z-0"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                                <div class="md:col-span-8 group">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Detected Address</label>
                                    <input type="text" id="address" name="address" readonly 
                                           class="w-full bg-transparent text-sm font-bold text-slate-700 outline-none placeholder:italic" 
                                           placeholder="Click on the map to set address...">
                                </div>
                                <div class="md:col-span-2 group">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Latitude</label>
                                    <input type="text" id="lat" name="latitude" readonly class="w-full bg-transparent text-[10px] font-mono font-bold text-blue-600 outline-none">
                                </div>
                                <div class="md:col-span-2 group">
                                    <label class="block text-[9px] font-black uppercase text-slate-400 mb-1">Longitude</label>
                                    <input type="text" id="lng" name="longitude" readonly class="w-full bg-transparent text-[10px] font-mono font-bold text-blue-600 outline-none">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Media Upload Section -->
                    <section>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-2 h-8 bg-green-500 rounded-full"></div>
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Property Media</h3>
                        </div>

                        <div class="relative group">
                            <div class="border-4 border-dashed border-slate-100 rounded-[2rem] p-12 text-center hover:border-blue-400 hover:bg-blue-50/30 transition-all cursor-pointer group">
                                <input type="file" name="images[]" multiple accept="image/*" 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
                                       onchange="previewImages(this)">
                                <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                                </div>
                                <h4 class="text-lg font-black text-slate-900 mb-2">Upload Property Photos</h4>
                                <p class="text-sm text-slate-500 font-medium max-w-xs mx-auto">Drag and drop images here or click to browse files.</p>
                                <p class="text-[10px] text-slate-400 mt-4 uppercase font-black tracking-widest">PNG, JPG up to 5MB each</p>
                            </div>
                        </div>
                        <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-8"></div>
                    </section>

                    <!-- Submit Button -->
                    <div class="pt-12 border-t border-slate-100">
                        <button type="submit" class="w-full bg-slate-900 text-white rounded-3xl py-6 px-10 font-black uppercase tracking-widest text-lg hover:bg-blue-600 hover:scale-[1.01] active:scale-[0.99] transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-4">
                            <i class="fas fa-rocket text-xl"></i>
                            Publish Property Listing
                        </button>
                        <p class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-6">By publishing, you agree to our terms of service and property verification guidelines.</p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([4.8156, 7.0498], 13); // Default Port Harcourt
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker;
    
    // Search Location Logic
    document.getElementById('search-loc-btn').addEventListener('click', async () => {
        const query = document.getElementById('address-search').value;
        if (!query) return;

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
            const data = await response.json();
            if (data.length > 0) {
                const { lat, lon, display_name } = data[0];
                const latlng = [parseFloat(lat), parseFloat(lon)];
                
                map.setView(latlng, 16);
                if (marker) marker.setLatLng(latlng);
                else marker = L.marker(latlng).addTo(map);

                document.getElementById('lat').value = parseFloat(lat).toFixed(8);
                document.getElementById('lng').value = parseFloat(lon).toFixed(8);
                document.getElementById('address').value = display_name;
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    });

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
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative aspect-square rounded-2xl bg-cover bg-center border-2 border-slate-100 shadow-sm animate-fade-in group';
                    div.style.backgroundImage = `url(${e.target.result})`;
                    
                    // Add a label for primary image
                    if(index === 0) {
                        const badge = document.createElement('span');
                        badge.className = 'absolute top-2 left-2 bg-blue-600 text-white text-[8px] font-black uppercase px-2 py-1 rounded-lg';
                        badge.innerText = 'Main Photo';
                        div.appendChild(badge);
                    }

                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    .leaflet-container {
        font-family: inherit;
    }
    .leaflet-bar a {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;
    }
</style>
