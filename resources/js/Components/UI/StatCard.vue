<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    trend: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'default',
        validator: (value) => ['default', 'navy', 'cobalt', 'emerald', 'amber', 'graphite'].includes(value),
    },
});
</script>

<template>
    <div
        class="rounded-[10px] border p-4 sm:p-5 transition-colors"
        :class="{
            'bg-white border-[#E2E8F0] shadow-xs': variant === 'default',
            'bg-[#EDF2F9] border-[#C2D2E9] text-[#132952]': variant === 'navy' || variant === 'cobalt',
            'bg-[#E6F7EF] border-[#A2E8C6] text-[#008046]': variant === 'emerald',
            'bg-[#FEF6E8] border-[#FCDDA3] text-[#C87D0E]': variant === 'amber',
            'bg-[#0C1830] text-[#F8FAFC] border-[#1E3A6E]': variant === 'graphite',
        }"
    >
        <p
            class="text-xs font-mono uppercase tracking-[0.06em] font-medium"
            :class="variant === 'graphite' ? 'text-[#94A3B8]' : 'text-[#5B6B8A]'"
        >
            {{ title }}
        </p>
        <p
            class="mt-2 text-2xl sm:text-3xl font-display font-bold tracking-tight tnum"
            :class="variant === 'graphite' ? 'text-white' : 'text-[#132952]'"
        >
            {{ value }}
        </p>
        <div v-if="subtitle || trend || $slots.extra" class="mt-2 flex items-center justify-between text-xs">
            <span
                v-if="subtitle"
                :class="variant === 'graphite' ? 'text-[#94A3B8]' : 'text-[#5B6B8A]'"
            >
                {{ subtitle }}
            </span>
            <span v-if="trend" class="font-mono text-[#00B262] font-semibold">
                {{ trend }}
            </span>
            <slot name="extra" />
        </div>
    </div>
</template>
