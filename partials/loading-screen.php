<!-- Loading Screen -->
<div id="loading-screen" class="fixed inset-0 bg-white z-50 flex items-center justify-center" style="background: #617ba445 url('uploads/pattern-loading.png') repeat center;">
    <div class="relative">
        <svg class="w-48 md:w-64 h-48 md:h-64 animate-spin-slow" viewBox="0 0 160 160">
            <circle 
                class="loading-circle"
                cx="80" 
                cy="80" 
                r="70" 
                stroke-width="7" 
                fill="none"
                stroke="#E5E7EB"
            />
            <circle 
                id="progress-circle"
                class="loading-circle"
                cx="80" 
                cy="80" 
                r="70" 
                stroke-width="7" 
                fill="none"
                stroke="transparent"
            />
        </svg>
        <div id="loading-logo" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-32 md:w-40 flex flex-col items-center">
            <div id="clock" class="relative w-20 h-20 md:w-32 md:h-32">
                <div class="absolute w-full h-full rounded-full bg-slate-200"></div>
                <div id="hour-hand" class="absolute w-1 h-8 bg-slate-700 left-1/2 bottom-1/2 transform -translate-x-1/2 origin-bottom"></div>
                <div id="minute-hand" class="absolute w-1 h-10 bg-gray-800 left-1/2 bottom-1/2 transform -translate-x-1/2 origin-bottom hidden"></div>
                <div id="second-hand" class="absolute w-1 h-10 bg-copper left-1/2 bottom-1/2 transform -translate-x-1/2 origin-bottom"></div>
                <div class="absolute w-6 h-6 bg-slate-700 rounded-full left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2"></div>
            </div>
        </div>
        <button id="start-button" class="flex items-center gap-2 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-slate-blue text-base md:text-xl text-white px-8 py-6 rounded-full ring-2 ring-offset-4 ring-deep-blue whitespace-nowrap overflow-hidden opacity-0 scale-0">
            <span class="opacity-0 transition-opacity duration-300 text-lg" id="button-text">Hayatını yavaşlat</span>
            <svg class="w-6 h-6 ml-3 animate-pulse opacity-0 transition-opacity duration-300" id="button-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>
    </div>
</div>