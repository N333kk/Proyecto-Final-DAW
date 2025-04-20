<script setup>
defineProps({
    value: String,
});
</script>

<template>
    <label class="block font-medium text-sm text-white dark:text-white/60">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
