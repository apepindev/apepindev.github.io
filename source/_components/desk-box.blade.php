@props(['x' => 0, 'y' => 0, 'z' => 0, 'w', 'd', 'h'])

{{-- A CSS box on the desk stage: footprint w by d at (x, y), height h, raised by z. Units are cqw. The slot fills the top face. --}}
<span {{ $attributes->merge(['class' => 'box']) }} style="--x: {{ $x }}; --y: {{ $y }}; --z: {{ $z }}; --w: {{ $w }}; --d: {{ $d }}; --h: {{ $h }}">
    <span class="f f-back"></span>
    <span class="f f-left"></span>
    <span class="f f-right"></span>
    <span class="f f-front"></span>
    <span class="f f-top">{{ $slot }}</span>
</span>
