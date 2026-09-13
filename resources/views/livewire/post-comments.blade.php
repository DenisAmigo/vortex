<div class="space-y-4">
    @forelse ($comments as $comment)
        <div class="flex items-start space-x-3">
            <img src="{{ $comment->user->avatar ?? asset('images/avatar-placeholder.png') }}"
                 class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                 alt="{{ $comment->user->name }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="font-medium text-sm text-gray-800">{{ $comment->user->name }}</span>
                    <span class="text-xs text-gray-400 flex-shrink-0" title="{{ $comment->created_at->format('d.m.Y H:i') }}">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-sm text-gray-700 mt-0.5">{{ $comment->content }}</p>

                <!-- Действия: лайк + ответить -->
                <div class="flex items-center gap-4 mt-1 text-gray-400 text-xs">
                    <livewire:like-comment :comment="$comment" :key="'like-comment-'.$comment->id" />

                    <button class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                        </svg>
                        <span class="group-hover:text-blue-500 transition-colors duration-200">Ответить</span>
                    </button>

                    @if($comment->user_id === auth()->id())
                        <button class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
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
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-400 text-center">Пока нет комментариев. Будьте первым!</p>
    @endforelse

    <!-- Форма добавления комментария -->
    <livewire:add-comment :post="$post" :key="'add-comment-'.$post->id" />
</div>
