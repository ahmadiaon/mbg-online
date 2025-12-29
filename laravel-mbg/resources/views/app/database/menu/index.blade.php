@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card-box pb-10">

                <div class="row pd-20">
                    <div class="col-auto">
                        <h4 class="text-blue h4">Manage Group Form </h4>
                    </div>
                    <div class="col text-right">
                        <button class="btn btn-primary" onclick="openModal('group-form')" type="button">Tambah </button>

                    </div>
                </div>

                <div class="mb-20" id="datatable-group-form-wrapper">
                    <table id="datatable-group-form" class="display" style="width:100%">
                        <thead>
                            <tr id="header_table">
                                <th data-orderable="false" class="no-sort">URUTAN <div class="float-end"> <a
                                            class="btn btn-sm btn-outline-primary" href="#"
                                            onclick="filterDatatable()"> <i class="icon-copy bi bi-funnel float-end"></i>
                                        </a> <a onclick="orderTable(1)" class="btn btn-sm btn-outline-primary"
                                            href="#"> <i class="bi bi-filter"></i></a></div>
                                </th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    {{-- modal group form --}}
    <div class="modal fade" id="modal-group-form" tabindex="-1" role="dialog" aria-labelledby="header-modal-group-form">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="header-modal-group-form">
                        Group Form
                    </h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    <form action="/api/database/menu/storedatadatatable" enctype="multipart/form-data" id="form-group-form">
                        <div class="form-floating mb-3">
                            <input type="text" name="uuid" id="field-group-form-uuid"
                                class="form-control uuid_code_data" value="TIGA-BELAS" placeholder="1">
                            <label for="field-group-form-uuid">Code Data</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="number_sort" id="field-group-form-number_sort" class="form-control"
                                placeholder="1">
                            <label for="field-group-form-number_sort">Urutan Menu</label>
                        </div>
                        <div class="form-floating">
                            <input type="text" name="description" id="field-group-form-description" class="form-control"
                                placeholder="Database">
                            <label for="field-group-form-description">Deskripsi Menu</label>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="store-group-form" onclick="storeDatatable('group-form')"
                        class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal group form --}}
@endsection()

@section('js_code')
    <script>
        function dataShow(id_code_data) {
            let item = data_datatable_global.find(item => item.id === id_code_data);
            for (let key in item) {
                if (item.hasOwnProperty(key)) {
                    let inputField = $(`#field-group-form-${key}`);

                    // Jika field ditemukan, isi dengan item
                    if (inputField.length) {
                        inputField.val(item[key]);
                    }
                }
            }

            $('#modal-group-form').modal('show');
        }

        function dataDelete(id_code_data) {
            DELETE_ID_CODE_DATA = id_code_data;
            confirmDeleteData();
        }



        function getDatadataTable() {
            $.ajax({
                url: '/api/database/menu/getdatadatatable',
                type: "POST",
                data: {
                    table_name: 'group_forms'
                },
                success: function(response) {
                    let dataDatatable = response.data;
                    console.log("dataDatatable", dataDatatable);
                    data_datatable_global = dataDatatable;

                    let templateDataTable = {
                        code_table: "group-form",
                        parent_table: null,
                        primary_table: "number_sort",
                        menu_table: "STAND-ALONE",
                        description_table: "Menu Table",
                        fields: {
                            'number_sort': {
                                sort_field: '1',
                                code_field: 'number_sort',
                                description_field: 'Urutan',
                                visibility_data_field: 'show',
                                type_data_field: 'TEXT',
                            },
                            'description': {
                                sort_field: '1',
                                code_field: 'description',
                                description_field: 'Description',
                                visibility_data_field: 'show',
                                type_data_field: 'TEXT',
                            },
                            'id': {
                                sort_field: '2',
                                code_field: 'id',
                                description_field: 'ID Data',
                                visibility_data_field: 'show',
                                type_data_field: 'TEXT',
                            },
                            'uuid': {
                                sort_field: '3',
                                code_field: 'uuid',
                                description_field: 'UUID',
                                visibility_data_field: 'block',
                                type_data_field: 'TEXT',
                            },
                        },
                        data: data_datatable_global
                    }

                    GROUP_DATA = 'database_tables';
                    let dataArrParameter = {
                        tableId: 'group-form',
                        tableDataDetails: templateDataTable,
                        datasetTable: null,
                        paggingDatatable: true,
                        staticName: null,
                        isDeleteAction:false,
                    };
                    initDataTable(dataArrParameter);

                    // writeDatatable(dataDatatable);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function openModal(formCode) {
            $('#modal-' + formCode).modal('show');
        }

        function storeDatatable(formCode) {
            let isValid = true;

            // ini harusnya dari deskripsi field
            const requiredFields = [
                'number_sort', 'description'
            ];

            requiredFields.forEach(fieldId => {
                let formulaFindElement = 'field-' + formCode + '-' + fieldId;
                const field = document.getElementById(formulaFindElement);
                if (field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        $('#' + formulaFindElement).addClass('is-invalid');
                    } else {
                        $('#' + formulaFindElement).removeClass('is-invalid');
                    }
                }
            });

            // store data 
            let _url = $('#form-' + formCode).attr('action');
            var form = $('#form-' + formCode)[0];
            var form_data = new FormData(form);

            $.ajax({
                url: _url,
                type: "POST",
                headers: {
                    "authorization": "ahmaditoken1234"
                },
                processData: false, // Mencegah jQuery memproses FormData
                contentType: false, // Membiarkan browser mengatur Content-Type otomatis
                data: form_data,
                success: function(response) {
                    console.log("Data berhasil dikirim:", response);
                    showAlertSuccess();


                    if (response.data.status_data == 'created') {
                        table.row.add(response.data.data_complite).draw(false);
                        console.log("Data before ");
                        console.log(data_datatable_global);
                        data_datatable_global.push(response.data.data_complite);
                        console.log("Data update ");
                        console.log(data_datatable_global);
                    } else {
                        // update data_datatable_global 
                        console.log("Data before update ");
                        console.log(data_datatable_global);
                        let item = data_datatable_global.find(item => item.id === response.data.data_complite
                            .id);
                        if (item) {
                            item = response.data.data_complite;
                        }

                        console.log("Data update ");
                        console.log(data_datatable_global);


                        var row = table.rows().data().toArray().find(r => r.id == response.data.data_complite
                            .id);
                        if (row) {
                            // Update data
                            table = $('#datatable-Group-Form').DataTable();
                            let updatedData = response.data.data_complite; // Data yang baru dari response
                            let id_code_data = updatedData.id; // Ambil ID dari response

                            // Cari index baris berdasarkan ID
                            let rowIndex = table.rows().indexes().filter(function(index) {
                                return table.row(index).data().id === id_code_data;
                            });

                            // Jika ditemukan, update seluruh datanya
                            if (rowIndex.length > 0) {
                                table.row(rowIndex[0]).data(updatedData).draw(false); // Update semua kolom
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }
    </script>





    <script>
        $(document).ready(function() {
            $('.body-content').show();
            $('.loading').hide();
        });
        readyDB(function(db) {
            getDatadataTable();
        });
    </script>
@endsection()
