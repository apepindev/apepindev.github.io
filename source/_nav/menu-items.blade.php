<a title="{{ $page->siteName }} Posts" href="/posts"
   class="mb-2 lg:mb-0 lg:ml-6 {{ $page->isActive('/posts/') ? 'active' : '' }}">
    posts
</a>

<a title="{{ $page->siteName }} About" href="/about"
   class="mb-2 lg:mb-0 lg:ml-6 {{ $page->isActive('/about/') ? 'active' : '' }}">
    about
</a>

{{--<a title="{{ $page->siteName }} Contact" href="/contact"--}}
{{--   class="lg:ml-6 {{ $page->isActive('/contact/') ? 'active' : '' }}">--}}
{{--    contact--}}
{{--</a>--}}