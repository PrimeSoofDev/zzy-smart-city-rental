<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-lg mt-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Create Account</h2>
    <form action="<?= APP_URL ?>/auth/signup" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-semibold mb-1">Username</label>
            <input type="text" name="username" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Email Address</label>
            <input type="email" name="email" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Password</label>
            <input type="password" name="password" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">I am a:</label>
            <select name="role" class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="Tenant">Tenant</option>
                <option value="Landlord">Landlord</option>
            </select>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">Sign Up</button>
    </form>
</div>
