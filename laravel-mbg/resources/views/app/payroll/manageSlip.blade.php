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
        readyDB(function(db) {

            // DATA TABLE
            let templateDataTable = {
                code_table: "slip",
                parent_table: null,
                primary_table: "nrp",
                menu_table: "STAND-ALONE",
                description_table: "Slip Payroll",
                fields: {
                    'nrp': {
                        sort_field: '0',
                        code_field: 'nrp',
                        description_field: 'NRP',
                        visibility_data_field: 'show',
                        type_data_field: 'DARI-TABEL',
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

            let arrParameter = {
                tableId: 'slips',
                tableDataDetails: {},
                datasetTable: null,
                paggingDatatable: true,
                staticName: null,
                isDeleteAction:false,
            };

            GROUP_DATA = 'database_tables';
            $.ajax({
                url: '/api/database/menu/getdatadatatable',
                type: "POST",
                data: {
                    table_name: 'slips'
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
            console.log("======== FUNCTION   dataShow", code_data);
            code_data = toUUID(code_data);
            if (tableId == 'users') {
                let data_KARYAWAN = db['database_tables']['KARYAWAN']['join_data'][
                    toUUID(code_data)
                ];
                $('.project-title').text(`${data_KARYAWAN['PROJECT']?.['text_data'] ?? ""} |
                                                            ${data_KARYAWAN['DEPARTEMEN']?.['text_data'] ?? ""} |
                                                            ${data_KARYAWAN['DIVISI']?.['text_data'] ?? ""}`);
                $('.NAMA-KARYAWAN-title').text(data_KARYAWAN['NAMA-KARYAWAN']?.['text_data'] ?? "");
                $('.NRP-title').text(data_KARYAWAN['NRP']?.['text_data'] ?? "");
                $('#NRP').val(data_KARYAWAN['NRP']?.['text_data'] ?? "");
                $('.JABATAN-title').text(data_KARYAWAN['JABATAN']?.['text_data'] ?? "");
                $('.PERUSAHAAN-title').text(data_KARYAWAN['PERUSAHAAN']?.['text_data'] ?? "");
                $('#nik_ktp').val(data_KARYAWAN['NIK-KTP']?.['text_data'] ?? "");

                $('#datashow-users-modal').modal('show');
            }
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
