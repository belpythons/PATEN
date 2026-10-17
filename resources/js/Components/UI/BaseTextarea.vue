<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
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
    rows: {
        type: Number,
        default: 3,
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
        <textarea
            :id="id"
            :rows="rows"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="w-full rounded-[6px] border bg-white px-3 py-2 text-sm text-[#0F172A] placeholder-[#94A3B8] transition-colors focus:outline-none disabled:bg-[#F1F5F9] disabled:cursor-not-allowed font-sans resize-y"
            :class="error ? 'border-[#EF4444] focus:border-[#EF4444] focus:ring-1 focus:ring-[#EF4444]' : 'border-[#CBD5E1] hover:border-[#94A3B8] focus:border-[#132952] focus:ring-1 focus:ring-[#132952]'"
            @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>
        <p v-if="error" class="mt-1 text-xs text-[#EF4444] font-mono">
            {{ error }}
        </p>
        <p v-else-if="hint" class="mt-1 text-xs text-[#64748B]">
            {{ hint }}
        </p>
    </div>
</template>
