@props(['first' => false])

{{-- A full-width row rule with the centred sheet inside it. Crosshairs mark where the rule meets the rails. --}}
<div class="row {{ $first ? 'row-first' : '' }}">
    <div {{ $attributes->merge(['class' => 'sheet']) }}>
        @unless ($first)
            <span class="reg reg-l" aria-hidden="true"></span>
            <span class="reg reg-r" aria-hidden="true"></span>
        @endunless

        {{ $slot }}
    </div>
</div>
