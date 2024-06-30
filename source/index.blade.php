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
        <div class="md:flex w-full mb-6">
            @if ($featuredPost->cover_image)
                <div class="flex content-center items-center overflow-hidden col-auto md:mr-4 md:w-1/3">
                    <img src="{{ $featuredPost->cover_image }}" alt="{{ $featuredPost->title }} cover image" class="bg-cover">
                </div>
            @endif

            <div class="col-auto {{ $featuredPost->cover_image ? 'md:w-2/3' : '' }}">
                @include('_components.post-preview-inline', ['post' => $featuredPost, 'isFeatured' => true])
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
