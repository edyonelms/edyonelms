<div class="bg-white min-h-screen flex items-center justify-center px-6 py-16">
    <div class="text-center w-full max-w-2xl">

        {{-- Logo --}}
        <div class="flex justify-center mb-6">
            <img src="{{ asset('website-image/Group 11525.png') }}" alt="Edyone LMS" class="w-14 h-14 object-contain">
        </div>

        {{-- Coming Soon Badge --}}
        <div class="flex justify-center mb-5">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide bg-purple-50 text-purple-600 border border-purple-100">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                Coming Soon
            </span>
        </div>

        {{-- Heading --}}
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
            Websites for <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500">Schools</span>
        </h1>

        {{-- Subtext --}}
        <p class="text-gray-400 text-base leading-relaxed max-w-md mx-auto mb-12">
            A powerful no-code website builder for every school on Edyone LMS — beautifully branded, instantly live.
        </p>

        {{-- Feature cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-12">
            <div class="group flex flex-col items-center gap-3 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-purple-100 transition-all duration-200">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm0 8a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zm12 0a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Custom Pages</p>
                    <p class="text-xs text-gray-400 mt-0.5">Build pages your way</p>
                </div>
            </div>

            <div class="group flex flex-col items-center gap-3 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-pink-100 transition-all duration-200">
                <div class="w-11 h-11 bg-pink-50 rounded-xl flex items-center justify-center group-hover:bg-pink-100 transition-colors">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">School Branding</p>
                    <p class="text-xs text-gray-400 mt-0.5">Logo, colors & identity</p>
                </div>
            </div>

            <div class="group flex flex-col items-center gap-3 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-200">
                <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">Secure Hosting</p>
                    <p class="text-xs text-gray-400 mt-0.5">Fast, reliable & safe</p>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="flex items-center justify-center gap-3 mb-6">
            <div class="h-px w-16 bg-gray-100"></div>
            <p class="text-xs text-gray-300 tracking-wide uppercase">Under Development</p>
            <div class="h-px w-16 bg-gray-100"></div>
        </div>

        <p class="text-xs text-gray-400">We'll notify you as soon as this feature goes live.</p>

    </div>
</div>
