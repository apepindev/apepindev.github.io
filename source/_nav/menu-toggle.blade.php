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
    // Menu and search share two window events so only one is open at a time:
    // 'nav:open' (detail names who opened) closes the other, 'nav:close' closes both.
    const navMenu = {
        isOpen() {
            return !document.getElementById('js-nav-menu').classList.contains('hidden');
        },
        set(open) {
            const button = document.getElementById('menu-toggle');

            document.getElementById('js-nav-menu').classList.toggle('hidden', !open);
            document.getElementById('js-nav-menu-hide').classList.toggle('hidden', !open);
            document.getElementById('js-nav-menu-show').classList.toggle('hidden', open);
            document.documentElement.classList.toggle('menu-open', open);
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        },
        toggle() {
            const open = !this.isOpen();
            this.set(open);
            if (open) {
                window.dispatchEvent(new CustomEvent('nav:open', { detail: 'menu' }));
            }
        },
    }

    window.addEventListener('nav:open', (event) => {
        if (event.detail !== 'menu') navMenu.set(false);
    });
    window.addEventListener('nav:close', () => navMenu.set(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navMenu.isOpen()) navMenu.set(false);
    });
</script>
@endpush
