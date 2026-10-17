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
        validator: (value) => ['default', 'cobalt', 'graphite'].includes(value),
    },
});
</script>

<template>
    <div
        class="rounded-[10px] border p-4 sm:p-5 transition-colors"
        :class="{
            'bg-white border-[#E2E8F0] shadow-xs': variant === 'default',
            'bg-[#EFF6FF] border-[#BFDBFE]': variant === 'cobalt',
            'bg-[#0B0F19] text-[#F8FAFC] border-[#26334D]': variant === 'graphite',
        }"
    >
        <p
            class="text-xs font-mono uppercase tracking-[0.06em] font-medium"
            :class="variant === 'graphite' ? 'text-[#94A3B8]' : 'text-[#64748B]'"
        >
            {{ title }}
        </p>
        <p
            class="mt-2 text-2xl sm:text-3xl font-display font-bold tracking-tight tnum"
            :class="variant === 'graphite' ? 'text-white' : 'text-[#0F172A]'"
        >
            {{ value }}
        </p>
        <div v-if="subtitle || trend || $slots.extra" class="mt-2 flex items-center justify-between text-xs">
            <span
                v-if="subtitle"
                :class="variant === 'graphite' ? 'text-[#94A3B8]' : 'text-[#64748B]'"
            >
                {{ subtitle }}
            </span>
            <span v-if="trend" class="font-mono text-[#10B981] font-medium">
                {{ trend }}
            </span>
            <slot name="extra" />
        </div>
    </div>
</template>
