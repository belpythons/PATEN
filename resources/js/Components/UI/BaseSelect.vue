<script setup>
defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: '',
    },
    id: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="w-full">
        <label v-if="label" :for="id" class="block text-xs font-mono uppercase tracking-wider text-[#334155] mb-1.5 font-medium">
            {{ label }}
            <span v-if="required" class="text-[#EF4444]">*</span>
        </label>
        <div class="relative">
            <select
                :id="id"
                :value="modelValue"
                :required="required"
                :disabled="disabled"
                class="w-full rounded-[6px] border bg-white px-3 py-2 text-sm text-[#0F172A] transition-colors focus:outline-none disabled:bg-[#F1F5F9] disabled:cursor-not-allowed font-sans appearance-none pr-8 cursor-pointer"
                :class="error ? 'border-[#EF4444] focus:border-[#EF4444] focus:ring-1 focus:ring-[#EF4444]' : 'border-[#CBD5E1] hover:border-[#94A3B8] focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]'"
                @change="$emit('update:modelValue', $event.target.value)"
            >
                <option v-if="placeholder" value="" disabled :selected="!modelValue">
                    {{ placeholder }}
                </option>
                <option
                    v-for="(option, idx) in options"
                    :key="idx"
                    :value="typeof option === 'object' ? option.value : option"
                >
                    {{ typeof option === 'object' ? option.label : option }}
                </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-[#64748B]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
        <p v-if="error" class="mt-1 text-xs text-[#EF4444] font-mono">
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1 text-xs text-[#64748B]">
            {{ hint }}
        </p>
    </div>
</template>
