<!-- resources/views/livewire/admin/seating-plan.blade.php -->
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Seating Plan Management</h1>
        <div class="flex space-x-4">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Create Seating Plan
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Export Plan
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam</label>
                <select class="w-full border rounded-lg px-3 py-2 bg-white">
                    <option value="">Select Exam</option>
                    <option>Annual Examination 2024</option>
                    <option>Mid-term Examination 2024</option>
                    <option>Unit Test - March 2024</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Center</label>
                <select class="w-full border rounded-lg px-3 py-2 bg-white">
                    <option value="">All Centers</option>
                    <option>Main Building - Hall A</option>
                    <option>Main Building - Hall B</option>
                    <option>Science Block - Hall C</option>
                    <option>Commerce Block - Hall D</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" class="w-full border rounded-lg px-3 py-2 bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Session</label>
                <select class="w-full border rounded-lg px-3 py-2 bg-white">
                    <option value="">All Sessions</option>
                    <option>Morning (9:00 AM - 12:00 PM)</option>
                    <option>Afternoon (2:00 PM - 5:00 PM)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Seating Plans List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Center</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Annual Examination 2024</div>
                                <div class="text-sm text-gray-500">Mathematics - Class 10</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Main Building - Hall A</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>15 Mar 2024</div>
                            <div class="text-gray-500">9:00 AM - 12:00 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">40/40 Students</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                            <button class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Annual Examination 2024</div>
                                <div class="text-sm text-gray-500">Physics - Class 12</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Science Block - Hall C</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>16 Mar 2024</div>
                            <div class="text-gray-500">9:00 AM - 12:00 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">35/35 Students</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                            <button class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Mid-term Examination 2024</div>
                                <div class="text-sm text-gray-500">English - Class 9</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Main Building - Hall B</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>20 Mar 2024</div>
                            <div class="text-gray-500">2:00 PM - 5:00 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">30/30 Students</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                            <button class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Annual Examination 2024</div>
                                <div class="text-sm text-gray-500">Accounts - Class 11</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Commerce Block - Hall D</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>18 Mar 2024</div>
                            <div class="text-gray-500">9:00 AM - 12:00 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">25/25 Students</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                            <button class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">16</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">2</a>
                        <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">3</a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Seating Plan Preview -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Seating Plan Preview - Main Building Hall A</h3>
        <div class="border-2 border-gray-300 rounded-lg p-6 bg-gray-50">
            <div class="grid grid-cols-8 gap-4 mb-6">
                <!-- Teacher's Desk -->
                <div class="col-span-8 flex justify-center mb-4">
                    <div class="bg-blue-200 p-4 rounded-lg text-center w-48">
                        <div class="font-medium">Teacher's Desk</div>
                    </div>
                </div>
                
                <!-- Seats -->
                <?php for($row = 1; $row <= 5; $row++): ?>
                    <?php for($col = 1; $col <= 8; $col++): ?>
                        <div class="bg-white border-2 border-gray-300 rounded p-3 text-center">
                            <div class="text-xs font-medium">Seat <?php echo ($row-1)*8 + $col; ?></div>
                            <div class="text-xs text-gray-500">A24<?php echo str_pad(($row-1)*8 + $col, 3, '0', STR_PAD_LEFT); ?></div>
                        </div>
                    <?php endfor; ?>
                <?php endfor; ?>
            </div>
            
            <div class="flex justify-between items-center mt-4">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Total Capacity:</span> 40 students
                </div>
                <div class="flex space-x-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                        Print Seating Plan
                    </button>
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                        Export as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>