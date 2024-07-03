<div class="flex flex-col">
    <h2 class="text-3xl mb-1 mt-0">
        <a
            href="{{ $post->getUrl() }}"
            title="Read more - {{ $post->title }}"
            class="font-semibold font-sans"
        >{{ $post->title }}</a>
    </h2>

    <h5 class="post-meta text-sm mt-0">
        {{ $post->getDate()->format('jS F, Y') }} • {{ $post->getReadTime() }}
    </h5>

    <p class="my-0">{!! $post->getExcerpt(200) !!}</p>

    <div class="flex-row mb-2 mt-4">
        @if ($post->categories)
            @foreach ($post->categories as $category)
                @include('_components.category')
            @endforeach
        @endif
    </div>

    <a
        href="{{ $post->getUrl() }}"
        title="Read more - {{ $post->title }}"
        class="flex justify-end tracking-wide text-sm"
    >Read &RightArrow;</a>
</div>
