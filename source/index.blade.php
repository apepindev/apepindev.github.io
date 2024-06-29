@extends('_layouts.main')

@section('body')

    <div class="lg:flex align-middle sm:row-auto col-auto mb-12">
        <img src="/assets/img/memoji.png"
             alt="About image"
             class="bg-gradient-to-br from-primary-300 to-primary-700 rounded-full h-32 w-32 md:h-52 md:w-52 bg-contain md:mx-auto my-auto">

        <div class="self-center ml-0 lg:ml-6 col-auto">
            <p class="mb-6 text-3xl">Hi, I'm <strong>{{ $page->siteAuthor }}</strong>. 👋</p>

            <p class="mb-6">I'm a Senior Software Engineer at Kamma working with PHP/Laravel, Python and Node.js.</p>

            <p class="mb-6">This is where I'll brain dump things I've learnt from work and personal projects.</p>
        </div>
    </div>

    @if ($posts->where('featured', true)->count() > 0)
        <h2 class="mt-20 mb-12">Featured</h2>
    @endif

    @foreach ($posts->where('featured', true) as $featuredPost)

    <div class="lg:flex w-full mb-6">
        @if ($featuredPost->cover_image)
            <img src="{{ $featuredPost->cover_image }}" alt="{{ $featuredPost->title }} cover image" class="bg-contain mb-6 lg:mb-0 col-auto lg:w-1/3 mr-4">
        @endif

        <div class="col-auto">
            <h5 class="text-sm mt-0">
                {{ $featuredPost->getDate()->format('jS F, Y') }} • {{ $featuredPost->getReadTime() }}
            </h5>

            <h2 class="text-3xl mt-0 mb-1">
                <a href="{{ $featuredPost->getUrl() }}" title="Read {{ $featuredPost->title }}" class="font-bold">
                    {{ $featuredPost->title }}
                </a>
            </h2>

            <p class="mt-0 mb-4">{!! $featuredPost->getExcerpt() !!}</p>
        </div>

    </div>

    @if (! $loop->last)
        <hr class="border-b my-6">
    @endif
    @endforeach

{{--    @include('_components.newsletter-signup')--}}

    <h2 class="mt-20 mb-12">Recent</h2>

    @foreach ($posts->where('featured', false)->take(6)->chunk(2) as $row)
        <div class="flex flex-col md:flex-row md:-mx-6">
            @foreach ($row as $post)
                <div class="w-full md:w-1/2 md:mx-6">
                    @include('_components.post-preview-inline')
                </div>

                @if (! $loop->last)
                    <hr class="block md:hidden w-full border-b mt-2 mb-6">
                @endif
            @endforeach
        </div>

        @if (! $loop->last)
            <hr class="w-full border-b mt-2 mb-6">
        @endif
    @endforeach
@stop
