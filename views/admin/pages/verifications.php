<<hh1 class="text-3xl font-bold text-gray-800 mb-8">Verification Center</h1>

<<divdiv class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <<divdiv class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <<divdiv class="p-6 border-b border-gray-100 flex justify-between items-center">
            <<hh3 class="font-bold text-gray-800">Tenant KYCs</h3>
            <<spanspan class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold"><?php echo count($tenants); ?> Pending</span>
        </div>
        <<divdiv class="p-6 space-y-4">
            <?php foreach ($tenants as $tenant): ?>
            <<divdiv class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-300 transition-all group">
                <<divdiv class="flex items-center gap-3">
                    <<divdiv class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <<ii class="fas fa-user text-gray-400"></i>
                    </div>
                    <div>
                        <<pp class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($tenant['username']); ?></p>
                        <<pp class="text-[10px] text-gray-400 uppercase">Pending Verification</p>
                    </div>
                </div>
                <<formform action="admin/approve-tenant" method="POST" class="m-0">
                    <<inputinput type="hidden" name="user_id" value="<?php echo $tenant['user_id']; ?>">
                    <<buttonbutton class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">Verify Now</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <<divdiv class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <<divdiv class="p-6 border-b border-gray-100 flex justify-between items-center">
            <<hh3 class="font-bold text-gray-800">Landlord KYCs</h3>
            <<spanspan class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold"><?php echo count($landlords); ?> Pending</span>
        </div>
        <<divdiv class="p-6 space-y-4">
            <?php foreach ($landlords as $landlord): ?>
            <<divdiv class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-300 transition-all group">
                <<divdiv class="flex items-center gap-3">
                    <<divdiv class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <<ii class="fas fa-building text-gray-400"></i>
                    </div>
                    <div>
                        <<pp class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($landlord['username']); ?></p>
                        <<pp class="text-[10px] text-gray-400 uppercase">Pending Verification</p>
                    </div>
                </div>
                <<formform action="admin/approve-landlord" method="POST" class="m-0">
                    <<inputinput type="hidden" name="user_id" value="<?php echo $landlord['user_id']; ?>">
                    <<buttonbutton class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">Verify Now</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
