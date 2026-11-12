{{-- One row of the post schedule. Expects $post. --}}
<a href="{{ $post->getUrl() }}" class="schedule-row">
    <time datetime="{{ $post->getDate()->format('Y-m-d') }}" class="font-mono text-xs text-faint">{{ $post->getDate()->format('Y-m-d') }}</time>

    <span class="flex flex-col gap-1.5 min-w-0">
        <span><span class="schedule-title">{{ $post->title }}</span></span>
        @if ($post->description)
            <span class="text-[0.9375rem] leading-relaxed text-muted">{{ $post->description }}</span>
        @endif
        <span class="font-mono text-[0.6875rem] text-faint">{{ $post->getReadTime() }}</span>
    </span>

    <span class="flex flex-wrap gap-1.5">
        @foreach ($post->categories ?? [] as $category)
            <span class="tag">{{ $category }}</span>
        @endforeach
    </span>

    <svg class="schedule-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M7 17L17 7M9 7h8v8"/>
    </svg>
</a>
