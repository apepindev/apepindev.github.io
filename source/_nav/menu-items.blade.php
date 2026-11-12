@if ($posts->count() > 0)
<a href="/posts" class="nav-link {{ $page->isActive('/posts') ? 'active' : '' }}">Posts</a>
@endif

<a href="/uses" class="nav-link {{ $page->isActive('/uses') ? 'active' : '' }}">Uses</a>

<a href="/about" class="nav-link {{ $page->isActive('/about') ? 'active' : '' }}">About</a>
