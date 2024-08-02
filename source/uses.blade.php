---
title: Contact
description: Get in touch with us
---
@extends('_layouts.main')

@section('body')
<h1>What I use</h1>

<p class="lg:mb-8">Here's what helps me do my day-to-day.</p>

<div class="flow-root py-3">
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
        <h5><a href="https://www.jetbrains.com/">JetBrains IDEs</a></h5>
        <p>
            My go-to IDE is anything by JetBrains. They all come pre-packed with tons of features
            that just make my life easier. Currently I'm using PhpStorm and PyCharm.
        </p>

        <h5><a href="https://iterm2.com/">iTerm2</a></h5>
        <p>
            Pretty sure this is a common terminal alternative everyone uses on MacOS. The shell I
            use is ZSH (<a href="https://ohmyz.sh/">Oh My Zsh</a>) with <a href="https://github.com/romkatv/powerlevel10k">Powerlevel10k</a>.
        </p>

        <h5><a href="https://orbstack.dev/">OrbStack</a></h5>
        <p>
            A drop-in replacement to Docker for Mac, and a snappy one too. Works really well x86
            emulation and requires less resources.
        </p>

        <h5><a href="https://github.com/usebruno/bruno">Bruno</a></h5>
        <p class="mb-0">
            The only "offline" API client with Git collaboration in mind. It's fairly new to the
            scene but it has already gained a lot of attention and works great.
        </p>
    </x-uses-section>

    <x-uses-section heading="Other software">
        <h5><a href="https://arc.net/">Arc</a></h5>
        <p>
            Just recently made the switch from Brave and I'm really impressed with it. Comes with
            some nice features for development too.
        </p>

        <h5><a href="https://zed.dev/">Zed</a></h5>
        <p>
            From the creators of the Atom, they created a text editor that rivals Sublime Text,
            which is very impressive!
        </p>

        <h5><a href="https://boop.okat.best/">Boop</a></h5>
        <p class="mb-0">
            A neat text manipulation tool that already comes with an array of useful scripts. You
            can also create your own scripts too.
        </p>
    </x-uses-section>

    <x-uses-section heading="Peripherals">
        <div class="leading-tight">
            <h6>Gigabyte M28U Monitor</h6>
            <h6>Keychron V1 QMK Keyboard</h6>
            <h6>Apple Magic Trackpad</h6>
            <h6>Deltahub Carpio 2.0 & G2.0</h6>
            <h6>Elgato Stream Deck XL</h6>
            <h6>Elgato Wave:3 Microphone</h6>
            <h6>Elgato Facecam</h6>
            <h6>Elgato Key Light Air</h6>
            <h6 class="mb-0">BenQ ScreenBar Halo</h6>
        </div>
    </x-uses-section>
</div>
@stop
