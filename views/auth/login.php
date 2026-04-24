<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-lg mt-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Welcome Back</h2>
    <form action="<?= APP_URL ?>/auth/login" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-semibold mb-1">Email Address</label>
            <input type="email" name="email" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Password</label>
            <input type="password" name="password" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">Login</button>
    </form>
    <p class="mt-6 text-center text-gray-600">Don't have an account? <a href="<?= APP_URL ?>/auth/signup" class="text-blue-600 font-bold">Sign Up</a></p>
</div>
