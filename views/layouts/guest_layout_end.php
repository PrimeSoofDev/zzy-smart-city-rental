    <!-- FOOTER -->
    <footer class="py-20 border-t border-slate-100 px-6 bg-slate-50">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black">Z</div>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">ZZY RENTAL</span>
                </div>
                <p class="text-slate-500 max-w-sm leading-relaxed mb-8">
                    Revolutionizing the rental market with artificial intelligence. Secure, transparent, and built for the modern tenant.
                </p>
                <div class="flex gap-4">
                    <?php if($fb = SiteSetting::get('social_facebook')): ?>
                    <a href="<?= $fb ?>" target="_blank" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if($tw = SiteSetting::get('social_twitter')): ?>
                    <a href="<?= $tw ?>" target="_blank" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if($ig = SiteSetting::get('social_instagram')): ?>
                    <a href="<?= $ig ?>" target="_blank" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if($li = SiteSetting::get('social_linkedin')): ?>
                    <a href="<?= $li ?>" target="_blank" class="w-12 h-12 bg-white border border-slate-100 rounded-2xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all"><i class="fab fa-linkedin"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <h5 class="text-slate-900 font-bold mb-6 uppercase text-xs tracking-widest">Platform</h5>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a href="<?= APP_URL ?>/find-homes" class="hover:text-blue-600 transition-colors">Find a Home</a></li>
                    <li><a href="<?= APP_URL ?>/how-it-works" class="hover:text-blue-600 transition-colors">How it Works</a></li>
                    <li><a href="<?= APP_URL ?>/support" class="hover:text-blue-600 transition-colors">Safety</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Escrow Protection</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-slate-900 font-bold mb-6 uppercase text-xs tracking-widest">Company</h5>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li><a href="#" class="hover:text-blue-600 transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Careers</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-blue-600 transition-colors">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-slate-100 text-center text-xs text-slate-400 font-bold uppercase tracking-widest">
            &copy; <?= date('Y') ?> ZZY Smart Rental. All Rights Reserved.
        </div>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            const inner = header.querySelector('div');
            if (window.scrollY > 20) {
                header.classList.add('nav-scrolled');
                inner.classList.replace('h-20', 'h-16');
            } else {
                header.classList.remove('nav-scrolled');
                inner.classList.replace('h-16', 'h-20');
            }
        });
    </script>
</body>
</html>
