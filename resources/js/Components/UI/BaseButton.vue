<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'outline', 'danger', 'ghost', 'emerald', 'amber'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['click']);

const classes = computed(() => {
    const base = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#132952] disabled:opacity-50 disabled:cursor-not-allowed select-none rounded-[6px] cursor-pointer';

    const sizes = {
        sm: 'px-2.5 py-1 text-xs gap-1.5',
        md: 'px-4 py-2 text-sm gap-2',
        lg: 'px-5 py-2.5 text-base gap-2.5',
    };

    const variants = {
        primary: 'bg-[#132952] text-white hover:bg-[#0D1E3D] active:bg-[#0A162C] border border-transparent shadow-xs',
        emerald: 'bg-[#00B262] text-white hover:bg-[#009954] active:bg-[#008046] border border-transparent shadow-xs',
        amber: 'bg-[#FAA21B] text-white hover:bg-[#E59214] active:bg-[#C87D0E] border border-transparent shadow-xs',
        secondary: 'bg-white text-[#132952] border border-[#CBD5E1] hover:bg-[#F8FAFC] hover:border-[#94A3B8]',
        outline: 'bg-transparent text-[#132952] border border-[#E2E8F0] hover:border-[#132952] hover:text-[#132952]',
        danger: 'bg-white text-[#EF4444] border border-[#FECACA] hover:bg-[#FEF2F2] hover:border-[#EF4444]',
        ghost: 'bg-transparent text-[#273A60] hover:bg-[#F1F5F9] hover:text-[#132952]',
    };

    return `${base} ${sizes[props.size]} ${variants[props.variant]}`;
});
</script>

<template>
    <button
        :type="type"
        :class="classes"
        :disabled="disabled || loading"
        @click="emit('click', $event)"
    >
        <svg
            v-if="loading"
            class="animate-spin -ml-0.5 mr-2 h-4 w-4 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot />
    </button>
</template>
