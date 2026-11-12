<button id="menu-toggle" type="button" class="icon-btn lg:hidden" aria-controls="js-nav-menu" aria-expanded="false" aria-label="Open menu" onclick="navMenu.toggle()">
    <svg id="js-nav-menu-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
        <path d="M4 7h16M4 12h16M4 17h16"/>
    </svg>
    <svg id="js-nav-menu-hide" class="hidden" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
        <path d="M6 6l12 12M18 6L6 18"/>
    </svg>
</button>

@push('scripts')
<script>
    const navMenu = {
        toggle() {
            const menu = document.getElementById('js-nav-menu');
            const button = document.getElementById('menu-toggle');
            const open = menu.classList.toggle('hidden') === false;

            document.getElementById('js-nav-menu-hide').classList.toggle('hidden', !open);
            document.getElementById('js-nav-menu-show').classList.toggle('hidden', open);
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        },
    }
</script>
@endpush
