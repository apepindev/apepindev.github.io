<div class="flex flex-col">
    <h5 class="text-sm mt-0">
        {{ $post->getDate()->format('jS F, Y') }} • {{ $post->getReadTime() }}
    </h5>

    <h2 class="text-3xl mb-1 mt-0">
        <a
            href="{{ $post->getUrl() }}"
            title="Read more - {{ $post->title }}"
            class="font-bold font-sans"
        >{{ $post->title }}</a>
    </h2>

    <p class="my-0 font-serif">{!! $post->getExcerpt(200) !!}</p>

    <div class="flex-row my-2">
        @if ($post->categories)
            @foreach ($post->categories as $category)
                @include('_components.category')
            @endforeach
        @endif
    </div>

    <a
        href="{{ $post->getUrl() }}"
        title="Read more - {{ $post->title }}"
        class="flex justify-end uppercase tracking-wide text-sm"
    >Read →</a>
</div>
