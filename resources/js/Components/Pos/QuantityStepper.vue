<script setup>
const props = defineProps({
    modelValue: {
        type: Number,
        default: 1,
    },
    min: {
        type: Number,
        default: 1,
    },
    max: {
        type: Number,
        default: 100,
    },
});

const emit = defineEmits(['update:modelValue']);

const triggerHaptic = () => {
    if (typeof navigator !== 'undefined' && navigator.vibrate) {
        try {
            navigator.vibrate(20);
        } catch (e) {
            // Ignore if vibration not permitted
        }
    }
};

const updateQty = (newVal) => {
    const clamped = Math.max(props.min, Math.min(props.max, newVal));
    triggerHaptic();
    emit('update:modelValue', clamped);
};

const increment = (delta = 1) => {
    updateQty(props.modelValue + delta);
};

const decrement = () => {
    updateQty(props.modelValue - 1);
};

const reset = () => {
    updateQty(1);
};
</script>

<template>
    <div class="space-y-2">
        <label class="block text-xs font-mono uppercase tracking-wider font-semibold text-[#132952]">
            Kuantitas Porsi / Item
        </label>

        <!-- Main Stepper Row -->
        <div class="flex items-center gap-3">
            <!-- Minus Button (>= 48px) -->
            <button
                type="button"
                :disabled="modelValue <= min"
                class="h-12 w-12 sm:h-14 sm:w-14 rounded-[8px] bg-[#F1F5F9] text-[#132952] border border-[#CBD5E1] hover:bg-[#E2E8F0] active:scale-90 active:bg-[#00B262] active:text-white transition-transform duration-75 flex items-center justify-center font-mono text-2xl font-bold cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed select-none shadow-2xs"
                aria-label="Kurangi kuantitas"
                @click="decrement"
            >
                -
            </button>

            <!-- Quantity Display Box -->
            <div class="flex-1 h-12 sm:h-14 rounded-[8px] bg-[#F8FAFC] border border-[#CBD5E1] flex items-center justify-center px-4">
                <span class="font-mono text-2xl sm:text-3xl font-bold text-[#132952] tracking-wider">
                    {{ modelValue }}
                </span>
                <span class="ml-2 text-xs font-mono text-[#64748B] uppercase font-medium">
                    Porsi
                </span>
            </div>

            <!-- Plus Button (>= 48px) -->
            <button
                type="button"
                :disabled="modelValue >= max"
                class="h-12 w-12 sm:h-14 sm:w-14 rounded-[8px] bg-[#F1F5F9] text-[#132952] border border-[#CBD5E1] hover:bg-[#E2E8F0] active:scale-90 active:bg-[#00B262] active:text-white transition-transform duration-75 flex items-center justify-center font-mono text-2xl font-bold cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed select-none shadow-2xs"
                aria-label="Tambah kuantitas"
                @click="increment(1)"
            >
                +
            </button>
        </div>

        <!-- Quick Increments & Reset Chips -->
        <div class="flex items-center gap-2 pt-1">
            <span class="text-[11px] font-mono text-[#64748B]">Pintas:</span>
            <button
                type="button"
                class="px-2.5 py-1 rounded-[4px] bg-[#F1F5F9] hover:bg-[#E2E8F0] active:scale-95 text-[#132952] text-xs font-mono border border-[#CBD5E1] cursor-pointer"
                @click="increment(1)"
            >
                +1
            </button>
            <button
                type="button"
                class="px-2.5 py-1 rounded-[4px] bg-[#F1F5F9] hover:bg-[#E2E8F0] active:scale-95 text-[#132952] text-xs font-mono border border-[#CBD5E1] cursor-pointer"
                @click="increment(2)"
            >
                +2
            </button>
            <button
                type="button"
                class="px-2.5 py-1 rounded-[4px] bg-[#F1F5F9] hover:bg-[#E2E8F0] active:scale-95 text-[#132952] text-xs font-mono border border-[#CBD5E1] cursor-pointer"
                @click="increment(5)"
            >
                +5
            </button>
            <button
                type="button"
                v-if="modelValue > 1"
                class="ml-auto px-2 py-1 rounded-[4px] text-[#B45309] hover:text-[#92400E] text-[11px] font-mono font-medium underline cursor-pointer"
                @click="reset"
            >
                Reset ke 1
            </button>
        </div>
    </div>
</template>
