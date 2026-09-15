<div class="space-y-4">
    @forelse ($comments as $comment)
        <div wire:key="comment-{{ $comment->id }}" class="flex items-start space-x-3">
            <img src="{{ $comment->user->avatar ?? asset('images/avatar-placeholder.png') }}"
                 class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                 alt="{{ $comment->user->name }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-sm text-gray-800">{{ $comment->user->name }}</span>
                    <span class="text-xs text-gray-400 flex-shrink-0" title="{{ $comment->created_at->format('d.m.Y H:i') }}">
                        {{ $comment->created_at->diffForHumans() }}
                        @if($comment->updated_at->gt($comment->created_at))
                            (изменено)
                        @endif
                    </span>
                </div>
                <p class="text-sm text-gray-700 mt-0.5">
                    @if($comment->parent_id)
                        <a href="/profile/{{ $comment->parent->user->id }}"
                           class="text-blue-600 hover:underline font-medium">{{ explode(' ', $comment->parent->user->name)[0] }}</a>,
                    @endif
                    {{ $comment->content }}
                </p>

                <!-- Блок действий -->
                <div class="flex items-center gap-4 mt-1 text-gray-400 text-xs">
                    <livewire:like-comment :comment="$comment" :key="'like-comment-'.$comment->id" />

                    <button wire:click="startReply({{ $comment->id }})" class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        <span class="group-hover:text-blue-500 transition-colors duration-200">Ответить</span>
                    </button>

                    @if($comment->user_id === auth()->id())
                        <button wire:click="startEditing({{ $comment->id }})" class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 group-hover:text-yellow-500 transition-colors duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            <span class="group-hover:text-yellow-500 transition-colors duration-200">Редактировать</span>
                        </button>

                        <button wire:click="deleteComment({{ $comment->id }})"
                                wire:loading.attr="disabled"
                                wire:target="deleteComment({{ $comment->id }})"
                                class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 group-hover:text-red-500 transition-colors duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            <span class="group-hover:text-red-500 transition-colors duration-200">Удалить</span>
                        </button>
                    @endif
                </div>

                <!-- Форма ответа (появляется при клике на «Ответить») -->
                @auth
                    @if($replyingToCommentId === $comment->id)
                        <div class="mt-3">
                            <div class="flex items-start space-x-2">
                                <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
                                     class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-1"
                                     alt="{{ auth()->user()->name }}">

                                <label class="flex-1">
                                    <textarea wire:model="replyContent"
                                              wire:key="reply-textarea-{{ $comment->id }}"
                                              x-data="{
                                                  resize() {
                                                      $el.style.height = 'auto';
                                                      $el.style.height = $el.scrollHeight + 'px';
                                                  }
                                              }"
                                              x-init="resize()"
                                              @input="resize()"
                                              rows="1"
                                              class="w-full text-sm border-0 border-b border-gray-200 focus:ring-0 focus:border-blue-500 resize-none text-gray-700 placeholder-gray-400 overflow-hidden"
                                              placeholder="Ответить {{ explode(' ', $comment->user->name)[0] }}..."></textarea>

                                    <div class="flex justify-end gap-2 mt-2">
                                        <button wire:click="cancelReply"
                                                class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                                            Отмена
                                        </button>
                                        <button wire:click="addReply"
                                                wire:loading.attr="disabled"
                                                wire:target="addReply"
                                                :disabled="$wire.replyContent?.trim().length < 1"
                                                class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span wire:loading.remove wire:target="addReply">Ответить</span>
                                            <span wire:loading wire:target="addReply">Отправка...</span>
                                        </button>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center">Пока нет комментариев. Будьте первым!</p>
    @endforelse

    <!-- Форма добавления комментария -->
    @auth
        <div class="mt-3">
            <div class="flex items-start space-x-2">
                <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
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
                              wire:model="editingContent"
                              wire:key="add-comment-textarea-{{ $post->id }}"
                              x-init="resize()"
                              @input="resize()"
                              rows="1"
                              class="w-full text-sm border-0 border-b border-gray-200 focus:ring-0 focus:border-blue-500 resize-none text-gray-700 placeholder-gray-400 overflow-hidden"
                              placeholder="{{ $editingCommentId ? 'Редактирование...' : 'Написать комментарий...' }}"></textarea>
                    </label>

                    <div class="flex justify-end gap-2 mt-2" x-show="$wire.editingContent?.trim().length > 0" x-transition>
                        @if($editingCommentId)
                            <button wire:click="cancelEdit"
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                                Отмена
                            </button>
                            <button wire:click="saveEdit"
                                    wire:loading.attr="disabled"
                                    wire:target="saveEdit"
                                    :disabled="$wire.editingContent?.trim().length < 1"
                                    class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="saveEdit">Сохранить</span>
                                <span wire:loading wire:target="saveEdit">Сохранение...</span>
                            </button>
                        @else
                            <button wire:click="$set('editingContent', '')"
                                    type="button"
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                                Очистить
                            </button>
                            <button wire:click="addComment"
                                    wire:loading.attr="disabled"
                                    wire:target="addComment"
                                    :disabled="$wire.editingContent?.trim().length < 1"
                                    class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="addComment">Отправить</span>
                                <span wire:loading wire:target="addComment">Отправка...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endauth
</div>
