<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const textarea = ref<HTMLTextAreaElement | null>(null);

onMounted(() => {
    if (textarea.value?.hasAttribute('autofocus')) {
        textarea.value?.focus();
    }
});

defineExpose({ focus: () => textarea.value?.focus() });
</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        v-model="model"
        ref="textarea"
    ></textarea>
</template>
