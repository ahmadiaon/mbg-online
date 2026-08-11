 @extends('app.layout.main')

 @section('src_css')
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <!-- Bootstrap Icons -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <!-- Chart.js -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
     <style>
         body {
             background: #f4f6f9;
         }

         .card {
             border-radius: 1rem;
             box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
         }

         .table-container {
             max-height: 400px;
             overflow-y: auto;
         }
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
                             <input type="number" step="0.1" class="form-control" id="inputTinggi" name="tinggi"
                                 placeholder="0.0" required>
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
                             <input name="tinggi" type="number" step="0.1" class="form-control" id="editTinggi"
                                 required>
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
                 datasets: [{
                     label: 'Tinggi Air (cm)',
                     data: [],
                     borderColor: '#0d6efd',
                     backgroundColor: 'rgba(13,110,253,0.1)',
                     fill: true,
                     tension: 0.3
                 }]
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
                     y: {
                         beginAtZero: true
                     }
                 }
             }
         });

         // Ambil data dari server
         function fetchData() {
             fetch('/feature/water-level/data')
                 .then(res => res.json())
                 .then(data => {
                     console.log('data :');
                     console.log(data);
                     const tbody = document.getElementById('tableBody');
                     if (data.length === 0) {
                         tbody.innerHTML =
                             '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada data</td></tr>';
                     } else {
                         tbody.innerHTML = data.map(d => `
                        <tr>
                            <td>${d.tanggal}</td>
                            <td>${d.jam}</td>
                            <td>${d.tinggi} cm</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${d.id}" data-tanggal="${d.tanggal}" data-jam="${d.jam}" data-tinggi="${d.tinggi}" data-lokasi="${d.lokasi}"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger hapus-btn" data-id="${d.id}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    `).join('');
                     }
                     document.getElementById('dataCount').textContent = `${data.length} record`;

                     // Grafik
                     waterChart.data.labels = data.map(d => d.tanggal);
                     waterChart.data.datasets[0].data = data.map(d => d.tinggi);
                     waterChart.update();

                     attachEvents();
                 });
         }

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
         // Submit form edit (modal)
         document.getElementById('editForm').addEventListener('submit', async function(e) {
             e.preventDefault();
             const id = document.getElementById('editId').value;
             const formData = new FormData(this);
             formData.append('_method', 'PUT');

             console.log('Mengirim update untuk ID:', id);
             // Debug: lihat isi form data
             for (let pair of formData.entries()) {
                 console.log(pair[0] + ': ' + pair[1]);
             }

             try {
                 const response = await fetch(`/feature/water-level/${id}`, {
                     method: 'POST',
                     headers: {
                         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                         'Accept': 'application/json',
                     },
                     body: formData
                 });

                 console.log('Status respons:', response.status);
                 const text = await response.text(); // ambil sebagai teks dulu
                 console.log('Respons mentah (teks):', text.substring(0, 200)); // tampilkan 200 karakter pertama

                 // Coba parsing JSON
                 let result;
                 try {
                     result = JSON.parse(text);
                 } catch (jsonError) {
                     console.error('Respons bukan JSON. Kemungkinan HTML error:');
                     console.error(text);
                     alert('Server mengembalikan error. Lihat konsol (F12) untuk detail.');
                     return;
                 }

                 if (result.success) {
                     bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                     fetchData();
                 } else {
                     alert('Gagal mengupdate: ' + (result.message || 'Kesalahan tidak diketahui'));
                 }
             } catch (err) {
                 console.error('Network error:', err);
                 alert('Gagal terhubung ke server. Periksa koneksi.');
             }
         });

         // Inisialisasi
         fetchData();
     </script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 @endsection
