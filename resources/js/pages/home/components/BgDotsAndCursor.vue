<script setup lang="ts">
import { useMouse, useRafFn, useWindowSize } from '@vueuse/core';
import { computed, ref, useTemplateRef } from 'vue';

const { width, height } = useWindowSize();

const dotsAmount = computed(() => Math.round(((width.value / 40) * height.value) / 40));

const cursorTrail = useTemplateRef('cursorTrail');
const cursor = useTemplateRef('cursor');

const { x, y } = useMouse();

const trailX = ref(0);
const trailY = ref(0);

useRafFn(() => {
    if (!cursorTrail.value || !cursor.value) return;

    const isInteracting =
        document.querySelectorAll('a:hover, button:hover, .interactable:hover, input:hover, select:hover, label:hover, textarea:hover').length > 0;

    cursor.value.classList.toggle('interacting', isInteracting);
    cursorTrail.value.classList.toggle('interacting', isInteracting);

    cursor.value.style.top = `${y.value}px`;
    cursor.value.style.left = `${x.value}px`;

    trailX.value += (x.value - trailX.value) * 0.1;
    trailY.value += (y.value - trailY.value) * 0.1;

    cursorTrail.value.style.top = `${trailY.value}px`;
    cursorTrail.value.style.left = `${trailX.value}px`;

    const dots = [...document.querySelectorAll('.bg-dot')] as HTMLElement[];

    dots.forEach((dot) => {
        const mouseYFixed = y.value - document.documentElement.scrollTop;
        const { x: dotX, y: dotY } = dot.getBoundingClientRect();
        const distance = Math.sqrt(Math.pow(x.value - dotX, 2) + Math.pow(mouseYFixed - dotY, 2));
        const scale = (2 * (200 - distance)) / 100;
        dot.style.scale = scale < 0 ? '0' : `${scale}`;
    });
});
</script>

<template>
    <div class="pointer-events-none fixed top-0 flex h-screen w-screen flex-wrap gap-[40px] overflow-hidden">
        <div v-for="i in Array(dotsAmount)" :key="i" class="bg-dark dark:bg-light bg-dot h-1 w-1 rounded-full opacity-5"></div>
    </div>

    <div
        class="bg-light pointer-events-none absolute z-[999] aspect-square w-12 -translate-1/2 rounded-full mix-blend-difference transition-transform"
        ref="cursorTrail"
        id="cursorTrail"
    ></div>
    <div
        class="bg-light pointer-events-none absolute z-[999] aspect-square w-3 -translate-1/2 rounded-full mix-blend-difference transition-transform"
        ref="cursor"
        id="cursor"
    ></div>
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
