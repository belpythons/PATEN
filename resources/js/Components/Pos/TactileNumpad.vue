<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    maxLength: {
        type: Number,
        default: 6,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    autoSubmit: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:modelValue', 'complete', 'submit']);

const triggerHaptic = () => {
    if (typeof navigator !== 'undefined' && navigator.vibrate) {
        try {
            navigator.vibrate(25);
        } catch (e) {
            // Ignore if vibration not permitted
        }
    }
};

const handleDigit = (digit) => {
    if (props.disabled) return;
    if (props.modelValue.length < props.maxLength) {
        triggerHaptic();
        const next = props.modelValue + digit.toString();
        emit('update:modelValue', next);
        if (next.length === props.maxLength) {
            emit('complete', next);
            if (props.autoSubmit) {
                emit('submit', next);
            }
        }
    }
};

const handleBackspace = () => {
    if (props.disabled) return;
    if (props.modelValue.length > 0) {
        triggerHaptic();
        emit('update:modelValue', props.modelValue.slice(0, -1));
    }
};

const handleClear = () => {
    if (props.disabled) return;
    triggerHaptic();
    emit('update:modelValue', '');
};
</script>

<template>
    <div class="w-full max-w-sm mx-auto flex flex-col items-center select-none">
        <!-- 6 Visual PIN Dots Indicator -->
        <div class="mb-6 flex items-center justify-center gap-3.5">
            <div
                v-for="index in maxLength"
                :key="index"
                class="w-4 h-4 rounded-full border-2 transition-all duration-200"
                :class="[
                    index <= modelValue.length
                        ? 'bg-[#132952] border-[#132952] scale-110 shadow-xs'
                        : 'bg-white border-[#CBD5E1]',
                    error ? 'border-[#EF4444] bg-[#EF4444]/20' : '',
                ]"
            ></div>
        </div>

        <!-- Error Message if present -->
        <div v-if="error" class="mb-4 text-xs font-mono text-[#EF4444] text-center font-medium bg-[#FEF2F2] px-3 py-1.5 rounded-[4px] border border-[#FCA5A5] w-full">
            {{ error }}
        </div>

        <!-- 3x4 Touch Keypad Grid -->
        <div class="grid grid-cols-3 gap-3 w-full">
            <!-- Row 1: 1, 2, 3 -->
            <button
                v-for="digit in [1, 2, 3]"
                :key="digit"
                type="button"
                :disabled="disabled"
                class="h-14 sm:h-16 rounded-[8px] bg-white border border-[#CBD5E1] text-[#132952] font-mono text-2xl font-bold hover:bg-[#F8FAFC] active:scale-95 active:bg-[#E2E8F0] active:border-[#132952] transition-transform duration-75 shadow-xs flex items-center justify-center cursor-pointer disabled:opacity-50"
                @click="handleDigit(digit)"
            >
                {{ digit }}
            </button>

            <!-- Row 2: 4, 5, 6 -->
            <button
                v-for="digit in [4, 5, 6]"
                :key="digit"
                type="button"
                :disabled="disabled"
                class="h-14 sm:h-16 rounded-[8px] bg-white border border-[#CBD5E1] text-[#132952] font-mono text-2xl font-bold hover:bg-[#F8FAFC] active:scale-95 active:bg-[#E2E8F0] active:border-[#132952] transition-transform duration-75 shadow-xs flex items-center justify-center cursor-pointer disabled:opacity-50"
                @click="handleDigit(digit)"
            >
                {{ digit }}
            </button>

            <!-- Row 3: 7, 8, 9 -->
            <button
                v-for="digit in [7, 8, 9]"
                :key="digit"
                type="button"
                :disabled="disabled"
                class="h-14 sm:h-16 rounded-[8px] bg-white border border-[#CBD5E1] text-[#132952] font-mono text-2xl font-bold hover:bg-[#F8FAFC] active:scale-95 active:bg-[#E2E8F0] active:border-[#132952] transition-transform duration-75 shadow-xs flex items-center justify-center cursor-pointer disabled:opacity-50"
                @click="handleDigit(digit)"
            >
                {{ digit }}
            </button>

            <!-- Row 4: Clear, 0, Backspace -->
            <button
                type="button"
                :disabled="disabled || modelValue.length === 0"
                class="h-14 sm:h-16 rounded-[8px] bg-[#F1F5F9] border border-[#CBD5E1] text-[#64748B] hover:text-[#0F172A] font-mono text-xs font-semibold hover:bg-[#E2E8F0] active:scale-95 transition-transform duration-75 flex items-center justify-center cursor-pointer disabled:opacity-30"
                title="Hapus Semua"
                @click="handleClear"
            >
                BERSIHKAN
            </button>

            <button
                type="button"
                :disabled="disabled"
                class="h-14 sm:h-16 rounded-[8px] bg-white border border-[#CBD5E1] text-[#132952] font-mono text-2xl font-bold hover:bg-[#F8FAFC] active:scale-95 active:bg-[#E2E8F0] active:border-[#132952] transition-transform duration-75 shadow-xs flex items-center justify-center cursor-pointer disabled:opacity-50"
                @click="handleDigit(0)"
            >
                0
            </button>

            <button
                type="button"
                :disabled="disabled || modelValue.length === 0"
                class="h-14 sm:h-16 rounded-[8px] bg-[#F1F5F9] border border-[#CBD5E1] text-[#132952] hover:text-[#EF4444] font-mono text-sm font-semibold hover:bg-[#E2E8F0] active:scale-95 transition-transform duration-75 flex items-center justify-center cursor-pointer disabled:opacity-30"
                title="Hapus Satu Digit"
                @click="handleBackspace"
            >
                <!-- Backspace icon -->
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                </svg>
            </button>
        </div>
    </div>
</template>
