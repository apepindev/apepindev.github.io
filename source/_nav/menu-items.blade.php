<a title="{{ $page->siteName }} Blog" href="/blog"
   class="mb-2 lg:mb-0 lg:ml-6 {{ $page->isActive('/blog/') ? 'active' : '' }}">
    Blog
</a>

<a title="{{ $page->siteName }} About" href="/about"
   class="mb-2 lg:mb-0 lg:ml-6 {{ $page->isActive('/about/') ? 'active' : '' }}">
    About
</a>

{{--<a title="{{ $page->siteName }} Contact" href="/contact"--}}
{{--   class="lg:ml-6 {{ $page->isActive('/contact/') ? 'active' : '' }}">--}}
{{--    Contact--}}
{{--</a>--}}