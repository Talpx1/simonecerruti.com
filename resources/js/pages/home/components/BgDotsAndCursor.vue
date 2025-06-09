<script setup lang="ts">
import { useMouse, useRafFn, useWindowSize } from '@vueuse/core';
import { computed, ref, useTemplateRef } from 'vue';

const { width, height } = useWindowSize();
const dotsAmount = computed(() => Math.round(((width.value / 30) * height.value) / 30));

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

    ([...document.querySelectorAll('.bg-dot')] as HTMLElement[]).forEach((dot) => {
        const { x: dotX, y: dotY } = dot.getBoundingClientRect();
        const distance = Math.sqrt(Math.pow(x.value - dotX, 2) + Math.pow(y.value - dotY, 2));
        const scale = (2 * (200 - distance)) / 100;
        dot.style.scale = scale < 0 ? '0' : `${scale}`;
    });
});
</script>

<template>
    <div class="pointer-events-none absolute flex h-full w-full flex-wrap gap-10 overflow-hidden">
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
