---
title: Uses
description: The hardware and software Andrew uses day to day
---
@extends('_layouts.main')

@section('body')
<x-row :first="true" class="px-16 px-sheet pt-20 pb-12 max-md:pt-12 max-md:pb-8">
    <h1 class="text-[clamp(2.5rem,10vw,5rem)] leading-[0.98] font-extrabold tracking-[-0.04em]">What I use</h1>
    <p class="mt-5 text-xl text-muted">Here's what helps me do my day-to-day.</p>
</x-row>

<x-row>
    <x-uses-section heading="Workstations">
        <h5>13-inch, M1, 2020 MacBook Pro</h5>
        <p>
            This is my personal machine. With 16GB memory and 512GB storage it has plenty of power
            for what I need it for.
        </p>

        <h5>16-inch, M1 Pro, 2021 MacBook Pro</h5>
        <p class="mb-0">
            This is my work machine and it's quite a bit more powerful! With a faster chipset, 32GB
            memory and 1TB of storage it makes light work with the software and containers I run.
        </p>
    </x-uses-section>

    <x-uses-section heading="Development software">
        <h5><a class="btn-link hl-hover" href="https://www.jetbrains.com/">JetBrains IDEs</a></h5>
        <p>
            My go-to IDE is anything by JetBrains. They all come pre-packed with tons of features
            that just make my life easier. Currently I'm using PhpStorm, PyCharm, GoLand and DataGrip.
        </p>

        <h5><a class="btn-link hl-hover" href="https://zed.dev/">Zed</a></h5>
        <p>
            When I just need a fast text editor rather than a feature rich IDE for scripts and small
            projects.
        </p>

        <h5><a class="btn-link hl-hover" href="https://ghostty.org/">Ghostty</a>/<a class="btn-link hl-hover" href="https://cmux.com/">cmux</a></h5>
        <p>
            Pretty sure this is a common terminal alternative everyone uses on MacOS. The shell I
            use is a custom, lightweight variant of (<a class="btn-link hl-hover" href="https://ohmyz.sh/">Oh My Zsh</a>).
        </p>

        <h5><a class="btn-link hl-hover" href="https://orbstack.dev/">OrbStack</a></h5>
        <p>
            A drop-in replacement to Docker for Mac, and a snappy one too. Works really well x86
            emulation and requires less resources.
        </p>

        <h5><a class="btn-link hl-hover" href="https://github.com/usebruno/bruno">Bruno</a></h5>
        <p class="mb-0">
            The only "offline" API client with Git collaboration in mind. It's fairly new to the
            scene but it has already gained a lot of attention and works great.
        </p>
    </x-uses-section>

    <x-uses-section heading="Other software">
        <h5><a class="btn-link hl-hover" href="https://zen-browser.app/">Zen</a></h5>
        <p>
            Just recently made the switch from Arc (RIP) and I'm really impressed with it. It was
            quite a shift from a Chromium browser to a Firefox one but it's working great for me.
        </p>

        <h5><a class="btn-link hl-hover" href="https://boop.okat.best/">Boop</a></h5>
        <p class="mb-0">
            A neat text manipulation tool that already comes with an array of useful scripts. Really
            handy when I just need to do quick formatting.
        </p>
    </x-uses-section>

    <x-uses-section heading="Peripherals">
        <div class="leading-tight">
            <h6>Gigabyte M28U Monitor</h6>
            <h6>Keychron Q1 HE Keyboard</h6>
            <h6>Apple Magic Trackpad</h6>
            <h6>Deltahub Carpio 2.0 & G2.0</h6>
            <h6>Elgato Stream Deck XL</h6>
            <h6>Elgato Wave:3 Microphone</h6>
            <h6>Elgato Facecam</h6>
            <h6>Elgato Key Light Air</h6>
            <h6 class="mb-0">BenQ ScreenBar Halo</h6>
        </div>
    </x-uses-section>
</x-row>
@stop
