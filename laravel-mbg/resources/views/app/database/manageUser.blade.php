@extends('app.layout.main')
@section('src_css')
    <link rel="stylesheet" href="/assets/src/plugins/datatables/css/jquery.dataTables.min.css">
@endsection()
@section('content')
    <div class="row">
        <div class="col-md-10 mb-20">
            <div class="card-box pb-10">

                <div class="row pd-20">
                    <div class="col-auto">
                        <h4 class="text-blue h4">Manage Group Form </h4>
                    </div>
                </div>

                <div class="mb-20" id="datatable-users-wrapper">
                    <table id="datatable-users" class="display cell-border" style="width:100%">
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
    </div>




    <!-- Modal MANAGE Users -->
    <div class="modal fade" id="datashow-users-modal" tabindex="-1" aria-labelledby="datashow-users-modalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="datashow-users-modalLabel">Update User Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="name-avatar mb-20 d-flex align-items-center pr-2 pl-2 card-box w-100">
                            <div class="avatar mr-2 flex-shrink-0">
                                <img src="/assets/vendors/images/photo5.jpg" class="border-radius-100 box-shadow"
                                    width="50" height="50" alt="">
                            </div>

                            <div class="flex-grow-1" style="min-width:0;">
                                <!-- BADGE KANAN ATAS -->
                                <div class="d-flex justify-content-end mb-0">
                                    <span class="badge badge-pill badge-primary mb-0 lh-1 project-title">

                                    </span>
                                </div>

                                <!-- NAMA -->
                                <div class="font-14 weight-600 mb-0 lh-sm NAMA-KARYAWAN-title">

                                </div>

                                <!-- NRP -->
                                <div class="font-12 weight-500 mb-0 lh-sm">
                                    <span class="badge badge-pill badge-primary mb-0 lh-1 PERUSAHAAN-title">

                                    </span>
                                    <div class="NRP-title"></div>
                                </div>

                                <!-- JABATAN -->
                                <div class="font-12 weight-500 text-muted JABATAN-title mb-0 lh-sm">

                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-md-4">
                                <label for="">No KTP Baru</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="nik_ktp" id="nik_ktp" class="form-control"
                                    placeholder="No KTP baru">
                            </div>
                        </div>
                        <div class="row mb-20">
                            <div class="col-md-4">
                                <label for="">NRP</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="NRP" id="NRP" class="form-control" placeholder="NRP">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Level User</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="role" id="role" class="form-control"
                                    placeholder="Level User">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="editStoreUser()"
                        id="updateUserBtn">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal MANAGE Users --}}
@endsection()

@section('js_code')
    <script>
        readyDB(function(db) {

            // DATA TABLE



            let templateDataTable = {
                code_table: "users",
                parent_table: null,
                primary_table: "nrp",
                menu_table: "STAND-ALONE",
                description_table: "Users Account",
                fields: {
                    'nrp': {
                        sort_field: '0',
                        code_field: 'nrp',
                        description_field: 'NRP',
                        visibility_data_field: 'show',
                        data_source: {
                            "field_get_data_source": "NRP",
                            "code_data_source": "PERUSAHAAN-DIREKTUR",
                            "table_data_source": "KARYAWAN"
                        },
                        type_data_field: 'DARI-TABEL',
                    },
                    'role': {
                        sort_field: '1',
                        code_field: 'role',
                        description_field: 'Role',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'auth_login': {
                        sort_field: '2',
                        code_field: 'auth_login',
                        description_field: 'Auth Login',
                        visibility_data_field: 'filter',
                        type_data_field: 'TEXT',
                    },
                    'pin': {
                        sort_field: '5',
                        code_field: 'pin',
                        description_field: 'PIN',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'password': {
                        sort_field: '4',
                        code_field: 'password',
                        description_field: 'Password',
                        visibility_data_field: 'block',
                        type_data_field: 'TEXT',
                    }
                }
            }

            GROUP_DATA = 'database_tables';
            $.ajax({
                url: '/api/database/menu/getdatadatatable',
                type: "POST",
                data: {
                    table_name: 'users'
                },
                success: function(response) {
                    // let dataDatatable = response.data;
                    templateDataTable['data'] = response.data;
                    let arrParameter = {
                        tableId: 'users',
                        tableDataDetails: templateDataTable,
                        datasetTable: null,
                        paggingDatatable: true,
                        staticName: null,
                        isDeleteAction: false
                    };
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
@endsection()
