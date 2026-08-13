@extends('app.layout.main')

@section('src_css')
    <style>
        :root {
            --primary-color: #3b5998;
            --bg-color: #f4f6f9;
            --cell-border: #e0e0e0;
        }



        /* Container utama disesuaikan untuk layar HP (max 380px) */
        .calendar-container {
            background: #ffffff;
            width: 100%;
            max-width: 360px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 15px;
            box-sizing: border-box;
        }

        /* Header Kalender */
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            color: #555;
        }

        .calendar-header button {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .calendar-header h2 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        /* Grid Kalender */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            background-color: var(--cell-border);
            /* Warna garis antar sel */
            border: 1px solid var(--cell-border);
            border-radius: 6px;
            overflow: hidden;
        }

        /* Nama Hari */
        .day-name {
            background-color: #ffffff;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            padding: 8px 0;
            color: var(--primary-color);
        }

        /* Sel Tanggal */
        .day-cell {
            background-color: #ffffff;
            position: relative;
            height: 38px;
            /* Cukup besar untuk disentuh, cukup kecil untuk layar HP */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tanggal di pojok atas */
        .date-number {
            position: absolute;
            top: 2px;
            left: 4px;
            font-size: 9px;
            color: #888;
        }

        /* Kode Absensi di tengah */
        .absen-code {
            font-size: 13px;
            font-weight: 700;
            color: #333;
        }

        /* Sel kosong */
        .empty-cell {
            background-color: #fafafa;
        }

        /* Warna khusus untuk kode tertentu (Opsional) */
        .code-ds {
            color: #2e7d32;
        }

        /* Hijau untuk Dinas Siang */
        .code-dl {
            color: #f57f17;
        }

        /* Kuning/Oranye untuk Dinas Libur */
        .code-a {
            color: #c62828;
        }

        /* Merah untuk Alpha */
        .code-off {
            color: #546e7a;
            font-size: 11px;
        }

        /* Abu-abu untuk Libur */

        /* Keterangan / Legend */
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            font-size: 10px;
            justify-content: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .legend-box {
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
        }

        .bg-ds {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .bg-dl {
            background-color: #fff9c4;
            color: #f57f17;
        }

        .bg-off {
            background-color: #cfd8dc;
            color: #546e7a;
        }

        .bg-a {
            background-color: #ffcdd2;
            color: #c62828;
        }
    </style>
@endsection()
@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-4 mr-20 col-sm-12 mb-30 card-box pd-2">
            <!-- TAMBAHAN: Tambahkan class 'w-100' pada d-flex utama agar mengambil ruang penuh -->
            <div class="name-avatar d-flex align-items-center pd-2 pr-2 w-100">
                <div class="avatar mr-2 flex-shrink-0 mCS_img_loaded">
                    <img src="/assets/vendors/images/photo2.jpg" class="border-radius-100 box-shadow" width="50"
                        height="50" alt="">
                </div>
                <!-- TAMBAHAN: Tambahkan class 'w-100' di kelas 'txt' agar melebarkan ruang teks ke kanan -->
                <div class="txt w-100">

                    <!-- PERUBAHAN: Mengganti row/col menjadi d-flex agar badge PT. MBLE terdorong penuh ke kanan -->
                    <div class="d-flex align-items-center mb-1">
                        <span class="user-nrp badge badge-pill badge-sm" data-bgcolor="#e7ebf5" data-color="#265ed7"
                            style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">MBLE-0422003</span>

                        <!-- TAMBAHAN: Class 'ms-auto' di bawah ini yang memaksa badge meloncat ke ujung kanan -->
                        <span class="user-perusahaan badge badge-pill badge-sm ms-auto" data-bgcolor="#e7ebf5"
                            data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">PT.
                            MBLE</span>
                    </div>

                    <!-- PERUBAHAN: Mengganti row/col menjadi d-flex agar badge PT. MBLE terdorong penuh ke kanan -->
                    <div class="d-flex  align-items-center mb-1">
                        <div class=" user-name font-14 weight-600">Dr. Neil Wagner</div>

                        <!-- TAMBAHAN: Class 'ms-auto' di bawah ini yang memaksa badge meloncat ke ujung kanan -->
                        <span class="user-project badge badge-pill badge-sm ms-auto" data-bgcolor="#e7ebf5"
                            data-color="#265ed7" style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">Site
                            SRI</span>
                    </div>

                    <div class="d-flex align-items-center mb-1">
                        <div class="user-jabatan font-12 weight-500" data-color="#b2b1b6"
                            style="color: rgb(178, 177, 182);">
                            Senior Software Engineer</div>

                        <!-- TAMBAHAN: Class 'ms-auto' di bawah ini yang memaksa badge meloncat ke ujung kanan -->
                        <span class="user-departemen badge badge-pill badge-sm ms-auto" data-bgcolor="#e7ebf5"
                            data-color="#265ed7"
                            style="color: rgb(38, 94, 215); background-color: rgb(231, 235, 245);">HRGA</span>
                    </div>



                </div>
            </div>

            <div class="profile-info">
                <div class="row justify-content-center">
                    <div class="col-4 pd-2  ">
                        <ul class="pd-0 text-center card-box pt-10">
                            <li>
                                <div class="icon h1">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    <!-- <i class="icon-copy fa fa-stethoscope" aria-hidden="true"></i> -->
                                </div>
                                <div>Masa Kerja</div>
                                <div class="masa-kerja font-12 weight-500" data-color="#b2b1b6"
                                    style="color: rgb(178, 177, 182);">
                                    2 tahun 2 bulan 2 hari</div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-4 pd-2 " hidden>
                        <ul class="pd-0 text-center card-box pt-10">
                            <li>
                                <div class="icon h1">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    <!-- <i class="icon-copy fa fa-stethoscope" aria-hidden="true"></i> -->
                                </div>
                                <div>Kontrak Kerja</div>
                                <div class="kontrak-kerja font-12 weight-500" data-color="#b2b1b6"
                                    style="color: rgb(178, 177, 182);">
                                    2 tahun 2 bulan 2 hari</div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-4 pd-2 " hidden>
                        <ul class="pd-0 text-center card-box pt-10">
                            <li>
                                <div class="icon h1">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    <!-- <i class="icon-copy fa fa-stethoscope" aria-hidden="true"></i> -->
                                </div>
                                <div>On Site</div>
                                <div class="on-site font-12 weight-500" data-color="#b2b1b6"
                                    style="color: rgb(178, 177, 182);">
                                    2 tahun 2 bulan 2 hari</div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12" hidden>
                        <label for="ABSENSI"></label>
                        <div class="calendar-container">


                            <!-- Calendar -->
                            <div class="calendar-grid">
                                <!-- Hari -->
                                <div class="day-name">Min</div>
                                <div class="day-name">Sen</div>
                                <div class="day-name">Sel</div>
                                <div class="day-name">Rab</div>
                                <div class="day-name">Kam</div>
                                <div class="day-name">Jum</div>
                                <div class="day-name">Sab</div>

                                <!-- Baris 1 (Anggap bulan dimulai hari Minggu) -->
                                <div class="day-cell"><span class="date-number">1</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">2</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">3</span><span
                                        class="absen-code code-dl">DL</span></div>
                                <div class="day-cell"><span class="date-number">4</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">5</span><span
                                        class="absen-code code-a">A</span></div>
                                <div class="day-cell"><span class="date-number">6</span><span
                                        class="absen-code code-off">OFF</span></div>
                                <div class="day-cell"><span class="date-number">7</span><span
                                        class="absen-code code-ds">DS</span></div>

                                <!-- Baris 2 -->
                                <div class="day-cell"><span class="date-number">8</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">9</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">10</span><span
                                        class="absen-code code-dl">DL</span></div>
                                <div class="day-cell"><span class="date-number">11</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">12</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">13</span><span
                                        class="absen-code code-off">OFF</span></div>
                                <div class="day-cell"><span class="date-number">14</span><span
                                        class="absen-code code-ds">DS</span></div>

                                <!-- Baris 3 -->
                                <div class="day-cell"><span class="date-number">15</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">16</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">17</span><span
                                        class="absen-code code-dl">DL</span></div>
                                <div class="day-cell"><span class="date-number">18</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">19</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">20</span><span
                                        class="absen-code code-off">OFF</span></div>
                                <div class="day-cell"><span class="date-number">21</span><span
                                        class="absen-code code-ds">DS</span></div>

                                <!-- Baris 4 -->
                                <div class="day-cell"><span class="date-number">22</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">23</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">24</span><span
                                        class="absen-code code-dl">DL</span></div>
                                <div class="day-cell"><span class="date-number">25</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">26</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">27</span><span
                                        class="absen-code code-off">OFF</span></div>
                                <div class="day-cell"><span class="date-number">28</span><span
                                        class="absen-code code-ds">DS</span></div>

                                <!-- Baris 5 -->
                                <div class="day-cell"><span class="date-number">29</span><span
                                        class="absen-code code-ds">DS</span></div>
                                <div class="day-cell"><span class="date-number">30</span><span
                                        class="absen-code code-ds">DS</span></div>

                                <!-- Sel kosong agar grid genap di akhir bulan -->
                                <div class="day-cell empty-cell"></div>
                                <div class="day-cell empty-cell"></div>
                                <div class="day-cell empty-cell"></div>
                                <div class="day-cell empty-cell"></div>
                                <div class="day-cell empty-cell"></div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4 ml-20 col-sm-12 card-box mb-30">
            <h5 class="pd-20 h5 mb-0">MENU</h5>
            <div class="latest-post">
                <ul>
                    <li>
                        <div class="row">
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="/my-slip">SLIP</a>
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/user.png" alt="">
                                <h4>
                                    <a href="/user">User</a>
                                </h4>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <h5 hidden class="pd-20 h5 mb-0">FEATURE</h5>
            <div hidden class="latest-post">
                <ul>
                    <li>
                        <div class="row">

                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="https://token.mitrabaritogroup.com" target="_blank">Token emp</a>
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="/my-slip">Water Level</a>
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/user.png" alt="">
                                <h4>
                                    <a href="/user">IOT</a>
                                </h4>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <h5 class="pd-20 h5 mb-0">MAIL</h5>
            <div class="latest-post">
                <ul>
                    <li>
                        <div class="row">
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="/my-slip">mail MBG</a>
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="/my-slip">mail PT.MB</a>
                                </h4>
                            </div>
                            <div class="col-3 text-center">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                                <h4>
                                    <a href="/my-slip">mail lama</a>
                                </h4>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Card Grafik Water Level -->
        <div class="col-md-4 ml-20 col-sm-12 row ">
            <div class="col-12">
                <div class="card-box mb-30 pd-20">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="h5 mb-0">
                            <i class="bi bi-droplet text-primary me-2"></i>Grafik Tinggi Muka Air
                        </h5>
                        <span class="text-muted small">Data dari sensor</span>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="waterLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- warning modal change pin --}}
    <div class="modal fade" id="warning-modal-change-pin" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content bg-warning">
                <div class="modal-body text-center">
                    <h3 class="mb-15">
                        <i class="fa fa-exclamation-triangle"></i> Warning
                    </h3>
                    <p>
                        harap ganti password login anda menggunakan PIN, untuk memudahkan login di kemudian hari.
                    </p>
                    <button type="button" class="btn btn-dark close-modal" data-dismiss="modal">
                        batal
                    </button>
                    <a href="/user">
                        <button type="button" class="btn btn-success">
                            Ubah
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script>
        function hitungMasaKerja(tanggalMulaiStr) {
            // Parsing string YYYY-MM-DD
            const parts = tanggalMulaiStr.split('-').map(Number);
            const start = new Date(parts[0], parts[1] - 1, parts[2]);
            const now = new Date(); // hari ini

            let tahun = now.getFullYear() - start.getFullYear();
            let bulan = now.getMonth() - start.getMonth();
            let hari = now.getDate() - start.getDate();

            // Koreksi bulan
            if (bulan < 0) {
                tahun--;
                bulan += 12;
            }

            // Koreksi hari
            if (hari < 0) {
                const bulanSebelum = new Date(now.getFullYear(), now.getMonth(), 0);
                const hariDalamBulanSebelum = bulanSebelum.getDate();
                hari += hariDalamBulanSebelum;
                bulan--;
                if (bulan < 0) {
                    tahun--;
                    bulan += 12;
                }
            }

            return {
                tahun,
                bulan,
                hari
            };
        }

        function formatMasaKerja(tahun, bulan, hari) {
            return `${tahun} tahun ${bulan} bulan ${hari} hari`;
        }

        $(document).ready(function() {
            console.log('ready function :', db);
            const tglMulai = db['FILTER_APP']['PROFILE']['TANGGAL-MASUK-KERJA--TMK-']['value_data'];
            const hasil = hitungMasaKerja(tglMulai);

            // Format teks
            const teks = formatMasaKerja(hasil.tahun, hasil.bulan, hasil.hari);

            // Animasi fadeIn
            $('.masa-kerja')
                .text(teks)
                .fadeIn(500);

            const tglMulaiKontrak = db['FILTER_APP']['PROFILE']['TANGGAL-MASUK-KERJA--TMK-']['value_data'];
            const hasilKontrak = hitungMasaKerja(tglMulaiKontrak);

            // Format teks
            const teksKontrak = formatMasaKerja(hasilKontrak.tahun, hasilKontrak.bulan, hasilKontrak.hari);

            // Animasi fadeIn
            $('.kontrak-kerja')
                .text(teksKontrak)
                .fadeIn(500);

            loadWaterLevel();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        async function loadWaterLevel() {
            try {
                const response = await fetch('/feature/water-level/data');
                if (!response.ok) throw new Error('Gagal mengambil data');
                const data = await response.json();

                // Urutkan data berdasarkan tanggal & jam (ascending)
                data.sort((a, b) => (a.tanggal + a.jam).localeCompare(b.tanggal + b.jam));

                // Simpan data terakhir per lokasi per tanggal
                const mapByDate = {};
                data.forEach(d => {
                    if (!mapByDate[d.tanggal]) mapByDate[d.tanggal] = {
                        'PT. SRI': null,
                        'PT. MB': null
                    };
                    // overwrite dengan nilai terbaru
                    mapByDate[d.tanggal][d.lokasi] = d.tinggi;
                });

                const tanggalList = Object.keys(mapByDate).sort();
                const labels = tanggalList.map(tgl => {
                    const parts = tgl.split('-'); // asumsi YYYY-MM-DD
                    return `${parts[2]}/${parts[1]}`; // DD/MM
                });

                const sriData = tanggalList.map(tgl => mapByDate[tgl]['PT. SRI']);
                const mbData = tanggalList.map(tgl => mapByDate[tgl]['PT. MB']);

                const ctx = document.getElementById('waterLevelChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'PT. SRI',
                                data: sriData,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13,110,253,0.1)',
                                fill: true,
                                tension: 0.3,
                                spanGaps: true
                            },
                            {
                                label: 'PT. MB',
                                data: mbData,
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220,53,69,0.1)',
                                fill: true,
                                tension: 0.3,
                                spanGaps: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 10
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading water level chart:', error);
                document.getElementById('waterLevelChart').parentElement.innerHTML =
                    '<div class="text-center text-muted py-5">Gagal memuat data tinggi muka air.</div>';
            }
        }
    </script>
@endsection()
