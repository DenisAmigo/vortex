<div x-data="{
    hasAvatar: {{ $user->avatar ? 'true' : 'false' }},
    isOwnProfile: {{ auth()->id() === $user->id ? 'true' : 'false' }},
    showViewModal: @entangle('showViewModal'),
    showDeleteConfirm: @entangle('showDeleteConfirm')
}"
     x-on:livewire-upload-start="showViewModal = false">

    <!-- Аватар (кликабельный) -->
    <div class="relative">
        <img src="{{ $user->avatar ? Storage::url($user->avatar) . '?v=' . $user->updated_at->timestamp : asset('images/avatar-placeholder.png') }}"
             class="w-32 h-32 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
             @click="
                 if (isOwnProfile) {
                     if (hasAvatar) {
                         showViewModal = true;
                     } else {
                         $refs.fileInput.click();
                     }
                 } else if (hasAvatar) {
                     showViewModal = true;
                 }
             "
             alt="{{ $user->name }}"
             @if(!$user->avatar)
                 title="Заменить аватар"
             @endif>

        <!-- Прелоадер -->
        <div wire:loading.flex wire:target="photo"
             class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full z-10">
            <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Скрытый input для загрузки -->
    <input type="file"
           x-ref="fileInput"
           wire:model="photo"
           accept="image/*"
           class="hidden">

    <!-- Модалка просмотра (только для чтения, для чужих профилей) -->
    <div x-show="showViewModal"
         x-transition.duration.200ms
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
         x-cloak
         @click.self="showViewModal = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 p-6 pt-12 relative">
            <!-- Крестик закрытия -->
            <button @click="showViewModal = false"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            @php
                $originalPath = $user->avatar
                    ? str_replace('thumb.', 'original.', $user->avatar)
                    : null;
            @endphp

            <img src="{{ $originalPath && Storage::disk('public')->exists($originalPath)
                ? Storage::url($originalPath) . '?v=' . $user->updated_at->timestamp
                : ($user->avatar ? Storage::url($user->avatar) . '?v=' . $user->updated_at->timestamp : asset('images/avatar-placeholder.png')) }}"
                 class="w-full h-auto rounded-lg mb-4"
                 alt="{{ $user->name }}">

            @auth
                @if(auth()->id() === $user->id)
                    <div class="flex justify-end gap-3">
                        <button @click="$refs.fileInput.click()"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
                            Загрузить новый
                        </button>
                        <button @click="showDeleteConfirm = true"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-full hover:bg-red-700 transition">
                            Удалить аватар
                        </button>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Модалка подтверждения удаления -->
    <div x-show="showDeleteConfirm"
         x-transition.duration.200ms
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-cloak
         @click.self="showDeleteConfirm = false">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Удалить аватар?</h3>
            <p class="text-sm text-gray-600 mb-6">Это действие нельзя отменить.</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="showDeleteConfirm = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                    Отмена
                </button>
                <button wire:click="deleteAvatar"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-full transition">
                    Удалить
                </button>
            </div>
        </div>
    </div>
</div>
