<?php 
$title = 'Support';
include '../views/layouts/guest_layout_start.php'; 
?>

    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">
                <div>
                    <h1 class="text-6xl font-black text-slate-900 mb-8 tracking-tighter"><?= $content['intro']['title'] ?? "We're here to <br><span class='text-gradient'>Help</span>." ?></h1>
                    <p class="text-lg text-slate-500 mb-12 max-w-lg"><?= $content['intro']['subtitle'] ?? "Whether you're a tenant looking for a home or a landlord needing assistance with a listing, our team is ready to support you." ?></p>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6 items-start">
                            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Email Us</h4>
                                <p class="text-slate-500">support@zzyrental.com</p>
                            </div>
                        </div>
                        <div class="flex gap-6 items-start">
                            <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 shrink-0">
                                <i class="fas fa-phone-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Call Us</h4>
                                <p class="text-slate-500">+234 800 ZZY RENT</p>
                            </div>
                        </div>
                        <div class="flex gap-6 items-start">
                            <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-600 shrink-0">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900 mb-1">Office</h4>
                                <p class="text-slate-500">Victoria Island, Lagos, Nigeria</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-12 rounded-[3rem] border border-slate-100 shadow-2xl">
                    <h3 class="text-2xl font-black text-slate-900 mb-8">Send a Message</h3>
                    <form class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">First Name</label>
                                <input type="text" class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 outline-none focus:border-blue-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Last Name</label>
                                <input type="text" class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 outline-none focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                            <input type="email" class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 outline-none focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Message</label>
                            <textarea rows="4" class="w-full bg-white border border-slate-100 rounded-xl px-4 py-3 outline-none focus:border-blue-500 transition-colors"></textarea>
                        </div>
                        <button class="w-full py-4 bg-blue-600 text-white font-black rounded-xl shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?php include '../views/layouts/guest_layout_end.php'; ?>
