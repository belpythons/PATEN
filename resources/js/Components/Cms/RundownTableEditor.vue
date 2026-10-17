<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import BaseButton from '@/Components/UI/BaseButton.vue';
import BaseModal from '@/Components/UI/BaseModal.vue';
import BaseBadge from '@/Components/UI/BaseBadge.vue';

const props = defineProps({
    event: {
        type: Object,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    rundowns: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);
const editingRundownId = ref(null);
const feedbackMessage = ref('');
const feedbackType = ref('success');

// Form for Adding / Editing Rundown
const form = useForm({
    judul_sesi: '',
    waktu_mulai: '',
    waktu_selesai: '',
    pengisi_acara: '',
    urutan: props.rundowns ? props.rundowns.length + 1 : 1,
});

const openAddModal = () => {
    editingRundownId.value = null;
    form.reset();
    form.urutan = props.rundowns ? props.rundowns.length + 1 : 1;
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    editingRundownId.value = item.id;
    form.judul_sesi = item.judul_sesi;
    form.waktu_mulai = item.waktu_mulai ? item.waktu_mulai.substring(0, 16) : '';
    form.waktu_selesai = item.waktu_selesai ? item.waktu_selesai.substring(0, 16) : '';
    form.pengisi_acara = item.pengisi_acara || '';
    form.urutan = item.urutan || 1;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    editingRundownId.value = null;
};

const submitForm = () => {
    if (editingRundownId.value) {
        // Update existing
        form.put(`/cms/${props.event.slug}/rundowns/${editingRundownId.value}?token=${props.token}`, {
            preserveScroll: true,
            onSuccess: () => {
                feedbackMessage.value = 'Sesi rundown berhasil diperbarui!';
                feedbackType.value = 'success';
                closeModal();
            },
            onError: (errs) => {
                feedbackMessage.value = 'Gagal menyimpan: ' + Object.values(errs).join(', ');
                feedbackType.value = 'error';
            },
        });
    } else {
        // Create new
        form.post(`/cms/${props.event.slug}/rundowns?token=${props.token}`, {
            preserveScroll: true,
            onSuccess: () => {
                feedbackMessage.value = 'Sesi rundown berhasil ditambahkan!';
                feedbackType.value = 'success';
                closeModal();
            },
            onError: (errs) => {
                feedbackMessage.value = 'Gagal menambahkan: ' + Object.values(errs).join(', ');
                feedbackType.value = 'error';
            },
        });
    }
};

const deleteRundown = (id, judul) => {
    if (!confirm(`Hapus sesi "${judul}" dari rundown acara?`)) return;

    router.delete(`/cms/${props.event.slug}/rundowns/${id}?token=${props.token}`, {
        preserveScroll: true,
        onSuccess: () => {
            feedbackMessage.value = 'Sesi rundown berhasil dihapus.';
            feedbackType.value = 'success';
        },
        onError: () => {
            feedbackMessage.value = 'Gagal menghapus sesi rundown.';
            feedbackType.value = 'error';
        },
    });
};

const formatTimeRange = (start, end) => {
    if (!start) return '-';
    try {
        const dStart = new Date(start);
        const startTime = dStart.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        if (!end) return `${startTime} WITA`;
        const dEnd = new Date(end);
        const endTime = dEnd.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        return `${startTime} - ${endTime} WITA`;
    } catch {
        return `${start} - ${end || ''}`;
    }
};
</script>

<template>
    <div class="bg-white border border-[#CBD5E1] rounded-[10px] p-6 shadow-xs mb-8">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#CBD5E1] mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#132952]"></span>
                    <span class="font-mono text-xs uppercase tracking-wider text-[#132952] font-semibold">
                        MODUL 2.2 / MANAJEMEN RUNDOWN ACARA
                    </span>
                </div>
                <h3 class="text-lg font-display font-bold text-[#132952] tracking-tight">
                    Jadwal & Agenda Lapangan
                </h3>
                <p class="text-xs text-[#64748B] mt-0.5">
                    Susun urutan panggung seremonial, bazar, hiburan, dan pengundian doorprize secara tertib.
                </p>
            </div>

            <BaseButton
                variant="primary"
                size="sm"
                class="cursor-pointer shrink-0"
                @click="openAddModal"
            >
                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Sesi Rundown
            </BaseButton>
        </div>

        <!-- Alert Notification -->
        <div
            v-if="feedbackMessage"
            class="mb-4 p-3 rounded-[6px] text-xs font-mono flex items-center justify-between border"
            :class="feedbackType === 'success' ? 'bg-[#E6F7EF] text-[#00B262] border-[#00B262]' : 'bg-[#FEE2E2] text-[#DC2626] border-[#F87171]'"
        >
            <span>{{ feedbackMessage }}</span>
            <button type="button" class="text-xs cursor-pointer font-bold ml-2" @click="feedbackMessage = ''">&times;</button>
        </div>

        <!-- Rundown Table -->
        <div v-if="rundowns && rundowns.length > 0" class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-[#CBD5E1] bg-[#F8FAFC] text-[#64748B] font-mono text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-3 w-16">Urutan</th>
                        <th class="py-2.5 px-3 w-40">Jam Sesi</th>
                        <th class="py-2.5 px-3">Judul Sesi Acara</th>
                        <th class="py-2.5 px-3">Pengisi Acara / PIC</th>
                        <th class="py-2.5 px-3 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    <tr
                        v-for="r in rundowns"
                        :key="r.id"
                        class="hover:bg-[#F8FAFC] transition-colors"
                    >
                        <td class="py-3 px-3 font-mono font-bold text-[#132952]">
                            #{{ String(r.urutan || 1).padStart(2, '0') }}
                        </td>
                        <td class="py-3 px-3 font-mono text-[#132952] whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded-[4px] bg-[#EDF2F9] border border-[#CBD5E1] text-[#132952]">
                                {{ formatTimeRange(r.waktu_mulai, r.waktu_selesai) }}
                            </span>
                        </td>
                        <td class="py-3 px-3 font-medium text-[#132952]">
                            {{ r.judul_sesi }}
                        </td>
                        <td class="py-3 px-3 text-[#475569]">
                            {{ r.pengisi_acara || '-' }}
                        </td>
                        <td class="py-3 px-3 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <button
                                    type="button"
                                    class="p-1 text-[#64748B] hover:text-[#132952] transition-colors cursor-pointer"
                                    title="Edit Sesi"
                                    @click="openEditModal(r)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="p-1 text-[#64748B] hover:text-[#DC2626] transition-colors cursor-pointer"
                                    title="Hapus Sesi"
                                    @click="deleteRundown(r.id, r.judul_sesi)"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="p-8 border border-dashed border-[#CBD5E1] rounded-[8px] text-center bg-[#F8FAFC]"
        >
            <svg class="h-8 w-8 mx-auto text-[#94A3B8] mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="font-mono text-xs text-[#64748B] font-semibold uppercase">Belum Ada Sesi Rundown</p>
            <p class="text-xs text-[#94A3B8] mt-1">Klik tombol di atas untuk menyusun jadwal acara pertama.</p>
        </div>

        <!-- Add / Edit Modal -->
        <BaseModal
            :show="isModalOpen"
            :title="editingRundownId ? 'Edit Sesi Rundown' : 'Tambah Sesi Rundown Baru'"
            max-width="md"
            @close="closeModal"
        >
            <form class="space-y-4" @submit.prevent="submitForm">
                <div>
                    <label class="block text-xs font-mono text-[#475569] mb-1">Judul Sesi Kegiatan *</label>
                    <input
                        v-model="form.judul_sesi"
                        type="text"
                        placeholder="Contoh: Pembukaan & Sambutan Resmi Bupati Kutim"
                        required
                        class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-2 text-xs text-[#0F172A] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-mono text-[#475569] mb-1">Waktu Mulai *</label>
                        <input
                            v-model="form.waktu_mulai"
                            type="datetime-local"
                            required
                            class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-2 text-xs font-mono text-[#0F172A] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-mono text-[#475569] mb-1">Waktu Selesai *</label>
                        <input
                            v-model="form.waktu_selesai"
                            type="datetime-local"
                            required
                            class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-2 text-xs font-mono text-[#0F172A] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-mono text-[#475569] mb-1">Pengisi Acara / PIC</label>
                        <input
                            v-model="form.pengisi_acara"
                            type="text"
                            placeholder="Contoh: Dinas Koperasi & UKM / MC"
                            class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-3 py-2 text-xs text-[#0F172A] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-mono text-[#475569] mb-1">Nomor Urutan</label>
                        <input
                            v-model.number="form.urutan"
                            type="number"
                            min="1"
                            class="w-full rounded-[6px] border border-[#CBD5E1] bg-white px-2.5 py-2 text-xs font-mono text-[#0F172A] focus:border-[#132952] focus:ring-1 focus:ring-[#132952] focus:outline-none"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#CBD5E1]">
                    <BaseButton
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="closeModal"
                    >
                        Batal
                    </BaseButton>
                    <BaseButton
                        type="submit"
                        variant="primary"
                        size="sm"
                        :loading="form.processing"
                    >
                        {{ editingRundownId ? 'Perbarui Sesi' : 'Simpan Sesi' }}
                    </BaseButton>
                </div>
            </form>
        </BaseModal>
    </div>
</template>
