<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info', 'warning'].includes(value),
    },
    duration: {
        type: Number,
        default: 4000,
    },
});

const emit = defineEmits(['dismiss']);

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(() => {
            emit('dismiss');
        }, props.duration);
    }
});
</script>

<template>
    <div
        class="flex items-center gap-3 rounded-[6px] border px-4 py-3 shadow-sm text-sm transition-all font-sans bg-white"
        :class="{
            'border-[#A2E8C6] text-[#008046]': type === 'success',
            'border-[#FECACA] text-[#991B1B]': type === 'error',
            'border-[#C2D2E9] text-[#132952]': type === 'info',
            'border-[#FCDDA3] text-[#C87D0E]': type === 'warning',
        }"
        role="alert"
    >
        <span class="shrink-0">
            <!-- Success icon -->
            <svg v-if="type === 'success'" class="h-4 w-4 text-[#00B262]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <!-- Error icon -->
            <svg v-else-if="type === 'error'" class="h-4 w-4 text-[#EF4444]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <!-- Warning icon -->
            <svg v-else-if="type === 'warning'" class="h-4 w-4 text-[#FAA21B]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <!-- Info icon -->
            <svg v-else class="h-4 w-4 text-[#132952]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </span>

        <p class="font-medium text-xs sm:text-sm">
            {{ message }}
        </p>

        <button
            type="button"
            class="ml-auto text-current opacity-60 hover:opacity-100 p-0.5 rounded cursor-pointer"
            aria-label="Tutup notifikasi"
            @click="emit('dismiss')"
        >
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
