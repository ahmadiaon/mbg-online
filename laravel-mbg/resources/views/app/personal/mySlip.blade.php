@extends('app.layout.main')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Judul -->
                <div class="text-center mb-4">
                    <i class="bi bi-file-earmark-pdf text-danger display-4"></i>
                    <h3 class="fw-bold mt-2">Slip Gaji Karyawan</h3>
                    <p class="text-muted">Pilih periode untuk melihat slip</p>
                </div>

                <!-- Pilihan Tahun -->
                <div class="card shadow-sm rounded-4 mb-3 border-0">
                    <div class="card-body text-center">
                        <label class="form-label fw-bold mb-2">
                            <i class="bi bi-calendar-year me-1"></i>Pilih Tahun
                        </label> <br>
                        <div class="btn-group flex-wrap gap-1" role="group" id="yearGroup"></div>
                    </div>
                </div>

                <!-- Pilihan Bulan -->
                <div class="card shadow-sm rounded-4 mb-4 border-0">
                    <div class="card-body text-center">
                        <label class="form-label fw-bold mb-2">
                            <i class="bi bi-calendar-month me-1"></i>Pilih Bulan
                        </label>
                        <div class="btn-group flex-wrap gap-1" role="group" id="monthGroup"></div>
                    </div>
                </div>

                <!-- Tombol Lihat Slip -->
                <div class="d-grid mb-4">
                    <button class="btn btn-primary btn-lg rounded-pill" id="btnViewSlip">
                        <i class="bi bi-search me-2"></i>Lihat Slip
                    </button>
                </div>

                <!-- Area Preview Gambar (hasil render PDF) -->
                <div id="slipResult" class="card shadow-sm rounded-4 border-0 d-none">
                    <div
                        class="card-header bg-white d-flex justify-content-between align-items-center border-0 rounded-top-4">
                        <span class="fw-bold"><i class="bi bi-file-earmark-image text-danger me-1"></i>Slip Gaji</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" id="btnZoomIn" title="Perbesar"><i
                                    class="bi bi-zoom-in"></i></button>
                            <button class="btn btn-outline-secondary" id="btnZoomOut" title="Perkecil"><i
                                    class="bi bi-zoom-out"></i></button>
                            <button class="btn btn-outline-secondary" id="btnFitWidth" title="Sesuaikan Lebar"><i
                                    class="bi bi-arrows-fullscreen"></i></button>
                            <button class="btn btn-outline-secondary" id="btnDownload" title="Unduh Slip"><i
                                    class="bi bi-download"></i></button>
                        </div>
                    </div>
                    <div class="card-body bg-light rounded-bottom-4" id="slip-preview"
                        style="overflow:auto; -webkit-overflow-scrolling:touch;">
                        <!-- Canvas akan dimasukkan di sini -->
                    </div>
                </div>

                <!-- Pesan Error -->
                <div id="errorMessage" class="alert alert-danger d-none rounded-4 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <span id="errorText"></span>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js"></script>
    {{-- 
    <script>
        readyDB(function(db) {

            // DATA TABLE
            let templateDataTable = {
                code_table: "slip",
                parent_table: null,
                primary_table: "code_file",
                menu_table: "STAND-ALONE",
                description_table: "Slip Payroll",
                fields: {
                    'nrp': {
                        sort_field: '0',
                        code_field: 'nrp',
                        description_field: 'NRP',
                        visibility_data_field: 'hidden',
                        type_data_field: 'DARI-TABEL',
                    },
                    'code_file': {
                        sort_field: '1',
                        code_field: 'code_file',
                        description_field: 'File Code',
                        visibility_data_field: 'hidden',
                        type_data_field: 'TEXT',
                    },
                    'year': {
                        sort_field: '2',
                        code_field: 'year',
                        description_field: 'Tahun',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'month': {
                        sort_field: '5',
                        code_field: 'month',
                        description_field: 'Bulan',
                        visibility_data_field: 'show',
                        type_data_field: 'MONTH',
                    },
                }
            }

            let arrParameter = {
                tableId: 'slips',
                tableDataDetails: {},
                datasetTable: null,
                paggingDatatable: false,
                staticName: null,
                isDeleteAction: false
            };

            GROUP_DATA = 'database_tables';
            $.ajax({
                url: '/api/database/menu/getdatadatatable',
                type: "POST",
                data: {
                    table_name: 'slips',
                    nrp: toUUID(db['FILTER_APP']['PROFILE']['NRP']['value_data'])
                },
                success: function(response) {
                    // let dataDatatable = response.data;
                    templateDataTable['data'] = response.data;
                    arrParameter['tableDataDetails'] = templateDataTable;
                    conLog('response getdatadatatable', arrParameter);
                    initDataTable(arrParameter);
                },
                error: function(response) {
                    console.log(response);
                }
            });



        });

        


        function dataShow(tableId, code_data) {
            console.log("======== FUNCTION   dataShow : " + tableId, code_data);
            code_data = toUUID(code_data);
            let dataRow = db['database_tables'][tableId]['data'][code_data];
            console.log("dataRow", dataRow);
            code_file = dataRow;
            showdoc(dataRow['original_file']);
            goToPreview();

            // checkSlip(dataRow['original_file']);

        }
    </script>

    <script>
        const APP_URL = "{{ url('/') }}";
        let currentPdfPage = null;
        let code_file = '';

        /* =========================
         * CEK FILE ADA / TIDAK
         * ========================= */
        async function checkSlip(filename) {
            filename = filename.replace(/\.pdf$/i, '');

            try {
                const res = await fetch(`${APP_URL}/slip/${filename}`, {
                    method: 'HEAD'
                });
                return res.ok;
            } catch (e) {
                console.error('checkSlip error:', e);
                return false;
            }
        }

        /* =========================
         * TAMPILKAN PDF KE CARD
         * ========================= */
        async function showdoc(filename) {
            goToPreview();
            filename = filename.replace(/\.pdf$/i, '');
            const pdfUrl = `${APP_URL}/slip/${filename}`;

            const container = document.getElementById('slip-preview');

            if (!container) {
                console.error('slip-preview NOT FOUND');
                return;
            }

            // 🔒 PASTIKAN wrapper ADA
            let wrapper = document.getElementById('pdf-zoom-wrapper');
            if (!wrapper) {
                container.innerHTML = `
            <div id="pdf-zoom-wrapper"
                style="transform-origin:0 0; width:fit-content;">
                <span class="text-muted">Loading PDF...</span>
            </div>
        `;
                wrapper = document.getElementById('pdf-zoom-wrapper');
            } else {
                wrapper.innerHTML = '<span class="text-muted">Loading PDF...</span>';
            }

            const exists = await checkSlip(filename);
            if (!exists) {
                wrapper.innerHTML =
                    '<span class="text-danger">❌ File PDF tidak ditemukan</span>';
                return;
            }

            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                const pdf = await loadingTask.promise;

                currentPdfPage = await pdf.getPage(1);

                renderPageFit();

                document.getElementById('btn-fit').hidden = false;

            } catch (err) {
                console.error('PDF ERROR:', err);
                wrapper.innerHTML =
                    '<span class="text-danger">❌ Gagal membuka PDF</span>';
            }
        }

        function goToPreview() {
            const section = document.getElementById('section-preview-slip');
            if (!section) return;

            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }



        /* =========================
         * RENDER PDF SESUAI LEBAR CARD
         * ========================= */
        function renderPageFit() {
            if (!currentPdfPage) return;

            const container = document.getElementById('slip-preview');
            if (!container) return;

            // reset container
            container.innerHTML = '';

            // matikan flex dari inline HTML (WAJIB)
            container.style.display = 'block';
            container.style.alignItems = 'unset';
            container.style.justifyContent = 'unset';

            // biar bisa geser di HP
            container.style.overflow = 'auto';
            container.style.webkitOverflowScrolling = 'touch';

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            const containerWidth = container.clientWidth;

            // viewport asli
            const viewport = currentPdfPage.getViewport({
                scale: 1
            });

            // scale AWAL (pas lebar, tapi masih bisa di-zoom)
            const scale = containerWidth / viewport.width;

            // anti blur (retina / iPhone)
            const dpr = window.devicePixelRatio || 1;

            const scaledViewport = currentPdfPage.getViewport({
                scale: scale * dpr
            });

            // ukuran render asli
            canvas.width = scaledViewport.width;
            canvas.height = scaledViewport.height;

            // ukuran tampilan (penting!)
            canvas.style.width = (scaledViewport.width / dpr) + 'px';
            canvas.style.height = (scaledViewport.height / dpr) + 'px';

            // biar natural di HP
            canvas.style.display = 'block';
            canvas.style.touchAction = 'manipulation'; // IZINKAN PINCH ZOOM

            container.appendChild(canvas);

            currentPdfPage.render({
                canvasContext: ctx,
                viewport: scaledViewport
            });
        }





        /* =========================
         * BUTTON FIT WIDTH
         * ========================= */
        function fitWidth() {
            renderPageFit();
        }

        /* =========================
         * BUTTON DOWNLOAD
         * ========================= */

        function downloadSlip() {
            let originalFile = code_file['original_file'].replace(/\.pdf$/i, '');
            let downloadName = code_file['code_file'];

            const url = `${APP_URL}/slip-download/${originalFile}/${encodeURIComponent(downloadName)}`;

            // Safari / iOS friendly
            window.location.href = url;
        }
    </script>
 --}}

    <script>
        // JS Get Data
        const masterData = new MasterData();
        let arrDataSlip = {};


        (async function() {
            try {

                const data = await masterData.getDatadataTable('slips', 'nrp', db['FILTER_APP']['PROFILE']['NRP']['value_data']);
                const {
                    years,
                    monthsByYear
                } = processSlipData(data);

                data.forEach(slips => {
                    arrDataSlip[slips.code_file] = slips;
                });
                conLog('data return', data);
                conLog('processedData', monthsByYear);
                years.forEach(year => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-primary rounded-pill' + (year === selectedYear ?
                        ' active' : '');
                    btn.textContent = year;
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('#yearGroup .btn').forEach(b => b.classList
                            .remove('active'));
                        btn.classList.add('active');
                        selectedYear = year;
                        // Render ulang bulan sesuai tahun yang dipilih
                        renderMonths(monthsByYear[year]);
                    });
                    yearGroup.appendChild(btn);
                });
                // Set default ke tahun terbaru (indeks pertama)
                if (years.length > 0) {
                    selectedYear = years[0];
                    renderMonths(monthsByYear[selectedYear]);
                }
            } catch (error) {
                console.error('Gagal mengambil data:', error);
            }
        })();
    </script>

    <script>
        // JS Process this page
        function processSlipData(data) {
            const monthsByYear = {};

            // Kumpulkan bulan per tahun
            data.forEach(item => {
                const year = item.year;
                const month = item.month;

                if (!monthsByYear[year]) {
                    monthsByYear[year] = [];
                }

                if (!monthsByYear[year].includes(month)) {
                    monthsByYear[year].push(month);
                }
            });

            // Urutkan bulan di setiap tahun (ascending)
            Object.keys(monthsByYear).forEach(year => {
                monthsByYear[year].sort((a, b) => a - b);
            });

            // Dapatkan array tahun unik (terurut menurun)
            const years = Object.keys(monthsByYear)
                .map(Number)
                .sort((a, b) => b - a);

            return {
                years, // [2026, 2025, 2024, 2023]
                monthsByYear // {2023: [10,11,12], 2024: [1,2,...,12], ...}
            };
        }

        function renderMonths(months) {
            monthGroup.innerHTML = '';
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            months.forEach(month => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary rounded-pill' + (month === selectedMonth ? ' active' :
                    '');
                btn.textContent = monthNames[month - 1];
                btn.addEventListener('click', () => {
                    document.querySelectorAll('#monthGroup .btn').forEach(b => b.classList.remove(
                        'active'));
                    btn.classList.add('active');
                    selectedMonth = month;
                });
                monthGroup.appendChild(btn);
            });
        }
    </script>

    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
        // Elemen DOM
        const yearGroup = document.getElementById('yearGroup');
        const monthGroup = document.getElementById('monthGroup');
        const btnViewSlip = document.getElementById('btnViewSlip');
        const slipResult = document.getElementById('slipResult');
        const slipPreview = document.getElementById('slip-preview');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const btnZoomIn = document.getElementById('btnZoomIn');
        const btnZoomOut = document.getElementById('btnZoomOut');
        const btnFitWidth = document.getElementById('btnFitWidth');
        const btnDownload = document.getElementById('btnDownload');

        // Data tahun & bulan
        const currentYear = new Date().getFullYear();
        const years = Array.from({
            length: 6
        }, (_, i) => currentYear - i);
        const months_element = [{
                value: 1,
                label: 'Jan'
            }, {
                value: 2,
                label: 'Feb'
            }, {
                value: 3,
                label: 'Mar'
            },
            {
                value: 4,
                label: 'Apr'
            }, {
                value: 5,
                label: 'Mei'
            }, {
                value: 6,
                label: 'Jun'
            },
            {
                value: 7,
                label: 'Jul'
            }, {
                value: 8,
                label: 'Agu'
            }, {
                value: 9,
                label: 'Sep'
            },
            {
                value: 10,
                label: 'Okt'
            }, {
                value: 11,
                label: 'Nov'
            }, {
                value: 12,
                label: 'Des'
            }
        ];

        let selectedYear = currentYear;
        let selectedMonth = new Date().getMonth() + 1;
        let currentPdfDoc = null;
        let currentScale = 1.5;
        let currentPageNum = 1;
        let currentOriginalFile = null; // menyimpan nama file asli (UUID tanpa ekstensi)

      

        // Render halaman PDF ke canvas
        async function renderPage(pageNum) {
            if (!currentPdfDoc) return;
            const page = await currentPdfDoc.getPage(pageNum);
            const viewport = page.getViewport({
                scale: currentScale
            });

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';
            canvas.style.display = 'block';
            canvas.style.touchAction = 'manipulation';

            slipPreview.innerHTML = '';
            slipPreview.appendChild(canvas);

            await page.render({
                canvasContext: ctx,
                viewport
            }).promise;
            currentPageNum = pageNum;
        }

        // Fit width
        async function renderFitWidth() {
            if (!currentPdfDoc) return;
            const page = await currentPdfDoc.getPage(currentPageNum);
            const containerWidth = slipPreview.clientWidth;
            const viewport = page.getViewport({
                scale: 1
            });
            currentScale = containerWidth / viewport.width;
            await renderPage(currentPageNum);
        }

        // Muat PDF dari URL, simpan originalFile
        async function loadPDF(url) {
            try {
                currentPdfDoc = await pdfjsLib.getDocument(url).promise;
                // Ekstrak nama file asli (UUID) dari URL
                const parts = url.split('/');
                const filename = parts[parts.length - 1]; // ffc11d3c-...pdf
                currentOriginalFile = filename.replace(/\.pdf$/i, ''); // UUID tanpa .pdf

                
                slipResult.classList.remove('d-none');
                await renderFitWidth();
            } catch (err) {
                console.error(err);
                errorText.textContent = 'Gagal memuat PDF. Pastikan file tersedia.';
                errorMessage.classList.remove('d-none');
            }
        }

        // Tombol Lihat Slip
        btnViewSlip.addEventListener('click', async () => {
            slipResult.classList.add('d-none');
            errorMessage.classList.add('d-none');
            currentPdfDoc = null;
            currentScale = 1.5;
            slipPreview.innerHTML =
                '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat slip...</p></div>';

            // URL file (relatif dari root web)
            const dummyUrl = '/file/slips/ffc11d3c-d8de-4c18-88d0-f8afaaa7dd75.pdf';
            // Untuk produksi: sesuaikan dengan endpoint Anda
            let dataSlip = arrDataSlip[`${db['FILTER_APP']['USER']['nrp']}-${selectedYear}-${selectedMonth}`]['original_file'];
            let url = `http://assets.mitrabaritogroup.com/uploads/slips/`+`${dataSlip}`;


            btnViewSlip.disabled = true;
            btnViewSlip.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...';

            await loadPDF(url);

            btnViewSlip.disabled = false;
            btnViewSlip.innerHTML = '<i class="bi bi-search me-2"></i>Lihat Slip';
        });

        // Kontrol Zoom
        btnZoomIn.addEventListener('click', async () => {
            if (!currentPdfDoc) return;
            currentScale += 0.25;
            await renderPage(currentPageNum);
        });
        btnZoomOut.addEventListener('click', async () => {
            if (!currentPdfDoc) return;
            if (currentScale > 0.5) {
                currentScale -= 0.25;
                await renderPage(currentPageNum);
            }
        });
        btnFitWidth.addEventListener('click', async () => {
            if (!currentPdfDoc) return;
            await renderFitWidth();
        });

        // === FUNGSI DOWNLOAD ===
        btnDownload.addEventListener('click', (e) => {
            e.preventDefault();
            if (!currentOriginalFile) {
                alert('Tidak ada file yang dimuat.');
                return;
            }
            // Nama file yang akan terdownload
            const downloadName = `SLIP-${selectedYear}-${String(selectedMonth).padStart(2, '0')}.pdf`;
            // Endpoint download (sesuaikan dengan route Anda)
            const url = `/slip-download/${currentOriginalFile}/${encodeURIComponent(downloadName)}`;
            // Redirect ke URL download
            window.location.href = url;
        });
    </script>
@endsection()
