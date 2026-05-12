<div class="p-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">School Notifications</h2>
        <button class="text-blue-500 hover:text-blue-700 text-sm font-medium">
            Mark all as read
        </button>
    </div>

    <!-- Notification List -->
    <div class="space-y-3">
        <!-- New Student Registration -->
        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">New Student Registration</p>
                    <p class="text-xs text-gray-500">Rahul Sharma (Class 10-A) has completed registration.</p>
                    <p class="text-xs text-gray-400 mt-1">30 minutes ago</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                </div>
            </div>
        </div>

        <!-- Assignment Submission -->
        <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">Assignment Submitted</p>
                    <p class="text-xs text-gray-500">25 students have submitted the Science project (Due: Today).</p>
                    <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                </div>
            </div>
        </div>

        <!-- Add more notifications as needed -->

    </div>

    <!-- View All Link -->
    <div class="mt-4 text-center pt-4 border-t border-gray-200">
        <a href="#" class="text-sm text-blue-500 hover:text-blue-700 font-medium">View all notifications</a>
    </div>
</div>