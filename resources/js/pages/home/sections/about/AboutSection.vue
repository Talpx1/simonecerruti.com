<script setup lang="ts">
import protrait from '@/assets/images/about_protrait.jpg';
import AppLogo from '@/components/AppLogo.vue';
import FormattedTranslation from '@/components/FormattedTranslation.vue';
</script>

<template>
    <section class="flex h-screen max-h-screen min-h-screen overflow-hidden" id="about">
        <img :src="protrait" alt="Simone Cerruti dramatic black and white profile protrait" class="w-2/5 object-cover" />
        <div class="flex w-3/5 flex-col justify-between py-32">
            <div class="flex flex-col gap-12">
                <h2 class="px-8 text-8xl font-black">ABOUT SIMONE</h2>
                <h3 class="px-8 text-4xl font-semibold">
                    <FormattedTranslation
                        translation-key="I create software that adapts to your way of working, <0>not the other way around</0>."
                        :tags="[`<span class='underline decoration-4 underline-offset-3'>`]"
                    />
                </h3>
                <div
                    class="max-w-full overflow-x-clip bg-linear-[to_right,transparent,var(--dark)_35%,var(--dark)_65%,transparent] py-4 dark:bg-linear-[to_right,transparent,var(--light)_35%,var(--light)_65%,transparent]"
                >
                    <span class="text-light dark:text-dark flex w-full max-w-full flex-nowrap text-4xl uppercase">
                        <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
                            <h4>{{ $t('MANAGEMENT SOFTWARES') }}</h4>
                            /
                            <h4>ERP</h4>
                            /
                            <h4>CRM</h4>
                            /
                            <h4>{{ $t('WEBSITES') }}</h4>
                            /
                            <h4>E-COMMERCE</h4>
                            /
                            <h4>SAS</h4>
                            /
                            <h4>{{ $t('WEB PLATFORMS') }}</h4>
                            /
                            <h4>PWA</h4>
                            /
                            <h4>{{ $t('AND ANYTHING ELSE THAT COMES TO YOUR MIND') }}</h4>
                            <span class="mr-8">/</span>
                        </span>
                        <span class="animate-marquee flex flex-nowrap gap-8 text-nowrap whitespace-nowrap">
                            <span>{{ $t('MANAGEMENT SOFTWARES') }}</span>
                            /
                            <span>ERP</span>
                            /
                            <span>CRM</span>
                            /
                            <span>{{ $t('WEBSITES') }}</span>
                            /
                            <span>E-COMMERCE</span>
                            /
                            <span>SAS</span>
                            /
                            <span>{{ $t('WEB PLATFORMS') }}</span>
                            /
                            <span>PWA</span>
                            /
                            <span>{{ $t('AND ANYTHING ELSE THAT COMES TO YOUR MIND') }}</span>
                            <span class="mr-8">/</span>
                        </span>
                    </span>
                </div>
            </div>

            <div class="mx-auto flex flex-col gap-4 text-center">
                <div class="flex flex-col gap-1">
                    <div class="text-sm">{{ $t('PRODUCTS THAT ARE') }}</div>
                    <div class="flex justify-center gap-8 text-5xl font-black">
                        <span>{{ $t('RELIABLE') }}</span> / <span>{{ $t('INTUITIVE') }}</span> / <span>{{ $t('SCALABLE') }}</span>
                    </div>
                    <div class="text-sm font-extralight">{{ $t("just the way you're looking for them, just the way you imagine them.") }}</div>
                </div>
                <div class="text-2xl underline underline-offset-4">{{ $t('DISCOVER HOW') }} 🡒</div>
            </div>

            <div class="mx-auto flex items-center gap-8 text-4xl">
                <div>{{ $t('IF YOU CAN DREAM IT') }}</div>
                <div class="max-w-18">
                    <AppLogo weight="bold" />
                </div>
                <div>{{ $t('I CAN BUILD IT') }}</div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@keyframes marquee {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-100%);
    }
}

.animate-marquee {
    animation: marquee 25s linear infinite;
}
</style>
