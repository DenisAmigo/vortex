@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Управление пользователями</h1>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Аватар</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Имя</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Роли</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $user->id }}</td>
                            <td class="px-4 py-3">
                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/avatar-placeholder.png') }}"
                                     class="w-8 h-8 rounded-full object-cover"
                                     alt="{{ $user->name }}">
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <livewire:user-role-select :user="$user" :key="'role-'.$user->id" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($user->canBeImpersonated() && auth()->id() !== $user->id)
                                    <form action="{{ route('impersonate.start', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-full hover:bg-blue-700 transition">
                                            Войти как
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
