<h1 class="text-3xl font-bold text-gray-800 mb-8">System Audit Logs</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Security & Action Event Stream</h3>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700">Download Log Archive</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Entity</th>
                    <th class="px-6 py-4">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-gray-500">2026-04-22 14:22:01</td>
                    <td class="px-6 py-4 text-sm font-medium">Admin</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">User Update</span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600">User ID: 14</td>
                    <td class="px-6 py-4 text-xs font-mono text-gray-400">192.168.1.1</td>
                </tr>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-gray-500">2026-04-22 12:10:45</td>
                    <td class="px-6 py-4 text-sm font-medium">John_Landlord</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Property Create</span>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600">Prop ID: 882</td>
                    <td class="px-6 py-4 text-xs font-mono text-gray-400">102.12.44.11</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
