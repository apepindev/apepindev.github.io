@extends('_layouts.main')

@php $color = 'category-' . ($page->color ?? 'primary') @endphp

@section('body')
{{--    <h1 class="{{ $color }} text-3xl category inline-block tracking-normal uppercase font-semibold rounded-lg px-5 py-1.5">--}}
{{--        {{ $page->title }}--}}
{{--    </h1>--}}

    <h1 class="text-4xl break-words hyphens-auto">Posts tagged with "{{ $page->title }}"</h1>

    <div class="text-xl mb-6 pb-10">
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
