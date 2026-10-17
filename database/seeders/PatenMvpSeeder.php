<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\EventStand;
use App\Models\PosTransaction;
use App\Models\StandMenu;
use App\Models\UmkmProfile;
use App\Models\User;
use App\Services\SpkEngineService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatenMvpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clean existing records in dependency order for a pure, authentic state
        DB::statement('TRUNCATE TABLE spk_umkm_evaluations, attendees, pos_transactions, stand_menus, event_stands, umkm_profiles, event_rundowns, events, users RESTART IDENTITY CASCADE;');

        // 2. Seed Core Official Accounts
        $adminDinas = User::create([
            'name' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'email' => 'kadis@kutim.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin_dinas',
        ]);

        $organizer = User::create([
            'name' => 'Panitia Pelaksana Bazar Kutim',
            'email' => 'panitia@kutim.go.id',
            'password' => Hash::make('password'),
            'role' => 'organizer',
        ]);

        // 3. Seed Realistic Authentic UMKM Profiles (Kutai Timur)
        $umkmData = [
            [
                'nik' => '6408040101850001',
                'no_kk' => '6408040101850001',
                'nama_pemilik' => 'Hj. Siti Aminah',
                'nama_usaha' => 'Amplang & Kerupuk Ikan Batu Putih',
                'nomor_wa' => '081254320001',
                'kecamatan' => 'Sangatta Utara',
                'alamat_usaha' => 'Jl. Yos Sudarso II No. 14, Teluk Lingga, Sangatta Utara',
                'nib' => '1234000192837',
                'has_halal' => true,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408041503880002',
                'no_kk' => '6408041503880002',
                'nama_pemilik' => 'Ahmad Fauzi',
                'nama_usaha' => 'Kopi Luwak & Robusta Kutai Kafe',
                'nomor_wa' => '081347650002',
                'kecamatan' => 'Sangatta Utara',
                'alamat_usaha' => 'Jl. APT Pranoto No. 88, Swarga Bara, Sangatta Utara',
                'nib' => '1234000293848',
                'has_halal' => true,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408122007900003',
                'no_kk' => '6408122007900003',
                'nama_pemilik' => 'Nurul Hasanah',
                'nama_usaha' => 'Sanggar Krispi Pisang Raja Bukit Pelangi',
                'nomor_wa' => '082155670003',
                'kecamatan' => 'Sangatta Selatan',
                'alamat_usaha' => 'Jl. Poros Sangatta-Bontang KM 3, Sangatta Selatan',
                'nib' => '1234000384756',
                'has_halal' => false,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408045509920004',
                'no_kk' => '6408045509920004',
                'nama_pemilik' => 'Bernadetha Lung',
                'nama_usaha' => 'Kriya Tenun Tradisional Ulap Doyo Sangatta',
                'nomor_wa' => '085246780004',
                'kecamatan' => 'Sangatta Utara',
                'alamat_usaha' => 'Kompleks Budaya Dayak, Teluk Lingga, Sangatta Utara',
                'nib' => '1234000475869',
                'has_halal' => false,
                'has_pirt' => false,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408051206840005',
                'no_kk' => '6408051206840005',
                'nama_pemilik' => 'Bambang Sutrisno',
                'nama_usaha' => 'Abon & Olahan Ikan Haruan Teluk Pandan',
                'nomor_wa' => '081350890005',
                'kecamatan' => 'Teluk Pandan',
                'alamat_usaha' => 'Jl. Poros Bontang-Sangatta KM 18, Teluk Pandan',
                'nib' => '1234000567980',
                'has_halal' => true,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408062408930006',
                'no_kk' => '6408062408930006',
                'nama_pemilik' => 'Zulkifli Rahman',
                'nama_usaha' => 'Minuman Herbal Sarabba & Jahe Merah Bengalon',
                'nomor_wa' => '082251340006',
                'kecamatan' => 'Bengalon',
                'alamat_usaha' => 'Jl. Sepakat RT 04, Desa Sepaso, Bengalon',
                'nib' => '1234000658493',
                'has_halal' => false,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408044811870007',
                'no_kk' => '6408044811870007',
                'nama_pemilik' => 'Dewi Lestari',
                'nama_usaha' => 'Dapur Bunda Sambal Gami Mandau Khas Kutim',
                'nomor_wa' => '081250120007',
                'kecamatan' => 'Sangatta Utara',
                'alamat_usaha' => 'Jl. Wolter Monginsidi No. 12, Sangatta Utara',
                'nib' => '1234000749382',
                'has_halal' => true,
                'has_pirt' => false,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408101402800008',
                'no_kk' => '6408101402800008',
                'nama_pemilik' => 'Fransiskus Xaverius',
                'nama_usaha' => 'Madu Hutan & Kelulut Liar Muara Wahau',
                'nomor_wa' => '085348910008',
                'kecamatan' => 'Muara Wahau',
                'alamat_usaha' => 'Desa Nehas Liah Bing, Muara Wahau',
                'nib' => '1234000838291',
                'has_halal' => true,
                'has_pirt' => true,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408086205890009',
                'no_kk' => '6408086205890009',
                'nama_pemilik' => 'Rina Wulandari',
                'nama_usaha' => 'Batik Kutim Motif Daun Sawit & Eksotis Tambang',
                'nomor_wa' => '081349820009',
                'kecamatan' => 'Rantau Pulung',
                'alamat_usaha' => 'Desa Kebon Agung, Rantau Pulung',
                'nib' => '1234000927384',
                'has_halal' => false,
                'has_pirt' => false,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408041904910010',
                'no_kk' => '6408041904910010',
                'nama_pemilik' => 'Muhammad Ridwan',
                'nama_usaha' => 'Kedai Roti Canai & Teh Tarik Al-Barokah',
                'nomor_wa' => '082156780010',
                'kecamatan' => 'Sangatta Utara',
                'alamat_usaha' => 'Jl. Karya Etam No. 45, Sangatta Utara',
                'nib' => '1234001018273',
                'has_halal' => true,
                'has_pirt' => false,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
            [
                'nik' => '6408092209860011',
                'no_kk' => '6408092209860011',
                'nama_pemilik' => 'Supriyanto',
                'nama_usaha' => 'Olahan Buah Naga & Sirup Kaliorang Jaya',
                'nomor_wa' => '081258900011',
                'kecamatan' => 'Kaliorang',
                'alamat_usaha' => 'Desa Bukit Harapan, Kaliorang',
                'nib' => '1234001109182',
                'has_halal' => true,
                'has_pirt' => true,
                'pernah_terima_hibah' => true,
                'tahun_terima_hibah' => 2022,
            ],
            [
                'nik' => '6408115610940012',
                'no_kk' => '6408115610940012',
                'nama_pemilik' => 'Kristina Jalung',
                'nama_usaha' => 'Anyaman Rotan & Manik Tradisional Dayak Wehea',
                'nomor_wa' => '085249010012',
                'kecamatan' => 'Kongbeng',
                'alamat_usaha' => 'Desa Miau Baru, Kongbeng',
                'nib' => null,
                'has_halal' => false,
                'has_pirt' => false,
                'pernah_terima_hibah' => false,
                'tahun_terima_hibah' => null,
            ],
        ];

        $umkms = [];
        foreach ($umkmData as $item) {
            $umkms[] = UmkmProfile::create($item);
        }

        // 4. Seed Primary Event: "Pesta Rakyat & Bazar UMKM Sangatta Hebat 2026"
        $event1 = Event::create([
            'user_id' => $organizer->id,
            'slug' => 'bazar-umkm-sangatta-hebat-2026',
            'nama_event' => 'Pesta Rakyat & Bazar UMKM Sangatta Hebat 2026',
            'deskripsi' => 'Pekan promosi produk unggulan UMKM, kuliner khas daerah, dan kriya kreatif Kabupaten Kutai Timur dalam rangka percepatan ekonomi kerakyatan dan akuntabilitas seleksi bantuan modal usaha.',
            'penyelenggara' => 'Dinas Koperasi & UKM Kab. Kutai Timur',
            'lokasi_venue' => 'Lapangan Helipad Bukit Pelangi, Sangatta Utara',
            'tanggal_mulai' => Carbon::now()->subDays(3)->setTime(8, 0),
            'tanggal_selesai' => Carbon::now()->addDays(4)->setTime(22, 0),
            'secure_management_token' => Str::random(64),
            'template_theme' => 'kutim_formal',
            'color_mode' => 'duo',
            'color_primary' => '#132952',
            'color_secondary' => '#00B262',
            'gamifikasi_mode' => 'doorprize',
            'harga_htm' => 0.00,
            'is_awarding_active' => true,
        ]);

        // Rundowns for Event 1
        $rundowns1 = [
            [
                'waktu_mulai' => Carbon::now()->subDays(3)->setTime(9, 0),
                'waktu_selesai' => Carbon::now()->subDays(3)->setTime(11, 30),
                'judul_sesi' => 'Upacara Pembukaan & Pawai Produk Unggulan 18 Kecamatan',
                'pengisi_acara' => 'Bupati Kutai Timur & Kepala Dinas Koperasi UKM',
                'deskripsi_sesi' => 'Peresmian pembukaan bazar akbar dan peluncuran QRIS merchant serentak bagi stand UMKM.',
                'urutan' => 1,
            ],
            [
                'waktu_mulai' => Carbon::now()->subDays(2)->setTime(14, 0),
                'waktu_selesai' => Carbon::now()->subDays(2)->setTime(16, 30),
                'judul_sesi' => 'Workshop Akselerasi NIB, Sertifikasi Halal & Standarisasi Kemasan',
                'pengisi_acara' => 'Tim Fasilitator PLUT-KUMKM Kutim & Kemenag',
                'deskripsi_sesi' => 'Pendampingan langsung penerbitan perizinan berusaha bagi pelaku usaha mikro.',
                'urutan' => 2,
            ],
            [
                'waktu_mulai' => Carbon::now()->subDays(1)->setTime(19, 0),
                'waktu_selesai' => Carbon::now()->subDays(1)->setTime(22, 0),
                'judul_sesi' => 'Festival Musik Akustik Pemuda Kutai Timur & Live Cooking Gami',
                'pengisi_acara' => 'Komunitas Musisi Sangatta & Chef Lokal',
                'deskripsi_sesi' => 'Eksplorasi cita rasa kuliner khas Kutim dipadukan panggung kreatif anak muda.',
                'urutan' => 3,
            ],
            [
                'waktu_mulai' => Carbon::now()->setTime(16, 0),
                'waktu_selesai' => Carbon::now()->setTime(18, 0),
                'judul_sesi' => 'Gelar Wicara: Digitalisasi Transaksi dan Akuntabilitas Hibah Modal Usaha',
                'pengisi_acara' => 'Bank Indonesia Perwakilan Kaltim & Bank Kaltimtara',
                'deskripsi_sesi' => 'Sosialisasi pencatatan omzet transparan berbasis POS untuk verifikasi hibah APBD.',
                'urutan' => 4,
            ],
            [
                'waktu_mulai' => Carbon::now()->addDays(2)->setTime(19, 30),
                'waktu_selesai' => Carbon::now()->addDays(2)->setTime(22, 30),
                'judul_sesi' => 'Malam Puncak Awarding UMKM Terbaik & Penarikan Doorprize Utama',
                'pengisi_acara' => 'Panitia Pelaksana & Dewan Juri SPK',
                'deskripsi_sesi' => 'Pengumuman ranking evaluasi SPK, stand terfavorit, serta undian motor dan sepeda listrik.',
                'urutan' => 5,
            ],
        ];

        foreach ($rundowns1 as $rd) {
            $event1->rundowns()->create($rd);
        }

        // Stands for Event 1 (8 Stands)
        $standsConfig1 = [
            [
                'umkm_index' => 0,
                'nomor_stand' => 'STAND-01',
                'kategori_jualan' => 'kuliner',
                'daya' => 900,
                'menus' => [
                    ['nama_item' => 'Amplang Ikan Pipih Super (250g)', 'harga' => 35000],
                    ['nama_item' => 'Kerupuk Ikan Belida Gurih (500g)', 'harga' => 50000],
                    ['nama_item' => 'Amplang Kuku Macan Renyah', 'harga' => 25000],
                ],
                'target_tx' => 32,
                'qris_ratio' => 0.85,
            ],
            [
                'umkm_index' => 1,
                'nomor_stand' => 'STAND-02',
                'kategori_jualan' => 'kuliner',
                'daya' => 1300,
                'menus' => [
                    ['nama_item' => 'Kopi Robusta Kutai Dingin', 'harga' => 18000],
                    ['nama_item' => 'Kopi Luwak Asli Sangatta Pour Over', 'harga' => 35000],
                    ['nama_item' => 'Es Kopi Susu Aren Bukit Pelangi', 'harga' => 20000],
                    ['nama_item' => 'Teh Rempah Dayak Hangat', 'harga' => 15000],
                ],
                'target_tx' => 45,
                'qris_ratio' => 0.90,
            ],
            [
                'umkm_index' => 2,
                'nomor_stand' => 'STAND-03',
                'kategori_jualan' => 'kuliner',
                'daya' => 450,
                'menus' => [
                    ['nama_item' => 'Keripik Pisang Sanggar Coklat Keju', 'harga' => 18000],
                    ['nama_item' => 'Pisang Crispy Saus Karamel', 'harga' => 15000],
                    ['nama_item' => 'Keripik Pisang Original Renyah', 'harga' => 12000],
                ],
                'target_tx' => 26,
                'qris_ratio' => 0.60,
            ],
            [
                'umkm_index' => 3,
                'nomor_stand' => 'STAND-04',
                'kategori_jualan' => 'kriya',
                'daya' => 450,
                'menus' => [
                    ['nama_item' => 'Tas Selempang Tenun Ulap Doyo', 'harga' => 175000],
                    ['nama_item' => 'Gelang & Dompet Khas Etnik Dayak', 'harga' => 45000],
                    ['nama_item' => 'Syal Tenun Pewarna Alami', 'harga' => 95000],
                ],
                'target_tx' => 18,
                'qris_ratio' => 0.70,
            ],
            [
                'umkm_index' => 4,
                'nomor_stand' => 'STAND-05',
                'kategori_jualan' => 'kuliner',
                'daya' => 900,
                'menus' => [
                    ['nama_item' => 'Abon Ikan Haruan Pedas Manis (200g)', 'harga' => 40000],
                    ['nama_item' => 'Ikan Asin Teluk Pandan Premium', 'harga' => 30000],
                    ['nama_item' => 'Pilus Haruan Krispi Gurih', 'harga' => 15000],
                ],
                'target_tx' => 28,
                'qris_ratio' => 0.75,
            ],
            [
                'umkm_index' => 5,
                'nomor_stand' => 'STAND-06',
                'kategori_jualan' => 'kuliner',
                'daya' => 450,
                'menus' => [
                    ['nama_item' => 'Sarabba Susu Jahe Merah Hangat', 'harga' => 15000],
                    ['nama_item' => 'Sarabba Telur Bebek Stamina', 'harga' => 22000],
                    ['nama_item' => 'Bubuk Jahe Merah Instan Kotak', 'harga' => 30000],
                ],
                'target_tx' => 22,
                'qris_ratio' => 0.40,
            ],
            [
                'umkm_index' => 6,
                'nomor_stand' => 'STAND-07',
                'kategori_jualan' => 'kuliner',
                'daya' => 900,
                'menus' => [
                    ['nama_item' => 'Paket Nasi Sambal Gami Bawis', 'harga' => 30000],
                    ['nama_item' => 'Paket Nasi Sambal Gami Ayam', 'harga' => 25000],
                    ['nama_item' => 'Gami Cumi Cobek Panas', 'harga' => 32000],
                ],
                'target_tx' => 35,
                'qris_ratio' => 0.80,
            ],
            [
                'umkm_index' => 7,
                'nomor_stand' => 'STAND-08',
                'kategori_jualan' => 'kuliner',
                'daya' => 450,
                'menus' => [
                    ['nama_item' => 'Madu Hutan Liar Murni (500ml)', 'harga' => 120000],
                    ['nama_item' => 'Madu Kelulut Asli Propolis (250ml)', 'harga' => 85000],
                    ['nama_item' => 'Sarang Madu Alami Potong', 'harga' => 65000],
                ],
                'target_tx' => 20,
                'qris_ratio' => 0.85,
            ],
        ];

        $event1Stands = [];
        foreach ($standsConfig1 as $sc) {
            $umkm = $umkms[$sc['umkm_index']];
            $stand = EventStand::create([
                'event_id' => $event1->id,
                'umkm_id' => $umkm->id,
                'nomor_stand' => $sc['nomor_stand'],
                'kategori_jualan' => $sc['kategori_jualan'],
                'daya_listrik_watt' => $sc['daya'],
                'fasilitas_detail' => [
                    'meja' => 2,
                    'kursi' => 4,
                    'tenda' => '3x3 meter',
                    'stopkontak' => 2,
                    'lampu_led' => '18W',
                ],
                'pin_pos' => '123456',
                'total_omzet_terekam' => 0.00,
                'total_transaksi_terekam' => 0,
            ]);

            // Create Menus
            $createdMenus = [];
            foreach ($sc['menus'] as $m) {
                $createdMenus[] = StandMenu::create([
                    'stand_id' => $stand->id,
                    'nama_item' => $m['nama_item'],
                    'harga' => $m['harga'],
                    'is_available' => true,
                ]);
            }

            // Create Realistic Transactions spread across past 3 days
            $totalOmzet = 0.00;
            $txCount = $sc['target_tx'];
            $qrisTarget = (int) round($txCount * $sc['qris_ratio']);

            for ($i = 0; $i < $txCount; $i++) {
                $menu = $createdMenus[array_rand($createdMenus)];
                $qty = rand(1, 3);
                $subtotal = $qty * (float) $menu->harga;
                $isQris = $i < $qrisTarget;
                $daysAgo = rand(0, 2);
                $hour = rand(9, 21);
                $minute = rand(0, 59);
                $createdAt = Carbon::now()->subDays($daysAgo)->setTime($hour, $minute);

                PosTransaction::create([
                    'event_id' => $event1->id,
                    'stand_id' => $stand->id,
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'metode_pembayaran' => $isQris ? 'qris' : 'cash',
                    'created_at' => $createdAt,
                ]);

                $totalOmzet += $subtotal;
            }

            // Update stand recorded totals
            $stand->update([
                'total_omzet_terekam' => $totalOmzet,
                'total_transaksi_terekam' => $txCount,
            ]);

            $event1Stands[] = $stand;
        }

        // Attendees for Event 1 (30 Attendees with realistic names & tickets)
        $attendeeNames = [
            'Bayu Pratama', 'Siti Rahmawati', 'Dedi Kurniawan', 'Rina Anggraini', 'Muhammad Ilham',
            'Novita Sari', 'Agus Setiawan', 'Tri Wahyuni', 'Fajar Ramadhan', 'Indah Permata',
            'Rizky Saputra', 'Dewi Anggraini', 'Hendra Gunawan', 'Yulia Fitriani', 'Budi Santoso',
            'Fitri Handayani', 'Eko Prasetyo', 'Nur Aini', 'Aris Munandar', 'Wulandari',
            'Surya Darma', 'Mega Utami', 'Rian Hidayat', 'Dian Lestari', 'Wahyu Hidayat',
            'Citra Dewi', 'Andi Firmansyah', 'Putri Ayu', 'Denny Hermawan', 'Ratna Juwita',
        ];

        foreach ($attendeeNames as $idx => $name) {
            $isCheckedIn = $idx < 22;
            $isWinner = in_array($idx, [3, 11, 20]);
            $ticketNumber = sprintf('PKT-2026-%04d', $idx + 1);

            Attendee::create([
                'event_id' => $event1->id,
                'nama_lengkap' => $name,
                'nomor_wa' => sprintf('08125500%04d', $idx + 1),
                'kecamatan' => ['Sangatta Utara', 'Sangatta Selatan', 'Bengalon', 'Teluk Pandan'][rand(0, 3)],
                'nomor_tiket_doorprize' => $ticketNumber,
                'tipe_registrasi' => 'doorprize',
                'is_checked_in' => $isCheckedIn,
                'check_in_time' => $isCheckedIn ? Carbon::now()->subDays(rand(0, 2))->setTime(rand(10, 20), rand(0, 59)) : null,
                'is_doorprize_winner' => $isWinner,
                'created_at' => Carbon::now()->subDays(rand(1, 4)),
            ]);
        }

        // 5. Seed Secondary Event: "Festival Kuliner Pesisir & Hasil Laut Sangkulirang 2026"
        $event2 = Event::create([
            'user_id' => $organizer->id,
            'slug' => 'festival-kuliner-sangkulirang-2026',
            'nama_event' => 'Festival Kuliner Pesisir & Hasil Laut Sangkulirang 2026',
            'deskripsi' => 'Pameran kelezatan kuliner bahari, olahan hasil laut khas pesisir Sangkulirang, dan hilirisasi produk perikanan mandiri keluarga nelayan Kutai Timur.',
            'penyelenggara' => 'Dekranasda & Forum UMKM Sangkulirang',
            'lokasi_venue' => 'Dermaga Wisata Sangkulirang, Kab. Kutai Timur',
            'tanggal_mulai' => Carbon::now()->subDays(1)->setTime(10, 0),
            'tanggal_selesai' => Carbon::now()->addDays(3)->setTime(21, 0),
            'secure_management_token' => Str::random(64),
            'template_theme' => 'kutim_formal',
            'color_mode' => 'duo',
            'color_primary' => '#0F766E',
            'color_secondary' => '#F59E0B',
            'gamifikasi_mode' => 'doorprize',
            'harga_htm' => 0.00,
            'is_awarding_active' => true,
        ]);

        $rundowns2 = [
            [
                'waktu_mulai' => Carbon::now()->subDays(1)->setTime(10, 0),
                'waktu_selesai' => Carbon::now()->subDays(1)->setTime(12, 0),
                'judul_sesi' => 'Pembukaan Festival Bahari & Parade Perahu Hias Nelayan',
                'pengisi_acara' => 'Camat Sangkulirang & Tokoh Adat Pesisir',
                'deskripsi_sesi' => 'Apresiasi tradisi bahari dan pameran aneka ikan segar serta olahan laut.',
                'urutan' => 1,
            ],
            [
                'waktu_mulai' => Carbon::now()->setTime(15, 0),
                'waktu_selesai' => Carbon::now()->setTime(17, 30),
                'judul_sesi' => 'Lomba Kreasi Kuliner Hasil Laut & Demo Olahan Bebas Pengawet',
                'pengisi_acara' => 'Tim PKK Kecamatan & Pengusaha Kuliner',
                'deskripsi_sesi' => 'Inovasi menu bergizi ramah pasar dan standarisasi higienitas olahan ikan.',
                'urutan' => 2,
            ],
        ];

        foreach ($rundowns2 as $rd) {
            $event2->rundowns()->create($rd);
        }

        // Stands for Event 2 (4 Stands)
        $standsConfig2 = [
            [
                'umkm_index' => 4, // Bambang Sutrisno (Abon Haruan)
                'nomor_stand' => 'STAND-01',
                'kategori_jualan' => 'kuliner',
                'menus' => [
                    ['nama_item' => 'Abon Ikan Tenggiri Asli (250g)', 'harga' => 45000],
                    ['nama_item' => 'Kerupuk Ikan Laut Renyah', 'harga' => 20000],
                ],
                'target_tx' => 20,
            ],
            [
                'umkm_index' => 10, // Supriyanto (Kaliorang)
                'nomor_stand' => 'STAND-02',
                'kategori_jualan' => 'agribisnis',
                'menus' => [
                    ['nama_item' => 'Sirup Buah Naga Segar Dingin', 'harga' => 12000],
                    ['nama_item' => 'Stik Buah Naga Renyah Manis', 'harga' => 15000],
                ],
                'target_tx' => 25,
            ],
            [
                'umkm_index' => 8, // Rina Wulandari (Batik)
                'nomor_stand' => 'STAND-03',
                'kategori_jualan' => 'kriya',
                'menus' => [
                    ['nama_item' => 'Kain Batik Motif Ikan & Mangrove', 'harga' => 150000],
                    ['nama_item' => 'Scarf Motif Etnik Bahari', 'harga' => 65000],
                ],
                'target_tx' => 12,
            ],
            [
                'umkm_index' => 9, // Muhammad Ridwan (Roti Canai)
                'nomor_stand' => 'STAND-04',
                'kategori_jualan' => 'kuliner',
                'menus' => [
                    ['nama_item' => 'Roti Canai Kari Ikan Laut', 'harga' => 25000],
                    ['nama_item' => 'Teh Tarik Rempah Dingin', 'harga' => 15000],
                ],
                'target_tx' => 30,
            ],
        ];

        foreach ($standsConfig2 as $sc) {
            $umkm = $umkms[$sc['umkm_index']];
            $stand = EventStand::create([
                'event_id' => $event2->id,
                'umkm_id' => $umkm->id,
                'nomor_stand' => $sc['nomor_stand'],
                'kategori_jualan' => $sc['kategori_jualan'],
                'daya_listrik_watt' => 900,
                'fasilitas_detail' => ['meja' => 2, 'kursi' => 4, 'tenda' => '3x3m'],
                'pin_pos' => '123456',
                'total_omzet_terekam' => 0.00,
                'total_transaksi_terekam' => 0,
            ]);

            $createdMenus = [];
            foreach ($sc['menus'] as $m) {
                $createdMenus[] = StandMenu::create([
                    'stand_id' => $stand->id,
                    'nama_item' => $m['nama_item'],
                    'harga' => $m['harga'],
                    'is_available' => true,
                ]);
            }

            // Transactions only within the event's running period (started yesterday)
            $totalOmzet = 0.00;
            $txCount = $sc['target_tx'];
            for ($i = 0; $i < $txCount; $i++) {
                $menu = $createdMenus[array_rand($createdMenus)];
                $qty = rand(1, 2);
                $subtotal = $qty * (float) $menu->harga;
                $isQris = (rand(1, 10) <= 7);
                // Constrain to event period: started 1 day ago, hours 10-21
                $hour = rand(10, 21);
                $minute = rand(0, 59);
                $txDay = rand(0, 1); // 0 = today, 1 = yesterday (event start)
                $createdAt = Carbon::now()->subDays($txDay)->setTime($hour, $minute);

                PosTransaction::create([
                    'event_id' => $event2->id,
                    'stand_id' => $stand->id,
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                    'metode_pembayaran' => $isQris ? 'qris' : 'cash',
                    'created_at' => $createdAt,
                ]);

                $totalOmzet += $subtotal;
            }

            $stand->update([
                'total_omzet_terekam' => $totalOmzet,
                'total_transaksi_terekam' => $txCount,
            ]);
        }

        // Attendees for Event 2 (15 Attendees)
        for ($i = 1; $i <= 15; $i++) {
            Attendee::create([
                'event_id' => $event2->id,
                'nama_lengkap' => 'Warga Sangkulirang '.$i,
                'nomor_wa' => sprintf('08215600%04d', $i),
                'kecamatan' => 'Sangkulirang',
                'nomor_tiket_doorprize' => sprintf('TKT-SKL-%03d', $i),
                'tipe_registrasi' => 'doorprize',
                'is_checked_in' => true,
                'check_in_time' => Carbon::now()->subHours(rand(2, 20)),
                'is_doorprize_winner' => ($i === 5),
                'created_at' => Carbon::now()->subDays(1),
            ]);
        }

        // 6. Seed Third Event: "Bazar Ekonomi Kreatif & Kriya Bengalon 2026"
        $event3 = Event::create([
            'user_id' => $organizer->id,
            'slug' => 'bazar-ekraf-kriya-bengalon-2026',
            'nama_event' => 'Bazar Ekonomi Kreatif & Kriya Bengalon 2026',
            'deskripsi' => 'Ajang pameran kriya kreatif, kerajinan etnik Dayak Wehea, tenun tradisional, dan produk olahan herbal khas Bengalon dalam menyongsong sentra ekonomi mandiri.',
            'penyelenggara' => 'Forum Pemuda Pelopor & UMKM Bengalon',
            'lokasi_venue' => 'Alun-Alun Simpang Perdau, Bengalon, Kutai Timur',
            'tanggal_mulai' => Carbon::now()->addDays(5)->setTime(8, 0),
            'tanggal_selesai' => Carbon::now()->addDays(8)->setTime(22, 0),
            'secure_management_token' => Str::random(64),
            'template_theme' => 'kutim_formal',
            'color_mode' => 'mono',
            'color_primary' => '#132952',
            'gamifikasi_mode' => 'doorprize',
            'harga_htm' => 0.00,
            'is_awarding_active' => true,
        ]);

        // Event 3 is a FUTURE event — stands are registered but NO transactions yet
        $standsConfig3 = [
            ['umkm_index' => 5, 'nomor_stand' => 'STAND-01', 'kategori' => 'kuliner'],
            ['umkm_index' => 11, 'nomor_stand' => 'STAND-02', 'kategori' => 'kriya'],
            ['umkm_index' => 3, 'nomor_stand' => 'STAND-03', 'kategori' => 'kriya'],
        ];

        foreach ($standsConfig3 as $sc) {
            $umkm = $umkms[$sc['umkm_index']];
            $stand = EventStand::create([
                'event_id' => $event3->id,
                'umkm_id' => $umkm->id,
                'nomor_stand' => $sc['nomor_stand'],
                'kategori_jualan' => $sc['kategori'],
                'daya_listrik_watt' => 450,
                'fasilitas_detail' => ['meja' => 2, 'kursi' => 2, 'tenda' => '2x2m'],
                'pin_pos' => '123456',
                'total_omzet_terekam' => 0.00,
                'total_transaksi_terekam' => 0,
            ]);

            // Menus prepared in advance (ready for event day)
            StandMenu::create([
                'stand_id' => $stand->id,
                'nama_item' => 'Produk Khas '.$umkm->nama_usaha,
                'harga' => 25000,
                'is_available' => true,
            ]);
            StandMenu::create([
                'stand_id' => $stand->id,
                'nama_item' => 'Paket Souvenir Etnik Bengalon',
                'harga' => 50000,
                'is_available' => true,
            ]);

            // No transactions — event hasn't started yet
        }

        // Attendees for Event 3 — pre-registered only (event hasn't started)
        for ($i = 1; $i <= 10; $i++) {
            Attendee::create([
                'event_id' => $event3->id,
                'nama_lengkap' => 'Warga Bengalon '.$i,
                'nomor_wa' => sprintf('08235700%04d', $i),
                'kecamatan' => 'Bengalon',
                'nomor_tiket_doorprize' => sprintf('TKT-BGL-%03d', $i),
                'tipe_registrasi' => 'doorprize',
                'is_checked_in' => false,
                'check_in_time' => null,
                'is_doorprize_winner' => false,
                'created_at' => Carbon::now()->subDays(1),
            ]);
        }

        // 7. Execute SAW SPK Engine Evaluation — only for events with actual transactions
        /** @var SpkEngineService $spkService */
        $spkService = app(SpkEngineService::class);
        $spkService->evaluate($event1);
        $spkService->evaluate($event2);
        // Event 3 skipped — no transactions yet (future event)
    }
}
