<script setup lang="ts">
import FormattedTranslation from '@/components/FormattedTranslation.vue';
import { Toaster } from '@/components/ui/sonner';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import 'vue-sonner/style.css';
import BgDotsAndCursor from './components/BgDotsAndCursor.vue';
import ContactDialog from './components/ContactDialog.vue';
import HomeFooter from './components/HomeFooter.vue';
import HomeHeader from './components/HomeHeader.vue';

const isContactDialogOpen = ref(false);
</script>

<template>
    <Head :title="$t('Welcome')" />
    <Toaster />
    <div class="relative flex min-h-screen flex-col justify-between overflow-hidden" id="homepage">
        <BgDotsAndCursor />

        <HomeHeader @hire-clicked="isContactDialogOpen = true" />

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
                    <span class="interactable" @click="isContactDialogOpen = true">{{ $t("Don't wait, let's talk") }} 🡒</span>
                </h2>
            </div>
        </main>

        <HomeFooter />
    </div>

    <ContactDialog v-model="isContactDialogOpen" />
</template>

<style>
* {
    cursor: none;
}

a:hover,
button:hover,
.interactable:hover {
    cursor: none;
}

#cursor.interacting {
    transform: scale(4);
}

#cursorTrail.interacting {
    transform: scale(0);
}
</style>
