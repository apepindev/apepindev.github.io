---
title: About
description: A little bit about the site
---
@extends('_layouts.main')

@section('body')
    <x-row :first="true" class="px-16 px-sheet pt-20 pb-12 max-md:pt-12 max-md:pb-8">
        <h1 class="text-5xl md:text-[5rem] leading-[0.98] font-extrabold tracking-[-0.04em]">About</h1>
    </x-row>

    <x-row>
        <div class="grid grid-cols-[minmax(0,1fr)] gap-12 md:grid-cols-[minmax(0,1fr)_20rem] px-16 px-sheet py-14 max-md:py-10">
            <div class="prose">
                <blockquote>
                    <p>"Luck is the residue of design." John Milton</p>
                </blockquote>

                <p>I'm Andrew, and that's me with Gia, the most beautiful and kindest soul whom I'm lucky enough to
                    call my wife. I'm a Senior Software Engineer at Kamma and I'm having the best time there. I can't ask for more
                    where everyday I'm learning something new within a healthy company culture where everyone supports each other,
                    all the way from the C-suite down. Life is great!</p>

                <p>I'm primarily a backend PHP/Laravel developer but also work with Python and Node.js. In my spare
                    time I like to keep up with the latest software/tech news as well as work on some personal projects and try out
                    new languages, frameworks and updates. For leisure, enjoying spending time with Gia, indulging in the playful
                    antics of my cats (Remi and Oscar), immersing myself in gaming on my PC (currently hooked on Valorant), and
                    jamming along to music on my e-drum conversion kit.</p>
            </div>

            <figure class="mt-6 md:mt-0 order-first md:order-none">
                <div class="frame p-3">
                    <img src="/assets/img/me_and_gia.jpg" alt="Andrew and Gia" class="block w-full aspect-square object-cover">
                </div>
                <figcaption class="fig-caption"><span>FIG. 1</span><span>Andrew and Gia</span></figcaption>
            </figure>
        </div>
    </x-row>
@endsection
