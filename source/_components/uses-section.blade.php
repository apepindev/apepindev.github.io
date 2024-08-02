<dl class="mt-12 mb-8 lg:mb-0 lg:mt-8 text-sm">
    <div class="grid grid-cols-1 sm:grid-cols-4">
        <dt class="leading-tight mb-4 pr-4">
            <h3 class="font-normal">{{ $heading }}</h3>
        </dt>
        <dd class="sm:col-span-3 pl-6 border-l border-gray-300 dark:border-neutral-700 uses-border">
            {{ $slot }}
        </dd>
    </div>
</dl>
