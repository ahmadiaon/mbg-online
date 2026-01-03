@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-md-12 mb-20">
            <div class="card-box pb-10">

                <div class="row pd-20">
                    <div class="col-auto">
                        <h4 class="text-blue h4">Manage Group Form </h4>
                    </div>
                </div>

                <div class="mb-20" id="datatable-recruitments-wrapper">
                    <table id="datatable-recruitments" class="display cell-border" style="width:100%">
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

    <div class="modal fade" id="small-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Tindak Lanjut Lamaran
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    <div class="profile-info">
                        <h5 class="mb-20 h5 text-blue name"></h5>
                        <ul>
                            <li>
                                <span>Email :</span>
                                <div class="email"></div>
                            </li>
                            <li class="">
                                <span>Phone Number:</span>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="phone_number"> </div>
                                    </div>
                                    <div class="col-4">
                                        <a hidden class="phone_number_wa" target="_blank"
                                            href="https://wa.me/621255897044"><i class="icon-copy bi bi-whatsapp "></i> </a>
                                    </div>
                                </div>

                            </li>
                            <li class="">
                                <span>Alamat:</span>
                                <div class="alamat">ahmadi@mail.com</div>
                            </li>
                            <li>
                                <span>Posisi</span>
                                <h5 class="mb-20 h5 text-blue posisi"></h5>
                            </li>
                        </ul>
                    </div>
                    <input type="hidden" name="id_recruitment" id="id_recruitment">
                </div>
                <div class="modal-footer text-center">
                    <div class="btn-list ">
                        <button class="btn  btn-outline-warning file_cv" onclick="openPdfNewTab('recruitments', '')">
                            <i class="icon-copy bi bi-file-earmark-pdf"></i> CV
                        </button>


                        <button onclick="updateRecruitment('Disimpan')" type="button" class="btn" data-bgcolor="#3b5998"
                            data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(59, 89, 152);">
                            <i class="icon-copy bi bi-file-earmark-check"></i> Simpan
                        </button>
                        <button type="button" onclick="updateRecruitment('Ditolak')" class="btn" data-bgcolor="#bd081c"
                            data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(189, 8, 28);">
                            <i class="icon-copy bi bi-file-earmark-excel"></i> Tolak
                        </button>
                        <button data-dismiss="modal" type="button" class="btn" data-bgcolor="#00b489"
                            data-color="#ffffff" style="color: rgb(255, 255, 255); background-color: rgb(0, 180, 137);">
                            <i class="icon-copy bi bi-x-lg"></i> close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="pdfModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview PDF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="pdfIframe" src="" width="100%" height="600px" style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection()


@section('js_code')
    <script>
        readyDB(function(db) {

            // DATA TABLE
            let templateDataTable = {
                code_table: "recruitments",
                parent_table: null,
                primary_table: "nik_ktp",
                menu_table: "STAND-ALONE",
                description_table: "Recruitment",
                fields: {
                    'nik_ktp': {
                        sort_field: '1',
                        code_field: 'nik_ktp',
                        description_field: 'NIK KTP',
                        visibility_data_field: 'show',
                        data_source: {
                            "field_get_data_source": "NRP",
                            "code_data_source": "PERUSAHAAN-DIREKTUR",
                            "table_data_source": "KARYAWAN"
                        },
                        type_data_field: 'DARI-TABEL',
                    },
                    'full_name': {
                        sort_field: '2',
                        code_field: 'full_name',
                        description_field: 'Full Name',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'address_description': {
                        sort_field: '3',
                        code_field: 'address_description',
                        description_field: 'Address Description',
                        visibility_data_field: 'hidden',
                        type_data_field: 'TEXT',
                    },
                    'provinsi': {
                        sort_field: '4',
                        code_field: 'provinsi',
                        description_field: 'Provinsi',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'kabupaten': {
                        sort_field: '5',
                        code_field: 'kabupaten',
                        description_field: 'Kabupaten',
                        visibility_data_field: 'hidden',
                        type_data_field: 'TEXT',
                    },
                    'kecamatan': {
                        sort_field: '6',
                        code_field: 'kecamatan',
                        description_field: 'Kecamatan',
                        visibility_data_field: 'hidden',
                        type_data_field: 'TEXT',
                    },
                    'position': {
                        sort_field: '7',
                        code_field: 'position',
                        description_field: 'Position',
                        visibility_data_field: 'show',
                        type_data_field: 'DARI-TABEL',
                        data_source: {
                            "field_get_data_source": "JABATAN",
                            "code_data_source": "recruitments-JABATAN",
                            "table_data_source": "JABATAN"
                        },
                    },
                    'file': {
                        sort_field: '8',
                        code_field: 'file',
                        description_field: 'File',
                        visibility_data_field: 'show',
                        type_data_field: 'PDF',
                    },
                    'email': {
                        sort_field: '9',
                        code_field: 'email',
                        description_field: 'Email',
                        visibility_data_field: 'hidden',
                        type_data_field: 'TEXT',
                    },
                    'phone_number': {
                        sort_field: '10',
                        code_field: 'phone_number',
                        description_field: 'Phone Number',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'status': {
                        sort_field: '11',
                        code_field: 'status',
                        description_field: 'Status',
                        visibility_data_field: 'show',
                        type_data_field: 'STATUS-RECRUITMENT',
                    },
                    'time_propose': {
                        sort_field: '0',
                        code_field: 'time_propose',
                        description_field: 'Time Propose',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    }
                }
            }

            GROUP_DATA = 'database_tables';
            $.ajax({
                url: '/api/database/menu/getdatadatatable',
                type: "POST",
                data: {
                    table_name: 'recruitments',
                },
                success: function(response) {
                    let dataDatatable = response.data;
                    conLog('dataDatatable', dataDatatable)
                    templateDataTable['data'] = response.data;
                    let arrParameter = {
                        tableId: 'recruitments',
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
            console.log("======== FUNCTION   " + tableId, code_data);

            console.log('code_data');
            console.log(code_data);
            let data_detail = db['database_tables'][tableId]['data'][code_data];
            $('.name').text(data_detail.full_name);
            $('#id_recruitment').val(data_detail.id);
            $('.email').text(data_detail.email);
            $('.phone_number').text(data_detail.phone_number);
            $('.alamat').text(
                `${data_detail.provinsi}, ${data_detail.kabupaten}, ${data_detail.kecamatan}, ${data_detail.address_description} `
            );
            $('.posisi').text(db['database_tables']['JABATAN']['data'][data_detail.position]['JABATAN']['value_data']);
            $(".file_cv").attr("onclick", `openPdfNewTab('recruitments', '${data_detail.file}')`);
            $(".phone_number_wa").attr("href", `https:wa.me/${data_detail.phone_number}`);
            $('#small-modal').modal('show');
        }

        function openPdf(folder, filename) {

            const iframe = document.getElementById('pdfIframe');
            const modalEl = document.getElementById('pdfModal');

            // set url PDF (tanpa .pdf)
            iframe.src = `/file/${folder}/${filename}`;

            // buka modal
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        function openPdfNewTab(folder, filename) {
            const url = `/file/${folder}/${filename}`;
            window.open(url, '_blank');
        }

        function updateRecruitment(val_status) {
            // $('#loading-modal').modal('show');
            $.ajax({
                url: '/api/recruitment/store',
                type: "POST",
                data: {
                    id_lamaran: $(`#id_recruitment`).val(),
                    val_lamaran: val_status
                },
                success: function(response) {
                    console.log('response');
                    console.log(response);
                    stopLoading()
                },
                error: function(response) {
                    console.log(response);
                }
            });
            stopLoading();
        }
    </script>
@endsection()
