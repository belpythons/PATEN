<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'paper',
        validator: (value) => ['paper', 'graphite', 'muted'].includes(value),
    },
    padding: {
        type: String,
        default: 'normal',
        validator: (value) => ['none', 'sm', 'normal', 'lg'].includes(value),
    },
});

const paddingClasses = computed(() => {
    switch (props.padding) {
        case 'none': return 'p-0';
        case 'sm': return 'p-3 sm:p-4';
        case 'lg': return 'p-6 sm:p-8';
        default: return 'p-4 sm:p-6';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'graphite':
            return 'bg-[#0B0F19] text-[#F8FAFC] border-[#26334D] shadow-xs';
        case 'muted':
            return 'bg-[#F1F5F9] text-[#0F172A] border-[#E2E8F0]';
        default:
            return 'bg-white text-[#0F172A] border-[#E2E8F0] shadow-xs';
    }
});
</script>

<template>
    <div :class="['rounded-[10px] border transition-colors', variantClasses, paddingClasses]">
        <div v-if="$slots.header" class="mb-4 pb-3 border-b" :class="variant === 'graphite' ? 'border-[#26334D]' : 'border-[#E2E8F0]'">
            <slot name="header" />
        </div>
        <slot />
        <div v-if="$slots.footer" class="mt-4 pt-3 border-t" :class="variant === 'graphite' ? 'border-[#26334D]' : 'border-[#E2E8F0]'">
            <slot name="footer" />
        </div>
    </div>
</template>
