---
title: About
description: Andrew Pepin, a Senior Software Engineer at Kamma working mostly in PHP and Laravel.
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
                    <p>"Luck is the residue of design." <span class="text-sm">– John Milton</span></p>
                </blockquote>

                <p>I'm Andrew. That's me in the photo with Gia, the kindest and most beautiful person I know, and
                    I'm lucky enough to call her my wife. We're also blessed to have our baby boy Colby, who's the most sweetest and
                    funniest little guy!</p>

                <p>I'm a Systems Reliability Engineer (previously Senior Software Engineer) at Kamma, and I'm having the
                    best time here. I learn something new every day, and the culture is genuine and supportive, from the
                    C-suite down. I couldn't ask for a better company to work for.</p>

                <p>Most of skill-set is backend PHP and Laravel, Python and some JS/TS. As I've recently turned SRE,
                    I'm now focussing on maintaining availability and performance of our services that reside in K8s clusters. I like to keep
                    up with tech news, build personal projects, and try out new languages and frameworks. This site is
                    where I write down what I learn along the way.</p>

                <p>Outside of work, I love spending time with Gia and Colby and with my cat, Remi. I also enjoy playing
                    video games and drumming along to my favourite music on my converted e-drum kit.</p>
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
