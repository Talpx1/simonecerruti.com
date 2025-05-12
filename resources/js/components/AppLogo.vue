<script setup lang="ts">
const { weight = 'medium' } = defineProps<{
    weight?: 'thin' | 'medium' | 'bold';
    svgClasses?: string;
}>();
</script>

<template>
    <!-- THIN -->
    <!-- light -->
    <svg
        v-if="weight === 'thin'"
        class="hidden w-full dark:block"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 96 51"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M1 49.2494C22 49.2494 24.0781 42.1243 34.5 27.7493C49 7.7493 72 -8.7506 95 7.74931C72 7.74931 72 26.2492 72 27.7492C72 29.2492 72 49.2492 95 49.2492"
            stroke="#FDFFFC"
            stroke-width="2"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>
    <!-- dark -->
    <svg
        v-if="weight === 'thin'"
        class="block w-full dark:hidden"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 96 51"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M1 49.4986C22 49.4986 24.0781 42.3735 34.5 27.9985C49 7.99857 72 -8.50133 95 7.99857C72 7.99857 72 26.4985 72 27.9985C72 29.4985 72 49.4985 95 49.4985"
            stroke="#040F0F"
            stroke-width="2"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>

    <!-- MEDIUM -->
    <!-- light -->
    <svg
        v-if="weight === 'medium'"
        class="hidden w-full dark:block"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 100 54"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M3 51.2494C24 51.2494 26.0781 44.1243 36.5 29.7493C51 9.7493 74 -6.7506 97 9.74931C74 9.74931 74 28.2492 74 29.7492C74 31.2492 74 51.2492 97 51.2492"
            stroke="#FDFFFC"
            stroke-width="5"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>
    <!-- dark -->
    <svg
        v-if="weight === 'medium'"
        class="block w-full dark:hidden"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 100 54"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M3 51.4989C24 51.4989 26.0781 44.3738 36.5 29.9988C51 9.99881 74 -6.50109 97 9.99882C74 9.99882 74 28.4987 74 29.9987C74 31.4987 74 51.4987 97 51.4987"
            stroke="#040F0F"
            stroke-width="5"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>

    <!-- BOLD -->
    <!-- light -->
    <svg
        v-if="weight === 'bold'"
        class="hidden w-full dark:block"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 104 59"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M5 53.2494C26 53.2494 28.0781 46.1243 38.5 31.7493C53 11.7493 76 -4.7506 99 11.7493C76 11.7493 76 30.2492 76 31.7492C76 33.2492 76 53.2492 99 53.2492"
            stroke="#FDFFFC"
            stroke-width="10"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>
    <!-- dark -->
    <svg
        v-if="weight === 'bold'"
        class="block w-full dark:hidden"
        :class="svgClasses"
        preserveAspectRatio="xMidYMid meet"
        viewBox="0 0 104 59"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M5 53.4989C26 53.4989 28.0781 46.3738 38.5 31.9988C53 11.9988 76 -4.50109 99 11.9988C76 11.9988 76 30.4987 76 31.9987C76 33.4987 76 53.4987 99 53.4987"
            stroke="#040F0F"
            stroke-width="10"
            stroke-linecap="square"
            stroke-linejoin="bevel"
        />
    </svg>
</template>
