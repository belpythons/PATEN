<script setup>
import { watch, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl'].includes(value),
    },
    closeOnBackdrop: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    switch (props.maxWidth) {
        case 'sm': return 'max-w-sm';
        case 'md': return 'max-w-md';
        case 'lg': return 'max-w-lg';
        case 'xl': return 'max-w-xl';
        case '2xl': return 'max-w-2xl';
        default: return 'max-w-md';
    }
});
</script>

<template>
    <div v-if="show" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop with subtle fade -->
        <div
            class="fixed inset-0 bg-[#0F172A]/40 backdrop-blur-xs transition-opacity"
            @click="closeOnBackdrop ? close() : null"
        ></div>

        <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
            <div class="flex min-h-full items-end justify-center sm:items-center text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-[10px] bg-white text-left shadow-lg transition-all sm:my-8 w-full border border-[#CBD5E1]"
                    :class="maxWidthClass"
                    @click.stop
                >
                    <!-- Header -->
                    <div v-if="title || $slots.header" class="flex items-center justify-between border-b border-[#E2E8F0] px-5 py-4">
                        <slot name="header">
                            <h3 id="modal-title" class="text-base font-semibold font-display text-[#0F172A]">
                                {{ title }}
                            </h3>
                        </slot>
                        <button
                            type="button"
                            class="rounded-[6px] p-1 text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#0F172A] transition-colors cursor-pointer"
                            aria-label="Tutup modal"
                            @click="close"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-5 py-4">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div v-if="$slots.footer" class="border-t border-[#E2E8F0] bg-[#F8FAFC] px-5 py-3 flex items-center justify-end gap-2">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
