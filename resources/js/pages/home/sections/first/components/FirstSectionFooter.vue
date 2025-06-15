<script setup lang="ts">
import FooterContacts from './FooterContacts.vue';
</script>

<template>
    <footer class="flex items-end justify-between px-5 py-3">
        <p class="font-thin">© 2024-{{ new Date().getFullYear() }} Simone Cerruti</p>

        <FooterContacts />

        <p class="font-thin">{{ $t('VAT') }} IT02790210021</p>
    </footer>
</template>
