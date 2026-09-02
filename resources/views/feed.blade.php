@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row gap-6">

            <!-- Левая панель (десктоп) -->
            <aside class="hidden md:block w-64 flex-shrink-0">
                <nav class="bg-white rounded-xl shadow p-4 space-y-2">
                    <a href="#" class="flex items-center space-x-3 text-gray-700 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">Лента</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-gray-500 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="font-medium">Сообщества</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-gray-500 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="font-medium">Уведомления</span>
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">3</span>
                    </a>
                </nav>
            </aside>

            <!-- Основной контент (лента) -->
            <main class="flex-1 min-w-0">
                <!-- Форма создания поста (только для авторизованных) -->
                @auth
                    <div class="bg-white rounded-xl shadow p-4 mb-6">
                        <div class="flex items-start space-x-3">
                            <img src="{{ auth()->user()->avatar ?? asset('images/avatar-placeholder.png') }}"
                                 class="w-10 h-10 rounded-full object-cover" alt="Avatar">
                            <div class="flex-1">
                            <textarea rows="2"
                                      class="w-full border-0 resize-none focus:ring-0 text-gray-700 placeholder-gray-400"
                                      placeholder="Что у вас нового, {{ auth()->user()->name }}?"></textarea>
                                <div class="flex justify-between items-center mt-2">
                                    <button class="text-gray-400 hover:text-blue-500 transition">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">...</svg>
                                    </button>
                                    <button class="px-4 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
                                        Опубликовать
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Для гостей — предложение войти или зарегистрироваться -->
                    <div class="bg-white rounded-xl shadow p-4 mb-6 text-center">
                        <p class="text-gray-600">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Войдите</a> или
                            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">зарегистрируйтесь</a>,
                            чтобы публиковать посты.
                        </p>
                    </div>
                @endauth

                <!-- Лента постов -->
                @foreach ($posts as $post)
                    <div class="post bg-white rounded-xl shadow p-4 mb-4 hover:shadow-md transition"
                         x-data="{ openComments: false }"
                         x-id="['comments']">

                        <!-- Шапка поста -->
                        <div class="flex items-start space-x-3">
                            <img src="{{ $post->user->avatar ?? asset('images/avatar-placeholder.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Текст поста -->
                        <p class="mt-2 text-gray-700">{{ $post->content }}</p>

                        <!-- Действия -->
                        <div class="flex items-center gap-4 mt-4 text-gray-500">
                            <!-- Лайки -->
                            <button class="flex items-center gap-1 hover:text-red-500">
                                ❤️ <span>{{ $post->likes->count() }}</span>
                            </button>

                            <!-- Комментарии -->
                            <button @click="openComments = !openComments"
                                    class="flex items-center gap-1 hover:text-blue-500 transition">
                                💬 <span>{{ $post->comments->count() }}</span>
                            </button>

                            <!-- Репосты -->
                            <button class="flex items-center gap-1 hover:text-green-500">
                                🔄 <span>{{ $post->reposts->count() }}</span>
                            </button>
                        </div>

                        <!-- Блок комментариев (раскрывается под кнопками) -->
                        <div x-show="openComments" x-transition.duration.300ms class="mt-4 pt-4 border-t border-gray-100 space-y-4">
                            @forelse ($post->comments as $comment)
                                <div class="flex items-start space-x-3">
                                    <img src="{{ $comment->user->avatar ?? asset('images/avatar-placeholder.png') }}"
                                         class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                         alt="{{ $comment->user->name }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-sm text-gray-800">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 mt-0.5">{{ $comment->content }}</p>

                                        <!-- Действия: лайк + ответить -->
                                        <div class="flex items-center gap-4 mt-1 text-gray-400 text-xs">
                                            <button class="flex items-center gap-1 hover:text-red-500 transition">
                                                ❤️ <span>{{ $comment->likes->count() }}</span>
                                            </button>
                                            <button class="flex items-center gap-1 hover:text-blue-500 transition">
                                                💬 Ответить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center">Пока нет комментариев. Будьте первым!</p>
                            @endforelse

                            <!-- Форма добавления комментария (задел на будущее) -->
                            @auth
                                <div class="mt-3">
                                    <input type="text"
                                           placeholder="Написать комментарий..."
                                           class="w-full text-sm border-0 border-b border-gray-200 focus:ring-0 focus:border-blue-500 transition">
                                </div>
                            @endauth
                        </div>
                    </div>
                @endforeach

                <!-- Пагинация -->
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </main>

            <!-- Правая панель (десктоп) -->
            <aside class="hidden lg:block w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-bold text-gray-800">Тренды</h3>
                    <div class="mt-3 space-y-2">
                        <p class="text-sm text-blue-600">#Laravel</p>
                        <p class="text-sm text-blue-600">#Vortex</p>
                        <p class="text-sm text-blue-600">#PHP</p>
                    </div>
                </div>
            </aside>

        </div>
    </div>
@endsection
