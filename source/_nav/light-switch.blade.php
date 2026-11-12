<label class="icon-btn" title="Toggle dark mode">
    <input type="checkbox" name="light-switch" class="light-switch sr-only" aria-label="Dark mode"/>
    <svg class="light-switch-sun hidden" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
    </svg>
    <svg class="light-switch-moon hidden" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5z"/>
    </svg>
</label>

@push('scripts')
<script>
    if (document.querySelector('html').classList.contains('dark')) {
        document.querySelectorAll('.light-switch').forEach(el => el.checked = true);
        document.querySelectorAll('.light-switch-moon').forEach(el => el.classList.remove('hidden'));
    } else {
        document.querySelectorAll('.light-switch-sun').forEach(el => el.classList.remove('hidden'));
    }
</script>
@endpush
