<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <header class="grid max-h-48 w-full grid-cols-5 text-2xl uppercase">
        <div class="border-dark dark:border-light col-span-2 flex items-center justify-center border-r border-b py-10">
            <Link :href="route('home')">
                <AppLogo weight="bold" />
            </Link>
        </div>

        <div class="border-dark dark:border-light flex flex-col border-r">
            <div class="border-dark dark:border-light relative flex h-1/2 justify-center border-b">
                <div class="bg-light dark:bg-dark absolute bottom-0 flex translate-y-1/2 items-center gap-2 px-2">
                    <svg class="interactable" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_208_98)">
                            <path
                                d="M12 17C14.7614 17 17 14.7614 17 12C17 9.23858 14.7614 7 12 7C9.23858 7 7 9.23858 7 12C7 14.7614 9.23858 17 12 17Z"
                                stroke="#FDFFFC"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path d="M12 1V3" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 21V23" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.22 4.21997L5.64 5.63997" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M18.36 18.36L19.78 19.78" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M1 12H3" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M21 12H23" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.22 19.78L5.64 18.36" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M18.36 5.63997L19.78 4.21997" stroke="#FDFFFC" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_208_98">
                                <rect width="24" height="24" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>

                    <a href="#">ITA/ENG</a>
                </div>
            </div>
            <div class="h-1/2"></div>
        </div>

        <div class="border-dark dark:border-light flex flex-col border-r">
            <div class="border-dark dark:border-light relative flex h-1/2 items-center justify-center border-b">
                <Link :href="route('about')">{{ $t('About') }}</Link>
            </div>
            <div class="flex h-1/2 items-center justify-center">
                <Link :href="route('projects')">{{ $t('Projects') }}</Link>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="border-dark dark:border-light relative flex h-1/2 items-center justify-center border-b">
                <Link :href="route('blog')">{{ $t('Blog') }}</Link>
            </div>
            <div class="bg-dark dark:bg-light text-light dark:text-dark flex h-1/2 items-center justify-center font-extrabold italic">
                <span class="interactable">{{ $t('Hire') }}</span>
            </div>
        </div>
    </header>
</template>
