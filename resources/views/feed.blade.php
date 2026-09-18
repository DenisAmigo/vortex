@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row gap-6">

            <!-- Левая панель (десктоп) -->
            <aside class="hidden md:block w-64 flex-shrink-0">
                @include('partials.left-sidebar')
            </aside>

            <!-- Основной контент (лента) -->
            <main class="flex-1 min-w-0">
                <!-- Форма создания поста (только для авторизованных) -->
                @auth
                    <livewire:create-post />
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
                         x-data="{ openComments: false, confirmDelete: false }">

                        <!-- Шапка поста -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <a href="{{ route('profile.show', $post->user->id) }}" class="flex items-start space-x-3 group">
                                    <img src="{{ $post->user->avatar ? Storage::url($post->user->avatar) : asset('images/avatar-placeholder.png') }}" class="w-10 h-10 rounded-full" alt="Avatar">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $post->user->name }}</p>
                                        <p class="text-xs text-gray-400"
                                           title="{{ $post->created_at->format('d.m.Y H:i') }}">
                                            {{ $post->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                            </div>

                            <!-- Кнопка меню -->
                            @auth
                                <div x-data="{ openMenu: false, confirmDelete: false }" class="relative">
                                    <button @click="openMenu = !openMenu"
                                            class="text-gray-400 hover:text-gray-600 transition p-1 rounded-full hover:bg-gray-100">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <!-- Выпадающее меню -->
                                    <div x-show="openMenu"
                                         x-cloak
                                         @click.away="openMenu = false"
                                         x-transition.duration.150ms
                                         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-10">

                                        @if($post->user_id === auth()->id())
                                            <button @click="confirmDelete = true; openMenu = false"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                                🗑️ Удалить этот пост
                                            </button>
                                        @endif

                                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            🙈 Не показывать это в моей ленте
                                        </button>
                                    </div>

                                    <!-- Модальное окно подтверждения -->
                                    <div x-show="confirmDelete"
                                         x-cloak
                                         x-transition.duration.200ms
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                                         @click.away="confirmDelete = false">
                                        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Вы уверены?</h3>
                                            <p class="text-sm text-gray-600 mb-6">
                                                Это действие удалит пост <span class="font-medium text-gray-800">"{{ Str::limit($post->content, 50) }}"</span>.
                                                Отменить его будет невозможно.
                                            </p>
                                            <div class="flex justify-end space-x-3">
                                                <button @click="confirmDelete = false"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition">
                                                    Отмена
                                                </button>
                                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-full transition">
                                                        Удалить
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        </div>

                        <!-- Текст поста -->
                        <p class="mt-2 text-gray-700">{{ $post->content }}</p>

                        <!-- Действия -->
                        <div class="flex items-center gap-4 mt-4 text-gray-500">
                            <!-- Лайки -->
                            <livewire:like-post :post="$post" wire:key="like-{{ $post->id }}" />

                            <!-- Комментарии -->
                            <button @click="openComments = !openComments"
                                    class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-400 group-hover:text-blue-500 transition-colors duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                                </svg>
                                <livewire:comment-count :post="$post" :key="'comment-count-'.$post->id" />
                            </button>

                            <!-- Репосты -->
                            <button class="flex items-center gap-1 group cursor-pointer transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-400 group-hover:text-green-500 transition-colors duration-200">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                                </svg>
                                <span class="text-sm text-gray-500 group-hover:text-green-500 transition-colors duration-200">{{ $post->reposts->count() }}</span>
                            </button>
                        </div>

                        <!-- Блок комментариев (раскрывается) -->
                        <div x-show="openComments" x-transition.duration.300ms x-cloak class="mt-4 pt-4 border-t border-gray-100 space-y-4">
                            <livewire:post-comments :post="$post" :key="'comments-'.$post->id" />
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
