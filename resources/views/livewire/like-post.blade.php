<div x-data="{
    isLiked: @json($isLiked),
    likesCount: @json($likesCount),
    isPopupOpen: false,
    isLoading: false,
    toggleLike() {
        // Обновляем UI
        this.isLiked = !this.isLiked;
        this.likesCount += this.isLiked ? 1 : -1;

        // Отправляем запрос на сервер
        @this.toggleLike();
    },
    togglePopup() {
        this.isPopupOpen = !this.isPopupOpen;
        if (this.isPopupOpen) {
            this.isLoading = true;
            @this.loadLikers().then(() => {
                this.isLoading = false;
            });
        }
    }
}"
     class="relative flex items-center gap-1 cursor-pointer group select-none">

        <button class="flex items-center gap-1 group cursor-pointer transition-colors duration-200"
                @if(!auth()->check())
                    @click.prevent="window.location.href='{{ route('register') }}'"
                @else
                    @click="toggleLike()"
                @endif
        >

        <!-- Иконка (пустое сердце) -->
        <template x-if="!isLiked">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition-colors duration-200">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
        </template>

        <!-- Иконка (заполненное сердце) -->
        <template x-if="isLiked">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ef4444" class="w-6 h-6 transition-colors duration-200">
                <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.497 5.497 0 0112 5.342 5.497 5.497 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/>
            </svg>
        </template>

        <!-- Счётчик -->
        <span x-text="likesCount"
              @click.stop="togglePopup()"
              class="text-sm text-gray-500 group-hover:text-red-500 transition-colors duration-200"></span>
    </button>

    <!-- Всплывайка со списком лайкнувших -->
    <div x-show="isPopupOpen"
         x-transition.duration.200ms
         @click.away="isPopupOpen = false"
         x-cloak
         class="absolute bottom-full left-0 mb-2 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50 max-h-64 overflow-y-auto">

        <div class="p-3">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Кто лайкнул</h4>

            <!-- Индикатор загрузки -->
            <div x-show="isLoading" class="flex justify-center py-4">
                <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div x-show="!isLoading">
                @if($totalLikersCount === 0)
                    <p class="text-sm text-gray-400 text-center py-2">Пока никто не лайкнул</p>
                @else
                    <ul class="space-y-2">
                        @foreach($likers as $user)
                            <li>
                                <a href="/profile/{{ $user['id'] }}"
                                   class="flex items-center gap-2 hover:bg-gray-50 rounded-lg px-2 py-1 transition-colors duration-150">
                                    <img src="{{ $user->avatar ?? asset('images/avatar-placeholder.png') }}"
                                         class="w-6 h-6 rounded-full object-cover"
                                         alt="{{ $user->name }}">
                                    <span class="text-sm text-gray-700">
                                        {{ $user->name }}
                                        @if(auth()->check() && $user->id === auth()->id())
                                            <span class="text-xs text-gray-400 ml-1">(Вы)</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach

                        @if($totalLikersCount > 3)
                            <li class="text-sm text-gray-400 pl-2">
                                и ещё {{ $totalLikersCount - 3 }} человек(-а)
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
