<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="{{ $page->description ?? $page->siteDescription }}">

        <meta property="og:title" content="{{ $page->title ?: $page->siteName }}"/>
        <meta property="og:type" content="{{ $page->type ?? 'website' }}" />
        <meta property="og:url" content="{{ $page->getUrl() }}"/>
        <meta property="og:description" content="{{ $page->description ?? $page->siteDescription }}" />

        <title>{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}</title>

        <link rel="home" href="{{ $page->baseUrl }}">
        <link rel="icon" href="/favicon.svg">
        <link href="/blog/feed.atom" type="application/atom+xml" rel="alternate" title="{{ $page->siteName }} Atom Feed">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Fira+Code:wght@300..700&display=swap" rel="stylesheet">

        @if ($page->production)
            <!-- Insert analytics code here -->
        @endif

        <link rel="stylesheet" href="{{ mix('css/main.css', 'assets/build') }}">

        <script>
            if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.querySelector('html').classList.add('dark');
            } else {
                document.querySelector('html').classList.remove('dark');
            }
        </script>
    </head>

    <body class="flex flex-col justify-between min-h-screen leading-normal max-w-4xl mx-auto px-6">
        <header class="relative flex items-center h-20 lg:h-24" role="banner">
            <div class="container flex items-center max-w-8xl mx-auto">
                <div class="flex items-center">
                    <a href="/" title="{{ $page->siteName }} home" class="inline-flex items-center">
                        <h1 class="site-title my-0">
                            <span class="">//</span> {{ $page->siteName }}
                        </h1>
                    </a>
                </div>

                <div class="flex flex-1 justify-end items-center">
                    @include('_nav.menu')

                    <div class="hidden lg:block">
                        @include('_nav.light-switch')
                    </div>

                    @include('_nav.menu-toggle')

                    <div class="hidden lg:block">
                        @include('_nav.search-form')
                    </div>
                </div>
            </div>
        </header>

        @include('_nav.menu-responsive')

        <main role="main" class="flex-auto w-full container py-16">
            @yield('body')
        </main>

        <footer class="text-center text-xs mt-8 py-4" role="contentinfo">
            <ul class="flex flex-col md:flex-row justify-center list-none font-sans uppercase">
                <li class="md:mr-2">
                    {{ $page->siteName }} •
                    Built with <a href="https://jigsaw.tighten.co" title="Jigsaw by Tighten">Jigsaw</a>
                    and <a href="https://tailwindcss.com" title="Tailwind CSS, a utility-first CSS framework">Tailwind CSS</a>.
                </li>
            </ul>
        </footer>

        <script src="{{ mix('js/main.js', 'assets/build') }}"></script>

        @stack('scripts')

    </body>
</html>
