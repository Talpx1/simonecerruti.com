<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import Icon from '@/components/Icon.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useLocale } from '@/composables/useLocale';
import { Link } from '@inertiajs/vue3';

defineEmits<{ hireClicked: [] }>();

const { appearance, updateAppearance } = useAppearance();
const { currentLocale, updateLocale } = useLocale();
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
                <div
                    class="bg-light dark:bg-dark border-dark dark:border-light absolute bottom-0 flex translate-y-1/2 items-center gap-8 border px-1"
                >
                    <div class="flex gap-2">
                        <Icon
                            name="sun"
                            class="interactable"
                            :class="{ 'dark:bg-light bg-dark text-light dark:text-dark': appearance === 'light' }"
                            :size="24"
                            @click="updateAppearance('light')"
                        />
                        <Icon
                            name="moon"
                            class="interactable"
                            :class="{ 'dark:bg-light bg-dark text-light dark:text-dark': appearance === 'dark' }"
                            :size="24"
                            @click="updateAppearance('dark')"
                        />
                        <Icon
                            name="monitor"
                            class="interactable"
                            :class="{ 'dark:bg-light bg-dark text-light dark:text-dark': appearance === 'system' }"
                            :size="24"
                            @click="updateAppearance('system')"
                        />
                    </div>
                    <div class="flex gap-2">
                        <span
                            class="interactable"
                            :class="{ 'dark:bg-light bg-dark text-light dark:text-dark': currentLocale === 'it' }"
                            @click="updateLocale('it')"
                            >ITA</span
                        >
                        <span
                            class="interactable"
                            :class="{ 'dark:bg-light bg-dark text-light dark:text-dark': currentLocale === 'en' }"
                            @click="updateLocale('en')"
                            >ENG</span
                        >
                    </div>
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
                <span @click="$emit('hireClicked')" class="interactable">{{ $t('Hire') }}</span>
            </div>
        </div>
    </header>
</template>
