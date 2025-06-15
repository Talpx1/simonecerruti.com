<script setup lang="ts">
import FormattedTranslation from '@/components/FormattedTranslation.vue';
import FirstSectionFooter from './components/FirstSectionFooter.vue';
import HomeHeader from './components/HomeHeader.vue';

defineEmits<{ ctaClick: [] }>();
</script>

<template>
    <section class="flex h-screen max-h-screen min-h-screen flex-col justify-between overflow-hidden">
        <HomeHeader @hire-clicked="$emit('ctaClick')" />

        <div>
            <h1 class="px-5 text-6xl font-semibold tracking-tighter uppercase">
                <FormattedTranslation
                    translation-key="I create the <0>software</0> you're looking for..."
                    :tags="[`<span class='bg-dark dark:bg-light text-light dark:text-dark'>`]"
                />
                <br />
                <FormattedTranslation
                    translation-key="And what you didn't even know you <0>wished for</0>."
                    :tags="[`<span class='underline decoration-5 underline-offset-5'>`]"
                />
            </h1>
            <div class="to-dark dark:to-light mt-1 flex w-full justify-end bg-linear-to-r from-transparent to-60% py-4">
                <h2 class="text-light dark:text-dark px-4 text-5xl font-black uppercase">
                    <span class="interactable" @click="$emit('ctaClick')">{{ $t("Don't wait, let's talk") }} 🡒</span>
                </h2>
            </div>
        </div>

        <FirstSectionFooter />
    </section>
</template>
