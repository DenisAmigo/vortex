<div>
    @auth
        <div class="mt-3">

            <div class="flex items-start space-x-2">
                <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('images/avatar-placeholder.png') }}"
                     class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-1"
                     alt="{{ auth()->user()->name }}">

                <div class="flex-1">
                    <label x-data="{
                        resize() {
                            const el = $refs.textarea;
                            if (el) {
                                el.style.height = 'auto';
                                el.style.height = el.scrollHeight + 'px';
                            }
                        }
                    }"
                         x-effect="openComments && $nextTick(() => resize())">
                        <textarea x-ref="textarea"
                                  wire:model="content"
                                  wire:key="add-comment-textarea-{{ $post->id }}"
                                  x-init="resize()"
                                  @input="resize()"
                                  rows="1"
                                  class="w-full text-sm border-0 border-b border-gray-200 focus:ring-0 focus:border-blue-500 resize-none text-gray-700 placeholder-gray-400 overflow-hidden"
                                  placeholder="Написать комментарий..."></textarea>
                    </label>

                    <div class="flex justify-end gap-2 mt-2" x-show="$wire.content?.trim().length > 0" x-transition>
                        <!-- Кнопка "Очистить" -->
                        <button wire:click="$set('content', '')"
                                type="button"
                                class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                            Очистить
                        </button>

                        <!-- Кнопка "Отправить" -->
                        <button wire:click="addComment"
                                wire:loading.attr="disabled"
                                wire:target="addComment"
                                :disabled="$wire.content?.trim().length < 1"
                                class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="addComment">Отправить</span>
                            <span wire:loading wire:target="addComment">Отправка...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endauth
</div>
