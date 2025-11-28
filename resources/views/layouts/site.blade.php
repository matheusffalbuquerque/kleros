<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', __('site.meta.description'))">
    <link rel="icon" href="{{asset('images/kleros-logo.svg')}}" type="image/svg+xml">
    <title>@yield('title', __('site.meta.title'))</title>

    {{-- Favicon (opcional) --}}
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    {{-- Fonte principal --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        :root {
            --ui-font: "Segoe UI", Roboto, system-ui, -apple-system, Arial, sans-serif;
        }
        html {
            font-family: var(--ui-font);
            scroll-behavior: smooth;
            background-color: #1a1821;
            color: #f4f3f6;
        }
    </style>

    {{-- Tailwind CSS (se estiver usando Vite ou Mix) --}}
    @vite([
    'resources/css/app.scss',
    'resources/css/site.css',
    'resources/js/app.js'
    ])
</head>
<body class="antialiased">
    @if (session('locale_changed') || session('locale_error'))
        <div class="bg-[#1a1821] text-white">
            <div class="max-w-7xl mx-auto px-4 py-2 text-sm {{ session('locale_error') ? 'text-red-300' : 'text-emerald-200' }}">
                {{ session('locale_changed') ?? session('locale_error') }}
            </div>
        </div>
    @endif
    @yield('content')
    @stack('scripts')
</body>
</html>
