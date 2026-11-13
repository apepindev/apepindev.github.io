{{--
    Isometric drawing in CSS 3D of one desk item: a laptop, a cup of coffee or thick framed
    glasses. Fronts face south-west. Items are sized with the scale property, so `transform`
    stays free for animation. The inline script picks the item on each load, and a click
    toggles that item's loop: scrolling code, rising steam or a sheen across the lenses.
--}}
<button type="button" class="desk" data-show="laptop" aria-pressed="false" aria-label="Isometric drawing of a laptop. Press to animate.">
    <span class="desk-stage">
        <span class="desk-ground"></span>

        <span class="desk-item desk-laptop" data-desk-item="laptop">
            <x-desk-box :x="-15" :y="-10" :w="30" :d="20" :h="1.2">
                <span class="desk-laptop-keys"></span>
                <span class="desk-laptop-pad"></span>
            </x-desk-box>
            <span class="desk-laptop-hinge">
                <x-desk-box :x="0" :y="-19" :w="30" :d="19" :h="0.8">
                    <span class="desk-laptop-screen">
                        {{-- Two identical copies, so scrolling by one copy's height loops without a jump. --}}
                        <span class="desk-laptop-scroll">
                            @for ($copy = 0; $copy < 2; $copy++)
                                <span class="desk-laptop-lines">
                                    @foreach ([[40, 0], [62, 2], [48, 2], [30, 4], [55, 4], [36, 2], [20, 0], [52, 0], [44, 2], [26, 2]] as [$len, $indent])
                                        <i style="--len: {{ $len }}%; --indent: {{ $indent }}cqw"></i>
                                    @endforeach
                                </span>
                            @endfor
                        </span>
                    </span>
                </x-desk-box>
            </span>
        </span>

        <span class="desk-item desk-cup" data-desk-item="cup">
            <span class="desk-saucer"></span>
            <span class="desk-saucer desk-saucer-top"></span>
            <span class="desk-cup-handle"></span>
            @for ($i = 0; $i < 20; $i++)
                <span class="desk-cup-side" style="--i: {{ $i }}"></span>
            @endfor
            <span class="desk-cup-outline"></span>
            <span class="desk-cup-coffee"></span>
            <span class="desk-cup-rim"></span>
            <span class="desk-cup-steam">
                @foreach ([[22, 0], [50, 0.9], [78, 1.8]] as [$left, $delay])
                    <svg viewBox="0 0 10 40" style="--left: {{ $left }}%; --delay: {{ $delay }}s" aria-hidden="true"><path d="M5 40 C 1 33, 9 27, 5 20 S 1 7, 5 0" /></svg>
                @endforeach
            </span>
        </span>

        <span class="desk-item desk-glasses" data-desk-item="glasses">
            <x-desk-box class="box-solid" :x="-11" :y="-15" :w="0.7" :d="14.5" :z="5.4" :h="1" />
            <x-desk-box class="box-solid" :x="10.3" :y="-15" :w="0.7" :d="14.5" :z="5.4" :h="1" />
            @foreach (['-0.5cqw', '0.5cqw'] as $offset)
                <span class="desk-glasses-front {{ $loop->last ? 'desk-glasses-front-near' : '' }}" style="--offset: {{ $offset }}">
                    <span class="desk-glasses-lens desk-glasses-lens-l"></span>
                    <span class="desk-glasses-bridge"></span>
                    <span class="desk-glasses-lens desk-glasses-lens-r"></span>
                </span>
            @endforeach
        </span>
    </span>
</button>

<script>
    (function () {
        var desk = document.currentScript.previousElementSibling;
        var labels = { laptop: 'a laptop', cup: 'a cup of coffee', glasses: 'a pair of thick framed glasses' };
        var names = Object.keys(labels);

        // Skip the item shown on the previous load, when storage allows it.
        var last = null;
        try { last = sessionStorage.getItem('desk-item'); } catch (e) {}
        var choices = names.filter(function (name) { return name !== last; });
        var pick = choices[Math.floor(Math.random() * choices.length)];
        try { sessionStorage.setItem('desk-item', pick); } catch (e) {}

        desk.dataset.show = pick;
        desk.setAttribute('aria-label', 'Isometric drawing of ' + labels[pick] + '. Press to animate.');

        desk.addEventListener('click', function () {
            desk.setAttribute('aria-pressed', desk.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
        });
    })();
</script>
