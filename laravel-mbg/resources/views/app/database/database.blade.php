@extends('app.layout.main')
@section('src_css')
    <link rel="stylesheet" href="/assets/src/plugins/datatables/css/jquery.dataTables.min.css">
    <style>
        table {
            border-collapse: separate;
            table-layout: fixed;
        }

        table.dataTable thead tr th {
            overflow: visible !important;
        }

        table.dataTable {
            overflow: visible !important;
            position: relative;
        }

        .dropdown-menu {
            z-index: 2000 !important;
        }

        th,
        td {
            min-width: 120px;
            white-space: nowrap;
        }

        #datatable-database {
            border-collapse: separate;
            table-layout: fixed;
            width: 100%;
        }

        #datatable-database th,
        #datatable-database td {
            min-width: 120px;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-20 mb-20" id="data-database">
        {{-- LIST TABLE id="datatable-data" -- TABLE : datatable-Group-Form --}}
        <div class="col-md-6">

            {{-- LIST TABLE --}}
            <div class=" card-box mb-30"> {{-- LIST TABLE --}}
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-left">
                        <h4 class="text-blue h4">List TABLE</h4>
                    </div>
                    <div class="pull-right">

                        <button id="resetFilter-menu_table" onclick="filterDataTable()" class="btn btn-primary btn-sm"
                            role="button">All Filters</button>
                        <button id="resetFilter-menu_table" onclick="resetFilters()" class="btn btn-secondary btn-sm"
                            role="button">Reset</button>
                    </div>
                </div>
                <div class="mb-20" id="datatable-menu_table-wrapper">
                    <table id="datatable-menu_table" class="display cell-border" style="width:100%">
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
        {{-- FIELD DATA SHOW --}}
        <div class="col-md-6">
            <div class="faq-wrap">
                <h4 class="pd-20 text-blue description_table">Detail Data</h4>
                <div class="accordion" id="accordion-form-group">

                </div>
            </div>


        </div>

        <div class="col-md-12 mt-20"> {{-- LIST data --}}
            <div class=" card-box mb-30">
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-left ">
                        <h4 class="text-blue h4">List Data Tabel</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="field-show-header ">

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="pull-right">
                        <div class="btn-group">

                            <div class="row mb -20 pd-20">
                                <div class="col-auto ms-auto text-end">
                                    <button type="button" class="btn btn-success" data-toggle="modal"
                                        data-target="#modal-import-datatable">
                                        <i class="icon-copy bi bi-journal-arrow-up"></i> Upload
                                    </button>



                                    <div class="btn-group">
                                        <button onclick="toggleShowField()" type="button" class="btn btn-sm btn-primary">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> Un/show ALL
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> Report
                                        </button>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-secondary dropdown-toggle waves-effect"
                                                data-toggle="dropdown" aria-expanded="false">
                                                <i class="icon-copy bi bi-three-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" onclick="reportTable()"
                                                    type="button">Report</button>
                                                <button class="dropdown-item" onclick="reportTableFull()"
                                                    type="button">Report Full</button>
                                                <button class="dropdown-item" type="button">Template</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-20" id="datatable-dataDatatable-wrapper">
                    <table id="datatable-dataDatatable" class="display cell-border" style="width:100%">
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

    {{-- M O D A L Tampil DataTable --}}
    <div class="modal fade modal-tampil-datatable" id="modal-tampil-datatable" role="dialog"
        aria-labelledby="header-modal-tampil-datatable">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="header-modal-tampil-datatable">
                        Filter Data
                    </h4>
                    <button type="button" class="close close-modal" data-dismiss="modal">
                        ×
                    </button>
                </div>
                <div class="modal-body body-modal-tampil-datatable">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn close-modal btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="store-filter-datatable" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- M O D A L Tampil DataTable --}}


    {{-- MODAL IMPORT DB --}}
    <div class="modal fade" id="modal-import-datatable" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Import Data Table
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="form-import-datatable" method="POST">
                        <div class="form-group">
                            <label>Pilih data</label>
                            <input name="uploaded_file" type="file"
                                class="form-control-file form-control height-auto" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" onclick="importDatatable()">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL IMPORT DB --}}

    {{-- MODAL LOADING --}}
    <div class="modal fade" id="loading-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <br>
                <br>
                <div class="modal-body text-center">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="spinner-grow text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <br>
                <br>
            </div>
        </div>
    </div>
    {{-- MODAL LOADING --}}
@endsection

@section('js_code')
    <script>
        let element_option_type_data = '';
        let element_option_chosee_table = '';

        readyDB(function(db) {


            // TYPE DATA
            Object.entries(db.database_tables['TYPE-DATA'].data).forEach(([key, value]) => {
                element_option_type_data += `
            <option value="${key}">${value['TYPE-DATA']['text_data']}</option>
                `;
            });
            $('.type-data').append(element_option_type_data);

            // TABLE OPTIONS
            Object.entries(db.database_tables).forEach(([key, value]) => {
                $('.database-table').append(`
                    <option value="${key}">${value.description_table}</option>
                `);
            });

            // MENU OPTIONS
            Object.values(db.data_group_forms).forEach(element => {
                $('#menu_table').append(`
                    <option value="${element.uuid}">${element.description}</option>
                `);
            });

            // DATA TABLE
            datasetDatatable = Object.values(db.database_tables);

            let templateDataTable = {
                code_table: "menu_table",
                parent_table: null,
                primary_table: "code_table",
                menu_table: "STAND-ALONE",
                description_table: "Menu Table",
                fields: {
                    'code_table': {
                        sort_field: '1',
                        code_field: 'code_table',
                        description_field: 'Code Table',
                        visibility_data_field: 'filter',
                        type_data_field: 'TEXT',
                    },
                    'description_table': {
                        sort_field: '1',
                        code_field: 'description_table',
                        description_field: 'Description Table',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                    'menu_table': {
                        sort_field: '2',
                        code_field: 'menu_table',
                        description_field: 'Menu Table',
                        visibility_data_field: 'show',
                        type_data_field: 'TEXT',
                    },
                },
                data: datasetDatatable
            }

            GROUP_DATA = 'database_tables';
            let dataArrParameter = {
                tableId: 'menu_table',
                tableDataDetails: templateDataTable,
                datasetTable: null,
                paggingDatatable: true,
                staticName: null,
                isDeleteAction: false,
            };
            initDataTable(dataArrParameter);

        });
        /**/
    </script>
    @include('app.layout.JSDataDatatable')


    <script>
        // FUNCTION DATATABLE KHUSUS

        function dataShow(tableId, key_data_table) {
            console.log('FUNCTION ::: ' + tableId + ' - ' + key_data_table);
            if (tableId == 'menu_table') {
                let dataArrParameter = {
                    tableId: key_data_table,
                    tableDataDetails: null,
                    datasetTable: null,
                    paggingDatatable: true,
                    staticName: 'dataDatatable',
                    isDeleteAction: false,

                };
                initDataTable(dataArrParameter);
                createFormTable(key_data_table);
                return false;

            }

            console.log('FUNCTION :::  == DATA_dataShow - id_code_data:', tableId);
            resetForm(tableId);
            let detail_data_table = db['database_tables'][tableId];

            tableId_new = detail_data_table['primary_table'] ? detail_data_table['primary_table'] : tableId;
            if (tableId == tableId_new) {
                tableId = tableId_new;
                detail_data_table = db['database_tables'][tableId];
            }

            let data_datatable_detail = db['database_tables'][tableId]['data'][key_data_table];
            conLog('data_datatable_detail', data_datatable_detail);
            let first_data_datatable_detail = data_datatable_detail[detail_data_table['primary_table']];
            $(`#${tableId}-uuid_data`).val(first_data_datatable_detail.uuid_data);

            Object.entries(data_datatable_detail).forEach(([key, value]) => {
                $('#' + tableId + '-' + key).val(value.value_data).trigger(
                    'change'); // Trigger change event for select2
            });

            let uuid_parent = first_data_datatable_detail['uuid_data'];

            $(`#${tableId}-${detail_data_table['primary_table']}`).prop('disabled', true);


            const hasChild = (db['database_tables_child'][tableId]) ? true : false;
            conLog('hasChild', hasChild);
            conLog('tableId', tableId);

            if (hasChild) {
                // Lakukan sesuatu jika ada child
                db['database_tables_child'][tableId].forEach(table_id_child => {
                    let data_datatable_child_detail =
                        db['database_tables'][table_id_child]?.['data']?.[key_data_table] ?? null;

                    conLog('table_id_child', table_id_child)


                    let first_data_datatable_child_detail = detail_data_table['primary_table'];
                    $(`#${table_id_child}-uuid_data`).val(uuid_parent);
                    $(`#${table_id_child}-uuid_data`).val(uuid_parent);
                    if (!data_datatable_child_detail) {
                        console.warn(
                            `No child data found for table ${table_id_child} and code data ${key_data_table}`);
                        return; // Lewati iterasi ini jika tidak ada data child
                    } else {
                        conLog('data_datatable_child_detail', data_datatable_child_detail);
                        Object.entries(data_datatable_child_detail).forEach(([key, value]) => {
                            $('#' + table_id_child + '-' + key).val(value.value_data).trigger(
                                'change'); // Trigger change event for select2
                        });
                    }
                    $(`#${table_id_child}-${detail_data_table['primary_table']}`).val(key_data_table);
                    $(`#${table_id_child}-${detail_data_table['primary_table']}`).prop('disabled', true);

                });
            }

        }

        function storeDataTableDatabase(code_table) {
            conLog('code_table', code_table);
            $(`#form-table-detail-${code_table} :input`).prop('disabled', false);
            const formArray = $('#form-table-detail-' + code_table).serializeArray();
            const result = {};
            formArray.forEach(item => result[item.name] = item.value);
            conLog('formArray', formArray);

            let data_table = {
                ...db['database_tables'][code_table]
            };

            delete data_table['join_fields'];
            delete data_table['join_data'];
            delete data_table['field_show'];


            if (db['database_field_show'][code_table]) {
                Object.entries(db['database_field_show'][code_table]).forEach(([key_field, fields]) => {
                    let value_gabungan = '';
                    // conLog('key_field', key_field);
                    fields.forEach(items_field => {
                        value_gabungan = value_gabungan + `${items_field.split_by}` + $(
                            `#${code_table}-${items_field.field_show_code}`).val();
                    });
                    value_gabungan = value_gabungan.slice(1);
                    let new_data_form = {
                        name: key_field,
                        value: value_gabungan
                    }
                    formArray.push(new_data_form);
                });
            }

            conLog('formArray',formArray);
            // return false;

            $.ajax({
                url: '/api/database/data/storeData',
                type: "POST",
                data: {
                    data_table: data_table,
                    data_text: formArray,
                },
                success: function(response) {
                    let primary = db['database_tables'][code_table]['primary_table'];
                    let code_data = toUUID($(`#${code_table}-${primary}`)
                        .val());

                    console.log('response')
                    console.log(response)
                    let table = $('#datatable-' + DATA_TABLE_ID).DataTable();
                    let uuid_data = $(`#${code_table}-uuid_data`).val();
                    let updatedData = response.data.add_data;
                    DATA_dataSetFilter.push(updatedData);
                    db['database_tables'][code_table]['data'][code_data] = response.data.data
                    let data_index_update = response.data.data;
                    setDatabase('DATABASE', db);
                    // ============================
                    // === CREATE / ADD NEW DATA ===
                    // ============================
                    if (!uuid_data) {
                        if (response && response.data.add_data) {
                            // tambahkan row baru
                            table.row.add(response.data.add_data).draw(false);
                        }
                        return; // selesai
                    }
                    // ============================
                    // === UPDATE EXISTING DATA ===
                    // ============================
                    if (response && response.data.add_data) {
                        let keyOld = code_data; // ← INI yang kamu ambil dari input hidden
                        let keyNew = updatedData[primary]; // biasanya sama, tapi tetap aman

                        // =====================================
                        // 1. Cari row lewat HTML class render
                        // =====================================
                        let rowEl = $(`.row-datatable-${code_data}`).closest('tr');

                        if (rowEl.length === 0) {
                            console.warn("Row not found via render:", code_data);
                            return;
                        }

                        // =====================================
                        // 2. Hapus dari DataTable
                        // =====================================
                        table.row(rowEl).remove().draw(false);
                        // ==============================
                        // 2. Jika row lama ditemukan → hapus dulu
                        // ==============================
                        if (rowEl.length > 0) {
                            DATA_dataSetFilter = DATA_dataSetFilter.filter(r => r[primary] != keyOld);
                        }

                        // ==============================
                        // 3. Tambahkan row baru (update)
                        // ==============================
                        table.row.add(updatedData).draw(false);
                    }
                    $(`.uuid_data_value`).val(response.data.uuid_data);
                },
                complete: function() {
                    // Always hide the loading indicator after the AJAX call completes

                },
                error: function(response) {
                    conLog('error', response);
                    //alertModal()
                }
            });
        }


        function importDatatable() {
            var form = $('#form-import-datatable')[0];
            var form_data = new FormData(form);
            form_data.append('_token', $('meta[name="csrf-token"]').attr('content'));
            conLog('form_data', form_data);
            //  return false;
            startLoading();
            $.ajax({
                url: '/database/data/import-datatable',
                type: "POST",
                data: form_data,
                contentType: false,
                processData: false,
                success: function(response) {
                    conLog('responses data import', response);
                    stopLoading();
                },
                error: function(response) {
                    conLog('response err', response)
                }
            });
        }

        function reportTableFull() {
            conLog('function reportTableFull code_data', TABLE_ID);
            $.ajax({
                url: '/database/data/export-datatable',
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    code_table: TABLE_ID
                },
                success: function(response) {
                    conLog('responses', response);

                    // return false;

                    let safeUrl = `/download-excel?file=${encodeURIComponent(response.data)}`;

                    var dlink = document.createElement("a");
                    dlink.href = safeUrl;
                    dlink.setAttribute("download", "");
                    document.body.appendChild(dlink);
                    dlink.click();
                    dlink.remove();
                },
                error: function(response) {
                    conLog('response err', response)
                    // alertModal()
                }
            });
        }

        function resetForm(table_code) {
            createFormTable(table_code);
        }

        /*
            function storeDataTableDatabase(code_table) {
                const fileInputs_element = document.querySelectorAll(`input[type="file"].${code_table}`);

                fileInputs_element.forEach(input => {
                    conLog('xx', input);
                    input.addEventListener('change', handleFileInput);
                });

                const form = document.getElementById('myForm');
                const formData = new FormData();
                const fileInputs = document.querySelectorAll(`input[type="file"].${code_table}`);
                const fileInfo = document.getElementById('fileInfo');

                fileInfo.innerHTML = ''; // Clear previous file info

                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            formData.append(input.name, input.files[i]);
                        }
                    }
                });
                formData.append('code_table_data', code_table);


                // fields
                var formDataArray = $(`.form-${code_table}`).serializeArray();
                // var formDataArray = new FormData(document.getElementById(`#FORM-${code_table}`));
                let db_table = db['db']['database_table'][code_table];
                let data_source_this_field = {};
                Object.values(db['db']['database_field'][code_table]).forEach(element => {
                    if (element.type_data_field == KONSTANTA['Input Autocomplite']) {
                        data_source_this_field[element.full_code_field] = db['db']['database_data_source'][element
                            .full_code_field
                        ];
                        data_source_this_field[element.full_code_field]['primary_field'] = db['db']['database_table'][
                            data_source_this_field[element.full_code_field]['table_data_source']
                        ]['primary_table'];
                        data_source_this_field[element.full_code_field]['code_field'] = element.code_field;
                    }
                });

                if (db['db']['database_field_show'][code_table]) {
                    Object.entries(db['db']['database_field_show'][code_table]).forEach(([key_field, fields]) => {
                        let value_gabungan = '';
                        // conLog('key_field', key_field);
                        fields.forEach(items_field => {
                            value_gabungan = value_gabungan + `${items_field.split_by}` + $(
                                `#${code_table}-${items_field.field_show_code}`).val();
                        });
                        value_gabungan = value_gabungan.slice(1);
                        let new_data_form = {
                            name: key_field,
                            value: value_gabungan
                        }
                        formDataArray.push(new_data_form);
                    });
                }

                // conLog('formDataArray', formDataArray);

                // return false;
                // conLog('data_source_this_field', data_source_this_field);
                $.ajax({
                    url: '/api/mbg/manage/database/store-database',
                    type: "POST",
                    headers: {
                        'Content-Type': 'application/json',
                        'x-auth-login': ui_dataset.ui_dataset.user_authentication.auth_login
                    },
                    data: JSON.stringify({
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        formData: formDataArray,
                        uuid_data: $('#uuid_data').val(),
                        data_table: db_table,
                        data_source_this_field: data_source_this_field
                    }),
                    success: function(response) {
                        conLog('response', response);


                        if (db_table['primary_table']) {
                            $('.secondary_btn_store').attr('disabled', false);
                            $('.secondary_key').val(response['data']['data_database_datatable'][db_table[
                                'primary_table']]);
                        }

                        showModalSuccess();

                        $('#uuid_data').val(response.data.uuid_data);
                        formData.append('code_data', response.data.code_data);
                        formData.append('uuid_data', response.data.uuid_data);

                        if (formData) {
                            console.log('asad');
                            $.ajax({
                                url: '/api/mbg/manage/database/store-database-file',
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    conLog('re', response);
                                },
                                contentType: false,
                                processData: false,
                                error: function(response) {
                                    conLog('error', response);
                                    //alertModal()
                                }
                            });
                            formData.forEach((value, key) => {
                                if (value instanceof File) {
                                    console.log(`Key: ${key}`);
                                    console.log(`File Name: ${value.name}`);
                                    console.log(`File Size: ${value.size} bytes`);
                                    console.log(`File Type: ${value.type}`);
                                } else {
                                    console.log(`Key: ${key}`);
                                    console.log(`Value: ${value}`);
                                }
                            });
                        } else {
                            console.log('kosong');
                        }
                        refreshSession();

                    },
                    complete: function() {
                        // Always hide the loading indicator after the AJAX call completes
                        refreshSession();
                        actionCard(code_table)
                    },
                    error: function(response) {
                        conLog('error', response);
                        stopLoading();
                        //alertModal()
                    }
                });




            }
        */



        /**
         * Membuat kolom tertentu menjadi sticky (freeze)
         * @param {string} tableId - ID tabel (tanpa tanda #)
         * @param {number} colIndex - Index kolom yang ingin di-freeze (0-based)
         */
    </script>
@endsection
