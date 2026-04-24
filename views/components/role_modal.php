<div id="role-modal" class="fixed inset-0 modal-overlay flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-md w-full text-center">
        <h2 class="text-3xl font-bold mb-4">Welcome to ZZY Rental</h2>
        <p class="text-gray-600 mb-8">Please let us know your role to tailor your experience.</p>
        <div class="flex flex-col gap-4">
            <a href="<?= APP_URL ?>/auth/signup?role=Tenant" class="bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition">I am a Tenant</a>
            <a href="<?= APP_URL ?>/auth/signup?role=Landlord" class="bg-green-600 text-white py-3 rounded-xl font-bold hover:bg-green-700 transition">I am a Landlord</a>
        </div>
    </div>
</div>
