@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-md-8 mb-20">
            <div class="card-box pb-10">

                <div class="row pd-20">
                    <div class="col-auto">
                        <h4 class="text-blue h4">Manage SLIP</h4>
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
        <div class="col-md-4 col-sm-12">
            <form id="fileForm" class="card-box pd-20" action="/web/manage/slip" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <h5 class="h5">Bulan / Tahun </h5>
                    <p>
                        bulan dan tahun upah yang di bayar
                    </p>
                    <input name="month-year" id="month-year" class="form-control month-picker" placeholder="Select Month"
                        type="text">
                </div>
                <div class="form-group">
                    <label>File slip .pdf</label>
                    <input class="form-control" type="file" id="fileInput" name="file[]" multiple>
                </div>
                <div id="successMessage" style="display: none;">
                    <p>PDF files uploaded successfully.</p>
                </div>
                <button type="button" onclick="uploadFiles()" class="btn btn-primary" id="uploadBtn">Upload</button>
            </form>
        </div>
    </div>
@endsection()

@section('js_code')
    <script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.min.js"></script>

    <script>
        function showPdfModal(url, filename) {
            // Hapus modal sebelumnya jika ada
            const oldModal = document.getElementById('pdfModal');
            if (oldModal) oldModal.remove();

            const modalId = 'pdfModal-' + Date.now();
            const modalHtml = `
                    <div class="modal fade" id="${modalId}" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${filename || 'Preview PDF'}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body bg-light" style="overflow:auto; -webkit-overflow-scrolling:touch;">
                            <canvas id="pdfCanvas-${modalId}" style="display:block; margin:0 auto;"></canvas>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-group btn-group-sm me-auto">
                            <button class="btn btn-outline-secondary" id="zoomin-${modalId}" title="Perbesar"><i class="bi bi-zoom-in"></i></button>
                            <button class="btn btn-outline-secondary" id="zoomout-${modalId}" title="Perkecil"><i class="bi bi-zoom-out"></i></button>
                            <button class="btn btn-outline-secondary" id="fitwidth-${modalId}" title="Sesuaikan Lebar"><i class="bi bi-arrows-fullscreen"></i></button>
                            </div>
                            <a class="btn btn-outline-secondary" id="download-${modalId}" href="${url}" download="${filename}"><i class="bi bi-download"></i></a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                        </div>
                    </div>
                    </div>`;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modalEl = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            const canvas = document.getElementById(`pdfCanvas-${modalId}`);
            const btnZoomIn = document.getElementById(`zoomin-${modalId}`);
            const btnZoomOut = document.getElementById(`zoomout-${modalId}`);
            const btnFitWidth = document.getElementById(`fitwidth-${modalId}`);

            let pdfDoc = null;
            let currentPage = 1;
            let currentScale = 1.5;

            async function renderPage(pageNum) {
                if (!pdfDoc) return;
                const page = await pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({
                    scale: currentScale
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.style.width = viewport.width + 'px';
                canvas.style.height = viewport.height + 'px';
                const ctx = canvas.getContext('2d');
                await page.render({
                    canvasContext: ctx,
                    viewport
                }).promise;
            }

            async function loadPDF(pdfUrl) {
                try {
                    pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                    // Fit width awal
                    const page = await pdfDoc.getPage(1);
                    const containerWidth = canvas.parentElement.clientWidth;
                    const viewport = page.getViewport({
                        scale: 1
                    });
                    currentScale = containerWidth / viewport.width;
                    await renderPage(1);
                } catch (err) {
                    console.error(err);
                    alert('Gagal memuat PDF.');
                    modal.hide();
                }
            }

            btnZoomIn.addEventListener('click', async () => {
                if (!pdfDoc) return;
                currentScale += 0.25;
                await renderPage(currentPage);
            });

            btnZoomOut.addEventListener('click', async () => {
                if (!pdfDoc) return;
                if (currentScale > 0.5) {
                    currentScale -= 0.25;
                    await renderPage(currentPage);
                }
            });

            btnFitWidth.addEventListener('click', async () => {
                if (!pdfDoc) return;
                const page = await pdfDoc.getPage(currentPage);
                const containerWidth = canvas.parentElement.clientWidth;
                const viewport = page.getViewport({
                    scale: 1
                });
                currentScale = containerWidth / viewport.width;
                await renderPage(currentPage);
            });

            modalEl.addEventListener('hidden.bs.modal', () => {
                modalEl.remove();
                pdfDoc = null;
            });

            loadPDF(url);
        }
    </script>
    <script>
        const masterData = new MasterData();
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js';

        let arrParameter = {
            tableId: 'slips',
            tableDataDetails: {},
            datasetTable: null,
            paggingDatatable: true,
            staticName: null,
            isDeleteAction: false,
        };
        (async function() {
            try {
                const result = await CacheManager.getCache('db_role_4', null, null);
                console.log('Cache retrieved:', result);

                // Pastikan cache memiliki data yang valid
                if (!result?.data?.cached) {
                    console.error('Cache kosong atau tidak valid');
                    return; // hentikan eksekusi
                }

                // Perbarui variabel global db
                db = result.data.cached;

                const data = await masterData.getDatadataTable('slips', null, null);
                console.log('data slips', data);

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
                            visibility_data_field: 'show',
                            type_data_field: 'DARI-TABEL',
                            data_source: {
                                "field_get_data_source": "NRP",
                                "code_data_source": "PERUSAHAAN-DIREKTUR",
                                "table_data_source": "KARYAWAN"
                            },
                        },
                        'code_file': {
                            sort_field: '1',
                            code_field: 'code_file',
                            description_field: 'File Code',
                            visibility_data_field: 'show',
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



                GROUP_DATA = 'database_tables';

                templateDataTable['data'] = data;
                arrParameter['tableDataDetails'] = templateDataTable;
                conLog('response getdatadatatable', arrParameter);

                // Sekarang panggil initDataTable
                initDataTable(arrParameter);
            } catch (error) {
                console.error('Gagal mengambil data:', error);
            }
        })();


        function dataShow(tableId, code_data, index) {
            console.log("======== FUNCTION dataShow", code_data);
            console.log('db', tableId, code_data);
            console.log('arrParameter', arrParameter);
            console.log('Index', index);
            conLog('arrParameter.tableDataDetails', arrParameter.tableDataDetails.data[index]);

            // Pastikan arrParameter ada (global dari initDataTable)
            if (!arrParameter || !arrParameter.tableDataDetails || !arrParameter.tableDataDetails.data) {
                alert('Data tidak tersedia.');
                return;
            }

            const dataSource = arrParameter.tableDataDetails.data;
            let record = null;

            record = dataSource[index]; // Ambil record berdasarkan index


            if (!record) {
                alert('Data tidak ditemukan.');
                return;
            }

            // Ambil nama file asli dari record
            // Sesuaikan dengan struktur data: bisa record.original_file?.value_data atau record.original_file
            let originalFile = record.original_file?.value_data || record.original_file;
            if (!originalFile) {
                alert('File slip tidak tersedia.');
                return;
            }

            // Hapus ekstensi .pdf jika ada (untuk memastikan path bersih)
            originalFile = originalFile.replace(/\.pdf$/i, '');
            const fileUrl = `http://assets.mitrabaritogroup.com/uploads/slips/${originalFile}.pdf`;
            conLog('fileUrl', fileUrl);
            // Tampilkan modal PDF
            showPdfModal(fileUrl, `Slip-${code_data}.pdf`);
        }

        async function editStoreUser() {
            startLoading();
            let _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/api/user/update-user',
                type: "POST",
                async: false,
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    nrp: $(`#NRP`).val(),
                    nik_ktp: $(`#nik_ktp`).val()
                },
                success: function(response) {
                    conLog('response', response)

                    stopLoading();
                    showModalSuccess();
                },
                error: function(response) {
                    conLog('error', response)
                    //alertModal()
                },
                done: function() {
                    stopLoading();
                }
            });
        }
    </script>

    <script>
        async function uploadFiles() {
            var fileInput = document.getElementById('fileInput');
            var files = fileInput.files;
            var maxSize = 4 * 1024 * 1024; // 20 MB
            var currentSize = 0;
            $('#successMessage').hide();
            startLoading();

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var formData = new FormData();
                var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
                formData.append('_token', csrfToken);
                formData.append('file[]', file);
                // formData.append('file', file);
                formData.append('month-year', $(`#month-year`).val());
                await $.ajax({
                    url: '/payroll/slip',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        let slips = response.data;
                        CL(i);
                    },
                    contentType: false,
                    processData: false,
                });
            }
            $('#successMessage').show();
            stopLoading();
        }
    </script>
@endsection()
