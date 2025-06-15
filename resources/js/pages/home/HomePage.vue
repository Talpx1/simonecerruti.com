<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import 'vue-sonner/style.css';
import BgDotsAndCursor from './components/BgDotsAndCursor.vue';
import ContactDialog from './components/ContactDialog.vue';
import AboutSection from './sections/about/AboutSection.vue';
import FirstSection from './sections/first/FirstSection.vue';

const isContactDialogOpen = ref(false);
</script>

<template>
    <Head :title="$t('Welcome')" />

    <Toaster />
    <BgDotsAndCursor />
    <ContactDialog v-model="isContactDialogOpen" />

    <main class="overflow-x-hidden">
        <FirstSection @cta-click="isContactDialogOpen = true" />
        <AboutSection />
    </main>
</template>

<style>
body {
    position: relative;
    overflow-x: hidden;
}
</style>
