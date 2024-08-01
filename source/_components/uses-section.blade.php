<dl class="-my-3 text-sm">
    <div class="grid grid-cols-1 sm:grid-cols-4 mb-4">
        <dt class="leading-tight mb-4 pr-4">
            <h3>{{ $heading }}</h3>
        </dt>
        <dd class="sm:col-span-3 mb-6 pl-6 border-l border-gray-300 dark:border-neutral-700 uses-border">
            {{ $slot }}
        </dd>
    </div>
</dl>
