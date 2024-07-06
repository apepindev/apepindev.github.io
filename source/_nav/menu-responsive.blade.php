<nav id="js-nav-menu" class="absolute inset-0 hidden lg:hidden">
    <div onclick="navMenu.toggle()" class="fixed transition-colors duration-200 backdrop-blur inset-0"></div>
    <div class="fixed right-0 h-full flex flex-col transition-colors duration-200 shadow bg-gray-200/90 dark:bg-neutral-900/90 items-end w-2/5 md:w-1/5 py-3 pr-8 mr-0 pt-24 mx-4">
        @include('_nav.menu-items')
        @include('_nav.light-switch')
    </div>
</nav>
