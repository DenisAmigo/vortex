<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="flex items-start space-x-3">
            <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
                 class="w-10 h-10 rounded-full object-cover" alt="Avatar">
            <div class="flex-1">
                <textarea wire:model="content"
                          wire:key="create-post-textarea"
                          x-data="{
                              resize() {
                                  $el.style.height = 'auto';
                                  $el.style.height = $el.scrollHeight + 'px';
                              }
                          }"
                          x-init="resize()"
                          @input="resize()"
                          rows="1"
                          class="w-full border-0 resize-none focus:ring-0 text-gray-700 placeholder-gray-400"
                          placeholder="Что у вас нового, {{ auth()->user()->name }}?"></textarea>

                <div class="flex justify-between items-center mt-2">
                    <button class="text-gray-400 hover:text-blue-500 transition">
                        <!-- Иконка прикрепления (заглушка) -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    <button wire:click="createPost"
                            wire:loading.attr="disabled"
                            :disabled="$wire.content?.trim().length < 1"
                            class="px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="createPost">Опубликовать</span>
                        <span wire:loading wire:target="createPost">Публикация...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
