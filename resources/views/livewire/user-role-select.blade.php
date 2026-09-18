<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Текущие роли (кликабельные) -->
    <button @click="$wire.toggle()" @click.away="$wire.set('isOpen', false)"
            @if($user->hasRole('vortex_admin')) disabled @endif
            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs transition
                {{ $user->hasRole('vortex_admin')
                    ? 'bg-red-100 text-red-700 cursor-not-allowed'
                    : 'bg-blue-100 text-blue-700 hover:bg-blue-200 cursor-pointer' }}">

        @forelse($user->roles as $role)
            {{ $role->name }}
        @empty
            <span class="text-gray-400">Нет роли</span>
        @endforelse

        @unless($user->hasRole('vortex_admin'))
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        @endunless
    </button>

    <!-- Выпадающий список ролей -->
    @if($isOpen && !$user->hasRole('vortex_admin'))
        <div class="absolute z-10 mt-1 w-40 bg-white rounded-md shadow-lg border border-gray-200 py-1">
            @foreach($roles as $role)
                <button wire:click="changeRole({{ $role->id }})"
                        class="block w-full text-left px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-100 transition
                            {{ $user->hasRole($role->name) ? 'bg-blue-50 font-medium' : '' }}">
                    {{ $role->name }}
                </button>
            @endforeach
        </div>
    @endif
</div>
