@extends('_layouts.main')

@section('body')
    <x-row :first="true" class="px-16 px-sheet py-24 max-md:py-14">
        <p class="label">Error 404</p>
        <h1 class="mt-4 text-[6rem] md:text-[10rem] leading-none font-extrabold tracking-[-0.04em]"><span class="hl">404</span></h1>

        <div class="prose mt-8">
            @yield('content')
        </div>

        <a href="/" class="btn-link hl-hover mt-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
            Back to the home page
        </a>
    </x-row>
@endsection
