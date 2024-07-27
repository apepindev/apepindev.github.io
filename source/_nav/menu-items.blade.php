@if ($posts->count() > 0)
<a title="{{ $page->siteName }} Posts" href="/posts"
   class="flex my-1 lg:my-0 lg:ml-6 {{ $page->isActive('/posts/') ? 'active' : '' }}">
    posts
</a>
@endif

<a title="{{ $page->siteName }} About" href="/about"
   class="flex my-1 lg:my-0 lg:ml-6 {{ $page->isActive('/about/') ? 'active' : '' }}">
    about
</a>

{{--<a title="{{ $page->siteName }} Contact" href="/contact"--}}
{{--   class="lg:ml-6 {{ $page->isActive('/contact/') ? 'active' : '' }}">--}}
{{--    contact--}}
{{--</a>--}}