@extends('_layouts.main')

@section('body')
    @php
        $latest = $posts->firstWhere('featured', true) ?? $posts->first();
        $others = $posts->reject(fn ($post) => $latest && $post->getPath() === $latest->getPath())->take(8);
    @endphp

    <x-row :first="true">
        <section class="grid grid-cols-[minmax(0,1fr)] gap-12 md:grid-cols-[minmax(0,1fr)_18.75rem] px-16 px-sheet pt-24 pb-20 max-md:pt-14 max-md:pb-12">
            <div class="flex flex-col justify-center gap-7">
                <h1 class="text-[clamp(2.75rem,11vw,6rem)] leading-[0.95] font-extrabold tracking-[-0.04em]">Hi, I'm {{ $page->siteAuthor }}.</h1>
                <p class="text-xl leading-normal text-muted max-w-[36ch]">Senior Software Engineer at Kamma. This is where I write down what I learn from work and side projects.</p>
                <ul class="flex flex-wrap gap-2 font-mono text-xs">
                    <li class="border border-ink px-2.5 py-1.5"><span class="hl">PHP / Laravel</span></li>
                    <li class="border border-dashed border-ink px-2.5 py-1.5 text-muted">Python</li>
                    <li class="border border-dashed border-ink px-2.5 py-1.5 text-muted">Node.js</li>
                </ul>
            </div>

            <figure class="mt-6 md:mt-0 max-w-[18.75rem]">
                <div class="frame aspect-square grid place-items-center">
                    <span class="dimension" aria-hidden="true"><span>300</span></span>
                    <img src="/assets/img/memoji.png" alt="Andrew's memoji" class="w-[78%] h-auto">
                </div>
                <figcaption class="fig-caption"><span>FIG. 1</span><span>memoji.png</span></figcaption>
            </figure>
        </section>
    </x-row>

    @if ($latest)
        <x-row>
            <div class="grid grid-cols-[minmax(0,1fr)] md:grid-cols-2">
                <a href="{{ $latest->getUrl() }}" class="hl-hover flex flex-col gap-4 px-16 px-sheet py-12 md:border-r md:border-rule">
                    <span class="label">Latest · {{ $latest->getDate()->format('j M Y') }} · {{ $latest->getReadTime() }}</span>
                    <h2 class="text-[2.75rem] max-md:text-4xl leading-[1.05] font-extrabold tracking-[-0.03em]"><span class="hl-target">{{ $latest->title }}</span></h2>
                    @if ($latest->description)
                        <p class="text-[1.0625rem] leading-relaxed text-muted max-w-[46ch]">{{ $latest->description }}</p>
                    @endif
                    <span class="btn-link mt-2">
                        Read the post
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>

                <figure class="flex flex-col min-w-0 border-t border-rule md:border-t-0 bg-[var(--code-bg)] text-[var(--code-ink)]">
                    <figcaption class="flex justify-between gap-4 px-6 py-3.5 border-b border-[var(--code-rule)] font-mono text-[0.6875rem] text-[var(--code-faint)]">
                        <span>config.php</span>
                        <span class="hidden sm:inline">How read times on this site are counted</span>
                    </figcaption>
<pre class="!m-0 !border-0 flex-1"><code class="language-php">'getReadTime' => function ($page) {
    $cleaned = textOnly($page->getContent());

    $time = round(str_word_count($cleaned) / 200);

    if ($time > 0) {
        return "$time " . Str::plural('minute', $time);
    }

    return "&lt; 1 minute";
},</code></pre>
                </figure>
            </div>
        </x-row>
    @endif

    @if ($others->count())
        <x-row>
            <div class="flex flex-wrap gap-x-6 gap-y-2 items-baseline justify-between px-16 px-sheet pt-16 pb-7">
                <h2 class="text-[2rem] font-extrabold tracking-[-0.02em]">Writing</h2>
                <a href="/posts" class="btn-link hl-hover">All {{ $posts->count() }} posts</a>
            </div>

            @include('_components.schedule-head')

            @foreach ($others as $post)
                @include('_components.post-row')
            @endforeach
        </x-row>
    @endif

    <x-row>
        <div class="flex flex-wrap gap-x-6 gap-y-2 items-baseline justify-between px-16 px-sheet pt-16 pb-7">
            <h2 class="text-[2rem] font-extrabold tracking-[-0.02em]">Bill of materials</h2>
            <a href="/uses" class="btn-link hl-hover">Everything I use</a>
        </div>

        <div class="bom">
            <div>
                <p class="label mb-4">Workstations</p>
                <p class="font-bold">16" M1 Pro MacBook Pro</p>
                <p class="text-sm text-muted mb-3.5">Work · 32GB · 1TB</p>
                <p class="font-bold">13" M1 MacBook Pro</p>
                <p class="text-sm text-muted">Personal · 16GB · 512GB</p>
            </div>
            <div>
                <p class="label mb-4">Development</p>
                <ul class="space-y-2 font-bold">
                    <li>JetBrains IDEs</li>
                    <li>iTerm2</li>
                    <li>OrbStack</li>
                    <li>Bruno</li>
                </ul>
            </div>
            <div>
                <p class="label mb-4">Other software</p>
                <ul class="space-y-2 font-bold">
                    <li>Arc</li>
                    <li>Zed</li>
                    <li>Boop</li>
                </ul>
            </div>
            <div>
                <p class="label mb-4">This site</p>
                <ul class="space-y-2 font-bold">
                    <li>Jigsaw</li>
                    <li>Tailwind CSS</li>
                    <li>GitHub Pages</li>
                </ul>
            </div>
        </div>
    </x-row>
@stop
