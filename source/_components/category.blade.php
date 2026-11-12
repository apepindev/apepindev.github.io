@if ($cat = $categories->firstWhere('_meta.filename', $category))
    <a href="{{ '/posts/categories/' . $category }}" title="View posts in {{ $cat->title }}" class="tag">{{ $cat->title }}</a>
@else
    <span class="tag">{{ $category }}</span>
@endif
