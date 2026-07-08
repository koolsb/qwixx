<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="theme-color" content="#18181b">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">

        <title>{{ $title ?? config('app.name', 'Qwixx') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance
        @livewireStyles
    </head>
    <body class="min-h-full bg-zinc-50 text-zinc-800 antialiased dark:bg-zinc-950 dark:text-zinc-200">
        <header class="bg-zinc-900 text-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <a href="{{ route('picker') }}" class="flex items-center gap-3">
                    <span class="text-2xl font-black tracking-tight">
                        Qwi<span class="text-qwixx-red">x</span><span class="text-qwixx-blue">x</span>
                    </span>
                    <span class="hidden text-sm font-medium text-white/70 sm:inline">Scoresheets</span>
                </a>
                <div class="flex items-center gap-1">
                    <flux:button variant="ghost" size="sm" class="text-white!" x-data x-on:click="$flux.dark = ! $flux.dark" title="Light / dark mode">
                        <flux:icon.sun class="size-4" x-show="! $flux.dark" x-cloak />
                        <flux:icon.moon class="size-4" x-show="$flux.dark" x-cloak />
                    </flux:button>
                    <flux:button href="https://gamewright.com/pdfs/Rules/QwixxTM-RULES.pdf" target="_blank" variant="ghost" size="sm" class="text-white!">
                        Rules
                    </flux:button>
                </div>
            </div>
            <div class="qwixx-stripe h-1.5 w-full"></div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-8">
            {{ $slot }}
        </main>

        @livewireScripts
        @fluxScripts
    </body>
</html>
