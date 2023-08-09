@extends('layouts.app')

@section('content')
<x-accessibility.skip-to-content-link />

<div class="min-h-screen dark:bg-dark-700">
    <div class="relative lg:flex lg:items-start">
        <aside
               id="sidebar"
               class="z-20 hidden w-16 h-full min-h-screen overflow-hidden transition-all duration-300 bg-gradient-to-b from-gray-100 to-white lg:sticky lg:w-80 lg:shrink-0 lg:flex lg:flex-col lg:justify-end lg:items-end 2xl:max-w-lg 2xl:w-full dark:from-dark-800 dark:to-dark-700">
            <div class="relative flex flex-col flex-1 min-h-0 xl:w-80">
                <a href="/" class="flex items-center px-4 py-8 lg:px-8 xl:px-16">
                    <span class="hidden text-3xl font-medium text-red-500 lg:block">Laradoc.jp</span>
                </a>
                <div class="px-4 overflow-x-hidden overflow-y-auto lg:overflow-hidden lg:px-8 xl:px-16">
                    <nav id="indexed-nav" class="hidden lg:block lg:mt-4">
                        <div class="docs_sidebar" style="font-family: 'Noto Sans JP', sans-serif;">
                            @include('laravel10/ja/documentation')
                        </div>
                    </nav>
                </div>
                <div class="flex flex-col justify-end flex-grow">
                    @if ($page !== 'introduction')
                    <div class="hidden pl-16 mb-12 2xl:block">
                        <x-cube delay="0" class="ml-8" />
                        <x-cube delay="2000" class="mt-6 ml-32" />
                        <x-cube delay="1000" class="mt-12" />
                    </div>
                    @endif
                </div>
            </div>
        </aside>

        <header
                id="header"
                class="lg:hidden"
                @keydown.window.escape="navIsOpen = false"
                @click.outside="navIsOpen = false">
            <div class="relative w-full py-10 mx-auto transition duration-200 bg-white dark:bg-dark-700">
                <div class="flex items-center justify-between px-8 mx-auto sm:px-16">
                    <a href="/" class="flex items-center">
                        <span class="text-3xl font-medium text-red-500 sm:block">Laradoc.jp</span>
                    </a>
                    <div class="flex items-center justify-end flex-1">
                        <button id="header__sun" onclick="toSystemMode()" title="Switch to system theme" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sun" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7"></path>
                            </svg>
                        </button>
                        <button id="header__moon" onclick="toLightMode()" title="Switch to light mode" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z" />
                            </svg>
                        </button>
                        <button id="header__indeterminate" onclick="toDarkMode()" title="Switch to dark mode" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2M12 4A8 8 0 0 1 20 12A8 8 0 0 1 12 20V4Z" />
                            </svg>
                        </button>
                        <button class="relative w-10 h-10 p-2 ml-2 text-red-600 lg:hidden focus:outline-none focus:shadow-outline" aria-label="Menu" @click.prevent="navIsOpen = !navIsOpen">
                            <svg x-show="! navIsOpen" x-transition.opacity class="absolute inset-0 w-6 h-6 mt-2 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <svg x-show="navIsOpen" x-transition.opacity x-cloak class="absolute inset-0 w-6 h-6 mt-2 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <span :class="{ 'shadow-sm': navIsOpen }" class="absolute inset-0 z-20 pointer-events-none"></span>
            </div>
            <div
                 x-show="navIsOpen"
                 x-transition:enter="duration-150"
                 x-transition:leave="duration-100 ease-in"
                 x-cloak>
                <nav
                     x-show="navIsOpen"
                     x-cloak
                     class="absolute z-10 w-full origin-top transform shadow-sm"
                     x-transition:enter="duration-150 ease-out"
                     x-transition:enter-start="opacity-0 -translate-y-8 scale-75"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="duration-100 ease-in"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-8 scale-75">
                    <div class="relative p-8 bg-white docs_sidebar dark:bg-dark-600">
                        @include('laravel10/ja/documentation')
                    </div>
                </nav>
            </div>
        </header>

        <section class="flex-1 dark:bg-dark-700">
            <span class="hidden dark:hidden fixed -bottom-[350px] ml-[300px] bg-radial-gradient opacity-[.15] pointer-events-none lg:inline-flex w-[800px] h-[600px]"></span>
            <div class="px-8 sm:px-16 lg:px-24">

                <div class="w-full lg:w-40 lg:pl-12">
                    <div>
                        <label class="text-xs tracking-widest text-gray-600 uppercase dark:text-gray-500" for="version-switcher">Version</label>
                        <div x-data="" class="relative w-full p-0 transition-all duration-500 bg-white focus-within:border-gray-600 dark:bg-gray-800">
                            <select
                                    class="flex-1 w-full px-0 py-1 tracking-wide placeholder-gray-900 bg-white appearance-none focus:outline-none dark:bg-dark-700 dark:text-gray-400 dark:placeholder-gray-500"
                                    @change="window.location = $event.target.value">
                                <option value="" class="bg-gray-100" disabled>Laravel</option>
                                <option value="/laravel10/ja/installation">10.x</option>
                                <option value="" class="bg-gray-100" disabled>Livewire</option>
                                <option value="/livewire3/ja/installation">3.x</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-end max-w-screen-lg transition-colors dark:border-gray-700 lg:mt-8 lg:flex-row-reverse">
                    <div class="fixed items-center justify-center hidden top-8 lg:flex">
                        <button id="header__sun" onclick="toSystemMode()" title="Switch to system theme" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sun" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="4"></circle>
                                <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7"></path>
                            </svg>
                        </button>
                        <button id="header__moon" onclick="toLightMode()" title="Switch to light mode" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z" />
                            </svg>
                        </button>
                        <button id="header__indeterminate" onclick="toDarkMode()" title="Switch to dark mode" class="relative w-10 h-10 text-gray-500 focus:outline-none focus:shadow-outline">
                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2M12 4A8 8 0 0 1 20 12A8 8 0 0 1 12 20V4Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <section class="flex pt-8 pb-8 md:pt-16 md:pb-16">
                    <section class="flex-1 max-w-full docs_main">
                        <x-accessibility.main-content-wrapper>

                            <div class="relative prose dark:prose-invert">
                                @include($page)
                            </div>
                            <script>
                                // Set the active navigation state...
                                    Array.from(document.querySelectorAll('#header a, #sidebar a')).forEach(link => {
                                        if (link.hostname === location.hostname
                                            && (link.pathname === location.pathname || (link.pathname === '/introduction' && location.pathname === '/'))
                                        ) {
                                            link.classList.add('active')
                                            if (link.parentNode.tagName === 'LI') {
                                                link.parentNode.parentNode.parentNode.classList.add('sub--on')
                                            }
                                        }
                                    })

                                    // Make the navigation headings expandable...
                                    Array.from(document.querySelectorAll('.docs_sidebar h2')).forEach(el => {
                                        if (el.children.length > 1) {
                                            return
                                        }

                                        el.addEventListener('click', (e) => {
                                            const active = el.parentNode.classList.contains('sub--on');

                                            [...document.querySelectorAll('.docs_sidebar ul li')].forEach(el => el.classList.remove('sub--on'));

                                            if (! active) {
                                                el.parentNode.classList.add('sub--on');
                                            }
                                        })
                                    })

                                    // Highlight the active section in the table of contents...
                                    function setActiveTableOfContents () {
                                        const links = Array.from(document.querySelectorAll('.table-of-contents a'))
                                        const lastVisible = links
                                            .slice()
                                            .reverse()
                                            .find(link => {
                                                const el = document.querySelector(link.hash)

                                                return el.getBoundingClientRect().top <= 56;
                                            }) ?? links[0]

                                        links.forEach(link => {
                                            if (link === lastVisible) {
                                                link.classList.add('active')
                                            } else {
                                                link.classList.remove('active')
                                            }
                                        })
                                    }

                                    setActiveTableOfContents()
                                    window.addEventListener('scroll', setActiveTableOfContents, { passive: true })
                            </script>
                            {{-- <script async type="text/javascript" src="//cdn.carbonads.com/carbon.js?serve=CKYILK3E&placement=laravelcom" id="_carbonads_js"></script> --}}
                        </x-accessibility.main-content-wrapper>
                    </section>
                </section>
            </div>
        </section>
    </div>

    <footer class="relative pt-12 mt-12 bg-gradient-to-b from-gray-50 to-white dark:from-dark-800 dark:to-dark-700">
        <div class="w-full px-8 mx-auto max-w-screen-2xl">
            <div>
                <a href="https://laravel.com" class="inline-flex">
                    <img class="h-14" src="/img/logo.min.svg" alt="Laravel" loading="lazy">
                </a>
            </div>
            <div class="mt-6 sm:mt-12">
                <p class="max-w-sm text-xs text-gray-700 sm:text-sm dark:text-gray-500">
                    {{-- Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in most web projects. --}}
                    Laravel は、表現力豊かで洗練された構文を備えた Web アプリケーション フレームワークです。 開発が真に充実したものになるためには、楽しく創造的な経験でなければならないと考えています。 Laravel は、ほとんどの Web プロジェクトで使用される一般的なタスクを緩和することで、開発の苦痛を取り除こうとします。
                </p>
                <ul class="flex items-center mt-6 space-x-3">
                    <li>
                        <a href="https://twitter.com/laravelphp">
                            <img id="footer__twitter_dark" class="hidden w-6 h-6 dark:inline-block" src="/img/social/twitter.dark.min.svg" alt="Twitter" loading="lazy" width="24" height="20">
                            <img id="footer__twitter" class="inline-block w-6 h-6 dark:hidden" src="/img/social/twitter.min.svg" alt="Twitter" loading="lazy" width="24" height="20">
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/laravel">
                            <img id="footer__github_dark" class="hidden w-6 h-6 dark:inline-block" src="/img/social/github.dark.min.svg" alt="GitHub" loading="lazy" width="24" height="24">
                            <img id="footer__github" class="inline-block w-6 h-6 dark:hidden" src="/img/social/github.min.svg" alt="GitHub" loading="lazy" width="24" height="24">
                        </a>
                    </li>
                    <li>
                        <a href="https://discord.gg/mPZNm7A">
                            <img id="footer__discord_dark" class="hidden w-6 h-6 dark:inline-block" src="/img/social/discord.dark.min.svg" alt="Discord" loading="lazy" width="21" height="24">
                            <img id="footer__discord" class="inline-block w-6 h-6 dark:hidden" src="/img/social/discord.min.svg" alt="Discord" loading="lazy" width="21" height="24">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/laravelphp">
                            <img id="footer__youtube_dark" class="hidden w-6 h-6 dark:inline-block" src="/img/social/youtube.dark.min.svg" alt="YouTube" loading="lazy" width="169" height="150">
                            <img id="footer__youtube" class="inline-block w-6 h-6 dark:hidden" src="/img/social/youtube.min.svg" alt="YouTube" loading="lazy" width="169" height="150">
                        </a>
                    </li>
                </ul>
            </div>
            <div class="pt-6 pb-16 mt-10 border-t border-gray-200 dark:border-dark-500">
                <p class="text-xs text-gray-700 dark:text-gray-400">
                    Laravel is a Trademark of Taylor Otwell. Copyright © Laravel LLC.
                </p>
                <p class="mt-6 text-xs text-gray-700 dark:text-gray-400">
                    Code highlighting provided by <a href="https://torchlight.dev">Torchlight</a>
                </p>
            </div>
        </div>
    </footer>
</div>
@endsection