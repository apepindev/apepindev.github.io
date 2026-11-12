---
title: Posts
description: The list of posts for the site
pagination:
    collection: posts
    perPage: 10
---
@extends('_layouts.main')

@section('body')
    <x-row :first="true" class="px-16 px-sheet pt-20 pb-12 max-md:pt-12 max-md:pb-8">
        <h1 class="text-[clamp(2.5rem,10vw,5rem)] leading-[0.98] font-extrabold tracking-[-0.04em]">Posts</h1>
        <p class="mt-5 text-xl text-muted">{{ $posts->count() }} {{ $posts->count() === 1 ? 'post' : 'posts' }}, newest first.</p>
    </x-row>

    <x-row>
        @include('_components.schedule-head')

        @foreach ($pagination->items as $post)
            @include('_components.post-row')
        @endforeach

        @if ($pagination->pages->count() > 1)
            <nav class="flex flex-wrap items-center gap-2 px-16 px-sheet py-8 border-t border-rule font-mono text-xs" aria-label="Pagination">
                @if ($previous = $pagination->previous)
                    <a href="{{ $previous }}" class="icon-btn" aria-label="Previous page">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                    </a>
                @endif

                @foreach ($pagination->pages as $pageNumber => $path)
                    <a
                        href="{{ $path }}"
                        class="icon-btn {{ $pagination->currentPage == $pageNumber ? '!bg-hl !text-[var(--hl-ink)] !border-solid' : '' }}"
                        aria-label="Page {{ $pageNumber }}"
                        @if ($pagination->currentPage == $pageNumber) aria-current="page" @endif
                    >{{ $pageNumber }}</a>
                @endforeach

                @if ($next = $pagination->next)
                    <a href="{{ $next }}" class="icon-btn" aria-label="Next page">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endif
            </nav>
        @endif
    </x-row>
@stop
