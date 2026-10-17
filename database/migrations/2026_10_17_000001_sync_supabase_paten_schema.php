<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Ensure uuid-ossp extension is enabled
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        // 1. Table: events
        if (! Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->string('slug', 120)->unique();
                $table->string('nama_event', 255);
                $table->text('deskripsi');
                $table->string('penyelenggara', 150);
                $table->string('lokasi_venue', 255);
                $table->timestampTz('tanggal_mulai');
                $table->timestampTz('tanggal_selesai');
                $table->string('secure_management_token', 64)->unique();
                $table->string('template_theme', 50)->default('kutim_formal');
                $table->string('color_mode', 10)->default('duo');
                $table->string('color_primary', 7)->default('#0F766E');
                $table->string('color_secondary', 7)->default('#F59E0B');
                $table->text('poster_asset_url')->nullable();
                $table->string('gamifikasi_mode')->default('doorprize');
                $table->decimal('harga_htm', 12, 2)->default(0.00);
                $table->boolean('is_awarding_active')->default(true);
                $table->timestampsTz();
            });
        }

        // 2. Table: event_rundowns
        if (! Schema::hasTable('event_rundowns')) {
            Schema::create('event_rundowns', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('event_id');
                $table->timestampTz('waktu_mulai');
                $table->timestampTz('waktu_selesai');
                $table->string('judul_sesi', 200);
                $table->string('pengisi_acara', 150)->nullable();
                $table->text('deskripsi_sesi')->nullable();
                $table->integer('urutan')->default(1);
                $table->timestampTz('created_at')->useCurrent();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }

        // 3. Table: umkm_profiles
        if (! Schema::hasTable('umkm_profiles')) {
            Schema::create('umkm_profiles', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->string('nik', 16)->unique();
                $table->string('no_kk', 16);
                $table->string('nama_pemilik', 150);
                $table->string('nama_usaha', 150);
                $table->string('nomor_wa', 20);
                $table->string('kecamatan', 100);
                $table->text('alamat_usaha')->nullable();
                $table->string('nib', 50)->nullable();
                $table->boolean('has_halal')->default(false);
                $table->boolean('has_pirt')->default(false);
                $table->boolean('pernah_terima_hibah')->default(false);
                $table->integer('tahun_terima_hibah')->nullable();
                $table->timestampTz('created_at')->useCurrent();
            });
        }

        // 4. Table: event_stands
        if (! Schema::hasTable('event_stands')) {
            Schema::create('event_stands', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('event_id');
                $table->uuid('umkm_id')->nullable();
                $table->string('nomor_stand', 20);
                $table->string('kategori_jualan')->default('kuliner');
                $table->integer('daya_listrik_watt')->default(450);
                $table->jsonb('fasilitas_detail')->nullable();
                $table->string('pin_pos', 6);
                $table->decimal('total_omzet_terekam', 14, 2)->default(0.00);
                $table->integer('total_transaksi_terekam')->default(0);
                $table->timestampTz('created_at')->useCurrent();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('umkm_id')->references('id')->on('umkm_profiles')->onDelete('set null');
                $table->unique(['event_id', 'nomor_stand']);
            });
        }

        // 5. Table: stand_menus
        if (! Schema::hasTable('stand_menus')) {
            Schema::create('stand_menus', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('stand_id');
                $table->string('nama_item', 150);
                $table->decimal('harga', 12, 2);
                $table->boolean('is_available')->default(true);
                $table->timestampsTz();

                $table->foreign('stand_id')->references('id')->on('event_stands')->onDelete('cascade');
            });
        }

        // 6. Table: pos_transactions
        if (! Schema::hasTable('pos_transactions')) {
            Schema::create('pos_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('event_id');
                $table->uuid('stand_id');
                $table->uuid('menu_id');
                $table->integer('qty')->default(1);
                $table->decimal('subtotal', 12, 2);
                $table->string('metode_pembayaran')->default('cash');
                $table->timestampTz('created_at')->useCurrent();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('stand_id')->references('id')->on('event_stands')->onDelete('cascade');
                $table->foreign('menu_id')->references('id')->on('stand_menus')->onDelete('restrict');
            });
        }

        // 7. Table: attendees
        if (! Schema::hasTable('attendees')) {
            Schema::create('attendees', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('event_id');
                $table->string('nama_lengkap', 150);
                $table->string('nomor_wa', 20);
                $table->string('kecamatan', 100);
                $table->string('nomor_tiket_doorprize', 40)->unique();
                $table->string('tipe_registrasi')->default('doorprize');
                $table->boolean('is_checked_in')->default(false);
                $table->timestampTz('check_in_time')->nullable();
                $table->boolean('is_doorprize_winner')->default(false);
                $table->timestampTz('created_at')->useCurrent();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            });
        }

        // 8. Table: spk_umkm_evaluations
        if (! Schema::hasTable('spk_umkm_evaluations')) {
            Schema::create('spk_umkm_evaluations', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('uuid_generate_v4()'));
                $table->uuid('event_id');
                $table->uuid('umkm_id');
                $table->uuid('stand_id');
                $table->decimal('score_konsistensi_transaksi', 6, 4)->default(0.0000);
                $table->decimal('score_digitalisasi_qris', 6, 4)->default(0.0000);
                $table->decimal('score_kepatuhan_regulasi', 5, 2)->default(0.00);
                $table->decimal('skor_akhir_spk', 6, 2)->default(0.00);
                $table->string('cluster_kategori', 50)->nullable();
                $table->string('rekomendasi_program')->default('pending');
                $table->boolean('is_prioritas_hibah_200')->default(false);
                $table->text('catatan_dinas')->nullable();
                $table->timestampTz('calculated_at')->useCurrent();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('umkm_id')->references('id')->on('umkm_profiles')->onDelete('cascade');
                $table->foreign('stand_id')->references('id')->on('event_stands')->onDelete('cascade');
                $table->unique(['event_id', 'stand_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_umkm_evaluations');
        Schema::dropIfExists('attendees');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('stand_menus');
        Schema::dropIfExists('event_stands');
        Schema::dropIfExists('umkm_profiles');
        Schema::dropIfExists('event_rundowns');
        Schema::dropIfExists('events');
    }
};
