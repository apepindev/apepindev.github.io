@extends('_layouts.main')

@section('body')
    <x-row :first="true" class="px-16 px-sheet pt-20 pb-16 max-md:pt-12 max-md:pb-10">
        <a href="/posts" class="btn-link hl-hover font-mono !font-normal text-xs">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            All posts
        </a>

        <h1 class="mt-7 mb-5 text-[clamp(2.5rem,10vw,5rem)] leading-[0.98] font-extrabold tracking-[-0.04em] max-w-[16ch]">{{ $page->title }}</h1>

        @if ($page->description)
            <p class="text-xl leading-normal text-muted max-w-[46ch]">{{ $page->description }}</p>
        @endif
    </x-row>

    <x-row>
        <div class="article-grid">
            <aside class="article-meta flex flex-col gap-6 border-r border-dashed border-[var(--rule-soft)]">
                <div>
                    <p class="label mb-1.5">Published</p>
                    <time datetime="{{ date('Y-m-d', $page->date) }}" class="font-mono text-xs">{{ date('Y-m-d', $page->date) }}</time>
                </div>
                <div>
                    <p class="label mb-1.5">Read</p>
                    <p class="font-mono text-xs">{{ $page->getReadTime() }}</p>
                </div>
                @if ($page->categories)
                    <div>
                        <p class="label mb-2">Filed under</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($page->categories as $category)
                                @include('_components.category')
                            @endforeach
                        </div>
                    </div>
                @endif
                <div>
                    <p class="label mb-1.5">Author</p>
                    <p class="font-mono text-xs">{{ $page->author }}</p>
                </div>
            </aside>

            <article class="prose" v-pre>
                @if ($page->cover_image)
                    <img src="{{ $page->cover_image }}" alt="{{ $page->title }} cover image">
                    @if ($page->cover_credit)
                        <em>Credit {!! $page->cover_credit !!}</em>
                    @endif
                @endif

                @yield('content')
            </article>

            <aside class="article-toc border-l border-dashed border-[var(--rule-soft)]">
                <nav id="toc" class="toc sticky top-6 hidden" aria-label="On this page">
                    <p class="label mb-3">On this page</p>
                </nav>
            </aside>
        </div>
    </x-row>

    <x-row class="px-16 px-sheet py-12">
        <h2 class="text-2xl font-extrabold tracking-[-0.02em] mb-6">Comments</h2>
        @include('_components.comment-thread')
    </x-row>

    @php
        $older = $page->getNext();
        $newer = $page->getPrevious();
    @endphp

    @if ($older || $newer)
        <x-row>
            <nav class="post-nav" aria-label="More posts">
                @if ($older)
                    <a href="{{ $older->getUrl() }}" class="border-r border-dashed border-[var(--rule-soft)]">
                        <span class="label">Older</span>
                        <span class="text-xl font-bold"><span class="hl-target">{{ $older->title }}</span></span>
                    </a>
                @else
                    <span></span>
                @endif

                @if ($newer)
                    <a href="{{ $newer->getUrl() }}" class="items-end text-right">
                        <span class="label">Newer</span>
                        <span class="text-xl font-bold"><span class="hl-target">{{ $newer->title }}</span></span>
                    </a>
                @endif
            </nav>
        </x-row>
    @endif
@endsection

@push('scripts')
<script>
    // Build the "On this page" list from the post's h2 headings and mark the one in view.
    (function () {
        const toc = document.getElementById('toc');
        const headings = document.querySelectorAll('.prose h2');

        if (!toc || headings.length < 2) {
            return;
        }

        const links = [];

        headings.forEach((heading) => {
            const anchor = heading.querySelector('.heading-permalink');

            if (!anchor || !anchor.id) {
                return;
            }

            heading.id = anchor.id;
            anchor.removeAttribute('id');

            const link = document.createElement('a');
            link.href = '#' + heading.id;
            link.textContent = heading.firstChild.textContent.trim();
            toc.appendChild(link);
            links.push({ link, heading });
        });

        toc.classList.remove('hidden');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                links.forEach(({ link, heading }) => link.classList.toggle('active', heading === entry.target));
            });
        }, { rootMargin: '0px 0px -70% 0px' });

        links.forEach(({ heading }) => observer.observe(heading));
    })();
</script>
@endpush
