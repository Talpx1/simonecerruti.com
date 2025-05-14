<script setup lang="ts">
import FormattedTranslation from '@/components/FormattedTranslation.vue';
import { Head } from '@inertiajs/vue3';
import HomeFooter from './components/HomeFooter.vue';
import HomeHeader from './components/HomeHeader.vue';
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-light dark:bg-dark text-dark dark:text-light flex min-h-screen flex-col justify-between">
        <HomeHeader />

        <main>
            <h1 class="px-5 text-6xl font-semibold tracking-tighter uppercase">
                <FormattedTranslation
                    translation-key="I create the <0>software</0> you're looking for..."
                    :tags="[`<span class='bg-dark dark:bg-light text-light dark:text-dark'>`]"
                />
                <br />
                <FormattedTranslation
                    translation-key="And what you didn't even know you <0>wanted</0>."
                    :tags="[`<span class='underline decoration-5 underline-offset-5'>`]"
                />
            </h1>
            <div class="to-dark dark:to-light mt-1 flex w-full justify-end bg-linear-to-r from-transparent to-60% py-4">
                <h2 class="text-light dark:text-dark px-4 text-5xl font-black uppercase">
                    <a href="#">{{ $t("Don't wait, let's talk") }} 🡒</a>
                </h2>
            </div>
        </main>

        <HomeFooter />
    </div>
</template>
