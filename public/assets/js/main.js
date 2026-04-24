document.addEventListener('DOMContentLoaded', function() {
    if (!document.cookie.includes('zzy_role_preference')) {
        const modal = document.getElementById('role-modal');
        if (modal) {
            document.cookie = 'zzy_role_preference=true; path=/; max-age=31536000';
            modal.classList.remove('hidden');
        }
    }
});
