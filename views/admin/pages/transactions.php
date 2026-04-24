<h1 class="text-3xl font-bold text-gray-800 mb-8">Financial Overview</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Escrow Balance</p>
        <p class="text-3xl font-extrabold text-gray-900">$452,000.00</p>
        <div class="mt-2 text-green-600 text-xs font-bold">↑ 12% from last month</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Monthly Revenue</p>
        <p class="text-3xl font-extrabold text-gray-900">$12,450.00</p>
        <div class="mt-2 text-green-600 text-xs font-bold">↑ 8.2% increase</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Failed Payments</p>
        <p class="text-3xl font-extrabold text-red-600">14</p>
        <div class="mt-2 text-red-600 text-xs font-bold">Requires Attention</div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Transaction Ledger</h3>
        <div class="flex gap-2">
             <input type="text" placeholder="Search TxID..." class="px-3 py-1.5 border rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none">
             <button class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-200">Filter</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">TX-99283401</td>
                    <td class="px-6 py-4 text-sm text-gray-800">Michael Brown</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">Escrow Deposit</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">$1,200.00</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Completed</span>
                    </td>
                    <td class="px-6 py-4 text-right text-xs text-gray-400">2026-04-20</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">TX-99283405</td>
                    <td class="px-6 py-4 text-sm text-gray-800">Sarah Smith</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 uppercase">Landlord Payout</span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">$1,150.00</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-700 uppercase">Processing</span>
                    </td>
                    <td class="px-6 py-4 text-right text-xs text-gray-400">2026-04-21</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
