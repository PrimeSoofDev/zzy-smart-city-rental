<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
    <h2 class="text-3xl font-bold mb-6">List Your Property</h2>
    <form action="<?= APP_URL ?>/landlord/add-property" method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1">Property Title</label>
                <input type="text" name="title" required class="w-full p-3 border rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Price (Annual)</label>
                <input type="number" name="price" required class="w-full p-3 border rounded-xl">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Full Address</label>
            <input type="text" name="address" required class="w-full p-3 border rounded-xl">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full p-3 border rounded-xl"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1">Rooms</label>
                <input type="number" name="rooms" required class="w-full p-3 border rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Bathrooms</label>
                <input type="number" name="bathrooms" required class="w-full p-3 border rounded-xl">
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Property Type</label>
            <select name="type" class="w-full p-3 border rounded-xl bg-white">
                <option value="apartment">Apartment</option>
                <option value="house">House</option>
                <option value="commercial">Commercial</option>
                <option value="land">Land</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">Submit for Verification</button>
    </form>
</div>
