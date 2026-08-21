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
    

    <script>
        const masterData = new MasterData();

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
            let originalFile = record.original_file?.value_data || record.original_file;
            if (!originalFile) {
                alert('File slip tidak tersedia.');
                return;
            }

            // Hapus ekstensi .pdf jika ada
            originalFile = originalFile.replace(/\.pdf$/i, '');
            const fileUrl = `https://assets.mitrabaritogroup.com/uploads/slips/${originalFile}.pdf`;
            conLog('fileUrl', fileUrl);

            // Tampilkan modal PDF dengan parameter originalFile
            showPdfModal(fileUrl, `Slip-${code_data}.pdf`, originalFile);
        }
    </script>

    <script>
        async function uploadFiles() {
            var fileInput = document.getElementById('fileInput');
            var files = fileInput.files;
            var maxSize = 4 * 1024 * 1024; // 4 MB per file (sesuaikan)
            $('#successMessage').hide();
            startLoading();

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var formData = new FormData();
                var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
                formData.append('_token', csrfToken);
                formData.append('file[]', file);
                formData.append('month-year', $(`#month-year`).val());
                await $.ajax({
                    url: '/payroll/slip',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        let slips = response.data;
                        conLog('upload success', slips);
                    },
                    contentType: false,
                    processData: false,
                });
            }
            $('#successMessage').show();
            stopLoading();
        }
    </script>
@endsection