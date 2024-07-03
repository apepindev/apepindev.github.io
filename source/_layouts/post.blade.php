@extends('_layouts.main')

@section('body')
    @if ($page->cover_image)
        <img src="{{ $page->cover_image }}" alt="{{ $page->title }} cover image">
        @if ($page->image_credit)
            <em>Source {!! $page->image_credit !!}</em>
        @endif
    @endif

    <h1 class="mb-2">{{ $page->title }}</h1>

    <h5 class="post-meta text-sm mt-6 mb-4">
        {{ $page->author }} • {{ date('jS F, Y', $page->date) }} • {{ $page->getReadTime() }}
    </h5>

    @if ($page->categories)
        @foreach ($page->categories as $category)
            @include('_components.category')
        @endforeach
    @endif

    <div class="mb-10 mt-16 lg:mt-24 pb-4" v-pre>
        @yield('content')
    </div>

    <nav class="flex justify-between text-sm md:text-base">
        <div>
            @if ($next = $page->getNext())
                <a href="{{ $next->getUrl() }}" title="Older Post: {{ $next->title }}">
                    &LeftArrow; {{ $next->title }}
                </a>
            @endif
        </div>

        <div>
            @if ($previous = $page->getPrevious())
                <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}">
                    {{ $previous->title }} &RightArrow;
                </a>
            @endif
        </div>
    </nav>

    @include('_components.comment-thread')
@endsection
