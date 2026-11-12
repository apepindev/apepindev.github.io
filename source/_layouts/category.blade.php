@extends('_layouts.main')

@section('body')
    @php $categoryPosts = $page->posts($posts) @endphp

    <x-row :first="true" class="px-16 px-sheet pt-20 pb-12 max-md:pt-12 max-md:pb-8">
        <a href="/posts" class="btn-link hl-hover font-mono !font-normal text-xs">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            All posts
        </a>

        <h1 class="mt-7 text-[clamp(2.5rem,10vw,5rem)] leading-[0.98] font-extrabold tracking-[-0.04em] break-words hyphens-auto">{{ $page->title }}</h1>

        <div class="prose mt-5 text-xl">
            @yield('content')
        </div>

        <p class="label mt-2">{{ $categoryPosts->count() }} {{ $categoryPosts->count() === 1 ? 'post' : 'posts' }}</p>
    </x-row>

    <x-row>
        @if ($categoryPosts->count())
            @include('_components.schedule-head')

            @foreach ($categoryPosts as $post)
                @include('_components.post-row')
            @endforeach
        @else
            <p class="px-16 px-sheet py-10 text-muted">No posts in this category yet. <a href="/posts" class="underline underline-offset-4">See all posts</a>.</p>
        @endif
    </x-row>
@stop
