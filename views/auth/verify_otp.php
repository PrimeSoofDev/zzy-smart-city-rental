<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - ZZY Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .otp-input:focus {
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-3xl shadow-xl shadow-blue-200 mb-6">
                <i class="fas fa-shield-halved text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Identity Verification</h1>
            <p class="text-gray-500 mt-2">We've sent a 6-digit code to your registered identifier. Please enter it below to proceed.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 border border-gray-100 p-10">
            <div id="otp-container" class="flex justify-between gap-2 mb-8">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
                <input type="text" maxlength="1" class="otp-input w-12 h-16 text-center text-2xl font-bold bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition-all">
            </div>

            <button id="verify-btn" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-bold text-lg hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center justify-center gap-3">
                <span>Verify & Login</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </button>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Didn't receive the code? 
                    <button class="text-blue-600 font-bold hover:underline">Resend OTP</button>
                </p>
                <a href="<?= APP_URL ?>/auth/logout" class="inline-block mt-6 text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors">
                    Back to Login
                </a>
            </div>
        </div>
        
        <p class="text-center mt-10 text-xs text-gray-400 font-medium tracking-widest uppercase">Secured by ZZY Security</p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const verifyBtn = document.getElementById('verify-btn');

        // Auto-focus logic
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        verifyBtn.addEventListener('click', async () => {
            const otp = Array.from(inputs).map(i => i.value).join('');
            if (otp.length < 6) return alert('Please enter all 6 digits');

            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            verifyBtn.disabled = true;

            try {
                const response = await fetch('<?= APP_URL ?>/auth/verify-otp-submit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ otp })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    window.location.href = result.redirect;
                } else {
                    alert(result.message || 'Verification failed');
                    verifyBtn.innerHTML = '<span>Verify & Login</span><i class="fas fa-arrow-right text-sm"></i>';
                    verifyBtn.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please try again.');
                verifyBtn.innerHTML = '<span>Verify & Login</span><i class="fas fa-arrow-right text-sm"></i>';
                verifyBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
