@php
    $class = 'inline-block leading-loose tracking-wide uppercase text-xs font-semibold rounded mr-2 px-3 pt-px';
@endphp

@if($cat = $categories->firstWhere('_meta.filename', $category))
    @php $color = "category-" . ($cat->color ?? 'primary') @endphp

    <a
        href="{{ '/blog/categories/' . $category }}"
        title="View posts in {{ $cat->title }}"
        class="{{ $class }} {{ $color }}"
    >{{ $cat->title }}</a>
@else
    <div
        class="{{ $class }} cursor-default category"
    >{{ $category }}</div>
@endif


