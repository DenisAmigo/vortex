<div>
    @auth
        @if(auth()->id() !== $user->id)
            <button wire:click="toggleFollow"
                    wire:loading.attr="disabled"
                    wire:target="toggleFollow"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-full transition
                    {{ $isFollowing
                        ? 'text-gray-700 bg-gray-100 hover:bg-gray-200'
                        : 'text-white bg-blue-600 hover:bg-blue-700' }}">

                @if($isFollowing)
                    <!-- Иконка "минус" (Отписаться) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                    <span>Отписаться</span>
                @else
                    <!-- Иконка "плюс" (Подписаться) -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Подписаться</span>
                @endif
            </button>
        @endif
    @else
        <a href="{{ route('register') }}"
           class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Подписаться</span>
        </a>
    @endauth
</div>
