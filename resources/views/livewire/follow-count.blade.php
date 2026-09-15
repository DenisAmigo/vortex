<div class="flex items-center gap-6">
    <div>
        <span class="font-semibold text-gray-900">{{ $followersCount }}</span>
        <span class="text-gray-500 ml-1">{{ plural($followersCount, 'подписчик', 'подписчика', 'подписчиков') }}</span>
    </div>
    <div>
        <span class="font-semibold text-gray-900">{{ $followingsCount }}</span>
        <span class="text-gray-500 ml-1">{{ plural($followingsCount, 'подписка', 'подписки', 'подписок') }}</span>
    </div>
</div>
