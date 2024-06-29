<label class="flex justify-center items-center select-none h-10 lg:ml-6">
    <input type="checkbox" name="light-switch" class="light-switch hidden"/>
    <svg class="light-switch-sun w-6 h-6 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3V1m0 18v-2M5.05 5.05 3.636 3.636m12.728 12.728L14.95 14.95M3 10H1m18 0h-2M5.05 14.95l-1.414 1.414M16.364 3.636 14.95 5.05M14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/>
    </svg>

    <svg class="light-switch-moon w-6 h-6 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.509 5.75c0-1.493.394-2.96 1.144-4.25h-.081a8.5 8.5 0 1 0 7.356 12.746A8.5 8.5 0 0 1 8.509 5.75Z"/>
    </svg>
</label>

@push('scripts')
<script>
    if (document.querySelector('html').classList.contains('dark')) {
        document.querySelectorAll('.light-switch-moon').forEach(el => el.classList.remove('hidden'));
    } else {
        document.querySelectorAll('.light-switch-sun').forEach(el => el.classList.remove('hidden'));
    }
</script>
@endpush