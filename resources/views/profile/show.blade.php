@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col lg:flex-row gap-6">

            <!-- Левая панель (глобальная) -->
            <aside class="hidden lg:block w-64 flex-shrink-0">
                @include('partials.left-sidebar')
            </aside>

            <!-- Основной контент -->
            <main class="flex-1 min-w-0">

                <!-- Шапка профиля -->
                <div class="bg-white rounded-xl shadow mb-6 overflow-hidden">
                    <!-- Обложка -->
                    <div class="h-48 w-full bg-cover bg-center"
                         style="background-image: url('{{ asset('images/default-cover.png') }}');">
                    </div>

                    <!-- Информация о пользователе -->
                    <div class="p-6 relative">
                        <!-- Аватар -->
                        <div class="absolute -top-16 left-6">
                            <livewire:avatar-upload :user="$user" :key="'avatar-'.$user->id" />
                        </div>

                        <!-- Кнопка справа -->
                        <div class="flex justify-end">
                            @if(auth()->id() === $user->id)
                                <button class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                                    Редактировать профиль
                                </button>
                            @else
                                <livewire:follow-button :user="$user" :key="'follow-'.$user->id" />
                            @endif
                        </div>

                        <!-- Имя и био -->
                        <div class="mt-12">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            @if($user->bio)
                                <p class="mt-2 text-sm text-gray-600">{{ $user->bio }}</p>
                            @endif
                        </div>

                        <!-- Статистика -->
                        <div class="flex items-center gap-6 mt-4 text-sm">
                            <div>
                                <span class="font-semibold text-gray-900">{{ $postsCount }}</span>
                                <span class="text-gray-500 ml-1">{{ plural($postsCount, 'пост', 'поста', 'постов') }}</span>
                            </div>

                            <livewire:follow-count :user="$user" :key="'follow-count-'.$user->id" />

                            <div class="text-gray-400 text-xs ml-auto">
                                На сайте с {{ $user->created_at->format('d.m.Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Лента постов пользователя -->
                @forelse ($posts as $post)
                    <div class="bg-white rounded-xl shadow p-4 mb-4 hover:shadow-md transition relative"
                         x-data="{ openComments: false, confirmDelete: false }"
                         x-id="['comments']">

                        <!-- Шапка поста (переиспользуем логику из feed) -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <img src="{{ $post->user->avatar ? Storage::url($post->user->avatar) : asset('images/avatar-placeholder.png') }}"
                                     class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
                                    <p class="text-xs text-gray-400"
                                       title="{{ $post->created_at->format('d.m.Y H:i') }}">
                                        {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Текст поста -->
                        <p class="mt-2 text-gray-700 whitespace-pre-wrap">{{ $post->content }}</p>

                        <!-- Кнопки действий (лайк, комментарии, репост) -->
                        <div class="flex items-center gap-6 mt-4 text-gray-500">
                            <livewire:like-post :post="$post" :key="'like-'.$post->id" />

                            <button @click="openComments = !openComments"
                                    class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition-colors duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                                </svg>
                                <livewire:comment-count :post="$post" :key="'comment-count-'.$post->id" />
                            </button>

                            <button class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-400 group-hover:text-green-500 transition-colors duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                                </svg>
                                <span class="text-sm text-gray-500 group-hover:text-green-500 transition-colors duration-200">{{ $post->reposts->count() }}</span>
                            </button>
                        </div>

                        <!-- Блок комментариев -->
                        <div x-show="openComments" x-transition.duration.200ms class="mt-4 pt-4 border-t border-gray-100">
                            <livewire:post-comments :post="$post" :key="'comments-'.$post->id" />
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                        <p class="text-lg">Пока нет постов.</p>
                    </div>
                @endforelse

                <!-- Пагинация -->
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </main>

            <!-- Правая панель (заглушка) -->
            <aside class="hidden lg:block w-72 flex-shrink-0">
                <!-- Пока пусто -->
            </aside>
        </div>
    </div>
@endsection
