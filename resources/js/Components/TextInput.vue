<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-white/15 focus:border-purple-500 focus:ring-purple-500  bg-white/5 rounded-md shadow-sm text-black dark:text-white/80"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
