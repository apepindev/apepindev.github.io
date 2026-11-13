<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $page->description ?? $page->siteDescription }}">

        <meta property="og:title" content="{{ $page->title ?: $page->siteName }}"/>
        <meta property="og:type" content="{{ $page->type ?? 'website' }}" />
        <meta property="og:url" content="{{ $page->getUrl() }}"/>
        <meta property="og:description" content="{{ $page->description ?? $page->siteDescription }}" />

        <title>{{ $page->title ?  $page->title . ' | ' : '' }}{{ $page->siteName }}</title>

        <link rel="home" href="{{ $page->baseUrl }}">
        <link rel="icon" href="/favicon.svg">
        <link href="/rss/feed.atom" type="application/atom+xml" rel="alternate" title="{{ $page->siteName }} Atom Feed">

        <link rel="preload" href="/assets/fonts/FiraCodeNerdFont-Regular.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/assets/fonts/SchibstedGrotesk-Latin.woff2" as="font" type="font/woff2" crossorigin>

        @if ($page->production)
            <script src="https://steadyer.app/beacon.js" data-beacon-key="lNCoL9KeTG6xq5Q4AS6lmqMvGjYVd7lY" async></script>
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

    <!-- steadyer-test-marker -->
    <body class="flex flex-col min-h-screen">
        <!--
        THESIS: The blog is a technical drawing of one engineer's work. A sheet between two rails, crosshair marks, a post schedule and a title block replace the generic hero-plus-cards template.
        OWN-WORLD: White sheet #FFFFFF on a dotted margin, ink #111418, dashed secondary rules, hatched bands, lime highlighter #D9F24A. Schibsted Grotesk for text, FiraCode Nerd Font for labels, dates and code. Dark mode swaps the same variables.
        STORY: Readers see who Andrew is and his stack, open the latest post, scan the schedule, and find the tools he uses.
        FIRST VIEWPORT: Header row with the // apepin.dev mark, nav, search and theme switch. Large "Hi, I'm Andrew." left, memoji in a measured dashed frame right.
        FORM: Prototype A (drafting sheet) with prototype C's colour and type, chosen by the user on 2026-11-12. Seed key: none, the brief pinned the world.
        FINISH: unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, and DESIGN.md
        -->
        <header role="banner">
            <x-row :first="true">
                <div class="site-header px-16 px-sheet">
                    <a href="/" class="site-title" title="{{ $page->siteName }} home">// {{ $page->siteName }}</a>

                    <div class="flex items-center gap-3 md:gap-6">
                        @include('_nav.menu')
                        @include('_nav.search-form')
                        @include('_nav.light-switch')
                        @include('_nav.menu-toggle')
                    </div>
                </div>
            </x-row>
            @include('_nav.menu-responsive')
        </header>

        <div class="hatch" aria-hidden="true"></div>

        <main role="main" class="flex-auto">
            @yield('body')
        </main>

        <div class="hatch" aria-hidden="true"></div>

        <x-row :first="true" class="px-16 px-sheet pt-12 pb-14 max-md:pt-6 max-md:pb-8">
            <footer role="contentinfo" class="title-block">
                <div>
                    <p class="label">Title</p>
                    <p class="font-mono text-sm font-medium mt-1.5">// {{ $page->siteName }}</p>
                </div>
                <div>
                    <p class="label">Drawn by</p>
                    <p class="mt-1.5 font-medium">Andrew Pepin</p>
                </div>
                <div>
                    <p class="label">Built with</p>
                    <p class="mt-1.5">
                        <a href="https://jigsaw.tighten.co" class="hl-hover underline underline-offset-4 decoration-1">Jigsaw</a>
                        + <a href="https://tailwindcss.com" class="hl-hover underline underline-offset-4 decoration-1">Tailwind</a>
                    </p>
                </div>
                <div>
                    <p class="label">Feed</p>
                    <p class="mt-1.5"><a href="/rss/feed.atom" class="hl-hover underline underline-offset-4 decoration-1">RSS / Atom</a></p>
                </div>
                <div class="tb-note">
                    <p class="label">Note</p>
                    <p class="mt-1.5 text-muted">"Luck is the residue of design." John Milton</p>
                </div>
                <div>
                    <p class="label">Last rev.</p>
                    <p class="font-mono text-sm mt-1.5">{{ $posts->count() ? date('Y-m-d', $posts->first()->date) : '' }}</p>
                </div>
                <div>
                    <p class="label">Posts</p>
                    <p class="font-mono text-sm mt-1.5">{{ $posts->count() }}</p>
                </div>
                <div class="tb-sheet">
                    <p class="label">Sheet</p>
                    <p class="font-mono text-sm mt-1.5">1 of 1</p>
                </div>
            </footer>
        </x-row>

        <script src="{{ mix('js/main.js', 'assets/build') }}"></script>

        @stack('scripts')
    </body>
</html>
