<script setup lang="ts">
defineProps<{
    translationKey: string;
    tags: string[];
}>();
</script>

<template>
    <span v-html="$t(translationKey, tags)"></span>
</template>
