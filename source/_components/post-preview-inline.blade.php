<div class="flex flex-col mb-4">
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

{{--    <a--}}
{{--        href="{{ $post->getUrl() }}"--}}
{{--        title="Read more - {{ $post->title }}"--}}
{{--        class="uppercase tracking-wide mb-2"--}}
{{--    >Read</a>--}}
</div>
