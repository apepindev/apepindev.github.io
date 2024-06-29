@extends('_layouts.main')

@php $color = 'category-' . ($page->color ?? 'primary') @endphp

@section('body')
    <h1 class="{{ $color }} inline-block category tracking-normal uppercase font-semibold text-4xl rounded-lg px-7 py-1.5">
        {{ $page->title }}
    </h1>

    <div class="text-2xl mb-6 pb-10">
        @yield('content')
    </div>

    @foreach ($page->posts($posts) as $post)
        @include('_components.post-preview-inline')

        @if (! $loop->last)
            <hr class="w-full border-b mt-2 mb-6">
        @endif
    @endforeach

{{--    @include('_components.newsletter-signup')--}}
@stop
