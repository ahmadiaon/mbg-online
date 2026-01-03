@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12 mb-20">
            <div class="card-box pb-10">

                <div class="row pd-20">
                    <div class="col-auto">
                        <h4 class="text-blue h4">SLIP</h4>
                    </div>
                </div>

                <div class="mb-20" id="datatable-slips-wrapper">
                    <table id="datatable-slips" class="display cell-border" style="width:100%">
                        <thead>
                            <tr id="header_table">
                                <th>Field Data</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-12" id="section-preview-slip">
            <div class="card-box">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Preview Slip</h5>
                    <button id="btn-download" type="button" class="btn btn-primary btn-sm" onclick="downloadSlip()">
                        Download
                    </button>
                </div>
                <div id="slip-preview"
                    style="
                        width:100%;
                        min-height:400px;
                        border:1px solid #ddd;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#fafafa;
                    ">
                    <span class="text-muted">Belum ada slip dipilih</span>
                </div>

                <div class="mt-2 text-center">
                    <button id="btn-fit" class="btn btn-sm btn-secondary" hidden onclick="fitWidth()">
                        Fit Width
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="doc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Slip</h4>
                    <button type="button"class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body text-center">
                    <canvas id="pdf-canvas" class="d-block"></canvas>
                    <button id="changeWidth" onclick="changeWidth()" class="btn btn-secondary">lihat</button>
                    {{-- <div style="text-align: center;">
                        <iframe id="path_doc" src="http://192.168.8.135:8000/file/document/employee/01_ktp_file.pdf"
                            style="width:100%; height:500px;" frameborder="0"></iframe>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js"></script>
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
                        type_data_field: 'TEXT',
                    },
                }
            }

            let arrParameter = {
                tableId: 'slips',
                tableDataDetails: {},
                datasetTable: null,
                paggingDatatable: true,
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
@endsection()
