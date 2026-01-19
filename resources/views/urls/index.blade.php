@extends('layouts.app')

@section('title', 'Мои ссылки')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Мои ссылки</h1>
                <p class="text-gray-600 mt-2">Все созданные вами короткие ссылки</p>
            </div>
            <a href="{{ route('urls.create') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>Новая ссылка
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($urls->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Короткая ссылка
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Переходы
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Создана
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Срок жизни
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($urls as $url)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($url->is_commercial)
                                                <a href="{{ $url->commercial_redirect_url }}" target="_blank" class="text-yellow-600 hover:text-yellow-800">
                                                    <i class="fas fa-ad mr-1"></i>{{ $url->commercial_redirect_url }}
                                                </a>
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-ad mr-1"></i>Реклама
                                                </span>
                                            @else
                                                <a href="{{ $url->short_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $url->short_url }}
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            {{ Str::limit($url->original_url, 60) }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            <a href="{{ route('urls.stats', $url) }}" class="hover:text-indigo-600">
                                                <i class="fas fa-chart-bar mr-1"></i>Статистика
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 font-semibold">{{ $url->total_clicks }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $url->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($url->expires_at)
                                    @if($url->isExpired())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Истекла {{ $url->expires_at->format('d.m.Y') }}
                            </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                До {{ $url->expires_at->format('d.m.Y') }}
                            </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">Бессрочная</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('urls.stats', $url) }}"
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="Статистика">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <form action="{{ route('urls.destroy', $url) }}" method="POST"
                                          onsubmit="return confirm('Вы уверены, что хотите удалить эту ссылку?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $urls->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-link text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">У вас пока нет созданных ссылок</h3>
                <p class="text-gray-500 mb-6">Создайте свою первую короткую ссылку прямо сейчас</p>
                <a href="{{ route('urls.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i>Создать ссылку
                </a>
            </div>
        @endif
    </div>
@endsection
