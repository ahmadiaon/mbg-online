@extends('app.layout.main')

@section('src_css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body { background: #f4f6f9; }
        .card { border-radius: 1rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
        .table-container { max-height: 400px; overflow-y: auto; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@endsection

@section('content')
    <h2 class="fw-bold mb-4"><i class="bi bi-droplet text-primary me-2"></i>Pendataan Tinggi Muka Air</h2>

    <div class="row">
        <!-- Form Input -->
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">Input Data</h5>
                </div>
                <div class="card-body">
                    <form id="waterForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="inputTanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <select class="form-control" id="inputLokasi" name="lokasi" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="PT. SRI">PT. SRI</option>
                                <option value="PT. MB">PT. MB</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam</label>
                            <input type="time" class="form-control" id="inputJam" name="jam" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tinggi Air (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="inputTinggi" name="tinggi" placeholder="0.0" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grafik -->
        <div id="captureArea">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Grafik Tinggi Air</h5>
                    <button class="btn btn-sm btn-success" id="btnExportGrafik">
                        <i class="bi bi-camera"></i> Export Gambar
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="waterChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Lokasi -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Filter Lokasi</label>
            <select id="filterLokasi" class="form-select">
                <option value="all">Semua Lokasi</option>
                <option value="PT. SRI">PT. SRI</option>
                <option value="PT. MB">PT. MB</option>
            </select>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Data Historis</h5>
            <span class="text-muted small" id="dataCount">0 record</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-container">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Jam</th>
                            <th>Tinggi (cm)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Tinggi Air</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input name="tanggal" type="date" class="form-control" id="editTanggal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <select name="lokasi" class="form-control" id="editLokasi" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="PT. SRI">PT. SRI</option>
                                <option value="PT. MB">PT. MB</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam</label>
                            <input name="jam" type="time" class="form-control" id="editJam" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tinggi Air (cm)</label>
                            <input name="tinggi" type="number" step="0.1" class="form-control" id="editTinggi" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_code')
    <script>
        // Grafik
        const ctx = document.getElementById('waterChart').getContext('2d');
        const waterChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'PT. SRI',
                        data: [],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13,110,253,0.1)',
                        fill: true,
                        tension: 0.3,
                        spanGaps: true
                    },
                    {
                        label: 'PT. MB',
                        data: [],
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
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        let allData = [];          // Menyimpan data mentah dari server
        let currentLokasi = 'all'; // Filter lokasi aktif

        // Ambil data dari server
        async function fetchData() {
            try {
                const response = await fetch('/feature/water-level/data');
                const data = await response.json();
                allData = data;
                applyFilterAndRender();
            } catch (err) {
                console.error('Gagal mengambil data:', err);
            }
        }

        // Fungsi memfilter dan merender tabel + grafik
        function applyFilterAndRender() {
            // Filter data untuk tabel
            let filteredData = allData;
            if (currentLokasi !== 'all') {
                filteredData = allData.filter(item => item.lokasi === currentLokasi);
            }

            // Render tabel
            renderTable(filteredData);

            // Render grafik
            renderChart(filteredData);
        }

        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data</td></tr>';
            } else {
                tbody.innerHTML = data.map(d => `
                    <tr>
                        <td>${d.tanggal}</td>
                        <td>${d.lokasi}</td>
                        <td>${d.jam}</td>
                        <td>${d.tinggi} cm</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                data-id="${d.id}"
                                data-tanggal="${d.tanggal}"
                                data-lokasi="${d.lokasi}"
                                data-jam="${d.jam}"
                                data-tinggi="${d.tinggi}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger hapus-btn" data-id="${d.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }
            document.getElementById('dataCount').textContent = `${data.length} record`;
            attachEvents();
        }

        function renderChart(data) {
            const labels = [...new Set(data.map(d => `${d.tanggal} ${d.jam}`))].sort();

            // Siapkan array untuk dua dataset
            const sriData = [];
            const mbData = [];

            labels.forEach(label => {
                const [tanggal, jam] = label.split(' ');
                const sriEntry = data.find(d => d.tanggal === tanggal && d.jam === jam && d.lokasi === 'PT. SRI');
                const mbEntry = data.find(d => d.tanggal === tanggal && d.jam === jam && d.lokasi === 'PT. MB');

                sriData.push(sriEntry ? sriEntry.tinggi : null); // null agar grafik putus
                mbData.push(mbEntry ? mbEntry.tinggi : null);
            });

            waterChart.data.labels = labels;

            // Tampilkan dua dataset hanya jika filter "Semua", selain itu hanya satu dataset
            if (currentLokasi === 'all') {
                waterChart.data.datasets[0].hidden = false;
                waterChart.data.datasets[1].hidden = false;
                waterChart.data.datasets[0].label = 'PT. SRI';
                waterChart.data.datasets[1].label = 'PT. MB';
                waterChart.data.datasets[0].data = sriData;
                waterChart.data.datasets[1].data = mbData;
            } else {
                // Sembunyikan dataset yang tidak sesuai
                if (currentLokasi === 'PT. SRI') {
                    waterChart.data.datasets[0].hidden = false;
                    waterChart.data.datasets[1].hidden = true;
                    waterChart.data.datasets[0].label = 'PT. SRI';
                    waterChart.data.datasets[0].data = data.map(d => d.tinggi);
                } else if (currentLokasi === 'PT. MB') {
                    waterChart.data.datasets[0].hidden = true;
                    waterChart.data.datasets[1].hidden = false;
                    waterChart.data.datasets[1].label = 'PT. MB';
                    waterChart.data.datasets[1].data = data.map(d => d.tinggi);
                }
            }

            waterChart.update();
        }

        // Event listener untuk dropdown filter
        document.getElementById('filterLokasi').addEventListener('change', function() {
            currentLokasi = this.value;
            applyFilterAndRender();
        });

        // Pasang event pada tombol edit & hapus
        function attachEvents() {
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.onclick = function() {
                    const id = this.dataset.id;
                    document.getElementById('editId').value = id;
                    document.getElementById('editLokasi').value = this.dataset.lokasi;
                    document.getElementById('editTanggal').value = this.dataset.tanggal;
                    document.getElementById('editJam').value = this.dataset.jam;
                    document.getElementById('editTinggi').value = this.dataset.tinggi;
                    new bootstrap.Modal(document.getElementById('editModal')).show();
                };
            });

            document.querySelectorAll('.hapus-btn').forEach(btn => {
                btn.onclick = function() {
                    const id = this.dataset.id;
                    if (!confirm('Yakin ingin menghapus data ini?')) return;
                    fetch(`/feature/water-level/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) fetchData();
                    });
                };
            });
        }

        // Submit form tambah
        document.getElementById('waterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/feature/water-level', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    this.reset();
                    fetchData();
                } else {
                    alert('Gagal menyimpan');
                }
            });
        });

        // Submit form edit (modal)
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/feature/water-level/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                    fetchData();
                } else {
                    alert('Gagal mengupdate');
                }
            } catch (err) {
                console.error('Error update:', err);
                alert('Terjadi kesalahan. Lihat konsol.');
            }
        });

        // Export grafik menjadi gambar
        document.getElementById('btnExportGrafik').addEventListener('click', function() {
            html2canvas(document.getElementById('captureArea'), {
                scale: 2,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'grafik-tma.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // Inisialisasi
        fetchData();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection