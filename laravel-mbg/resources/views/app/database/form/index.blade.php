@extends('app.layout.main')
@section('src_css')
    <link rel="stylesheet" href="/assets/src/plugins/datatables/css/jquery.dataTables.min.css">
@endsection()

@section('content')
    <div class="pd-20 card-box mb-30">
        <div class="clearfix mb-10">
            <div class="pull-left">
                <h4 class="text-blue h4">Field Form</h4>
                <p>Tambah atau edit form</p>
            </div>
            <div class="pull-right" hidden>
                <a href="#basic-form1" id="basic-form1" class="btn btn-primary btn-sm scroll-click" rel="content-y"
                    data-toggle="collapse" role="button">Reset</a>
            </div>
        </div>
        <form>
            <div class="fields row profile-info" id="field-form-1">
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <label>Nama Field</label>
                        <input type="text" class="form-control description_field" id="description_field-1">
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 mb-2">
                    <div class="form-group">
                        <label>Type Data Field</label>
                        <select style="width: 100%;" onchange="selectSource(1)" name="type_data_field-1"
                            id="type_data_field-1" class="custom-select2 form-control type-data">
                            <option value="">Tipe Data Field</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-sm-12 mb-2">
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level_data_field-1" id="level_data_field-1" class="form-control">
                            <option value="1">1 | Public</option>
                            <option value="2">2 | Admin Divisi</option>
                            <option value="3">3 | HR</option>
                            <option value="4">4 | Manajemen</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12 mb-2">
                    <div class="form-group">
                        <label>Visibility</label>
                        <select name="visibility_data_field-1" id="visibility_data_field-1" class="form-control">
                            <option value="show">Show</option>
                            <option value="hide">Hide</option>
                            <option value="filter">Filter</option>
                            <option value="block">Block</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1 col-sm-6">
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="text" class="form-control" id="sort_field" value="1">
                    </div>
                </div>
                <div class="col-md-1 col-sm-6">
                    <div class="form-group">
                        <label>Action</label>
                        <button onclick="deleteFiled(1)" type="button" id="1" name="btn-delete-1"
                            class="form-control btn btn-danger btn-delete">
                            <i class="icon-copy dw dw-delete-3"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center btn-add-form-field">
                <div class="col-md-3 col-sm-12">
                    <div class="form-group">
                        <button type="button" onclick="addFormField()" class="btn-block btn btn-primary add-form-field">
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary agrement" onclick="openPersetujuan()"
                            alt="modal">
                            Tambah Persetujuan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="pd-20 card-box mb-30">
        <div class="clearfix mb-10">
            <div class="pull-left">
                <h4 class="text-blue h4">Manage Form</h4>
                <p>Tambah atau edit form</p>
            </div>
            <div class="pull-right" hidden>
                <a href="#basic-form1" id="basic-form1" class="btn btn-primary btn-sm scroll-click" rel="content-y"
                    data-toggle="collapse" role="button">Reset</a>
            </div>
        </div>
        <form>
            <div class="profile-info">
                <div class="form-group row">
                    <label class="col-sm-12 col-md-2 col-form-label">Nama Form</label>
                    <div class="col-sm-12 col-md-4 mb-2">
                        <input class="form-control" type="text" id="description_table" name="description_table"
                            placeholder="Nama Tabel">
                    </div>
                    <div class="col-sm-12 col-md-2">
                        <input class="form-control" id="count_field" type="text" value="1" placeholder="">
                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-md-2 col-form-label">Primary</label>
                    <div class="col-sm-12 col-md-4">
                        <input type="text" class="form-control" name="primary_table" id="primary_table">
                        {{-- <select name="" id="" class="form-control">
                            <option value="">Pilih Field Primary</option>
                        </select> --}}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-md-2 col-form-label">Nama Menu</label>
                    <div class="col-sm-12 col-md-4">
                        <select style="width: 100%;" name="menu_table" id="menu_table"
                            class="custom-select2 form-control employees">
                            <option value="">Nama Menu</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-md-2 col-form-label">Level Table</label>
                    <div class="col-sm-12 col-md-4">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="jenis-form" id="single" value="single"
                                    autocomplete="off" checked="">
                                Primary
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="jenis-form" id="multi" value="multi"
                                    autocomplete="off">
                                Secondary
                            </label>

                        </div>
                    </div>

                </div>
                <div class="form-group row">
                    <label class="col-sm-12 col-md-2 col-form-label">Referensi Tabel</label>
                    <div class="col-sm-12 col-md-4">
                        <select style="width: 100%;" onchange="selectParent()" name="parent_table" id="parent_table"
                            class="custom-select2 form-control database-table">
                            <option value="">Pilih Tabel Referensi</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <button type="button" class="col-6 btn btn-primary btn-block" onclick="storeForm()">Simpan
                        form</button>
                </div>
            </div>
            <div class="profile-info">
            </div>
        </form>
    </div>

    <div class=" card-box mb-30">
        <div class="pd-20 clearfix mb-10">
            <div class="pull-left">
                <h4 class="text-blue h4">DATABASE TABLE</h4>
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

    {{-- modal from_table --}}
    <div class="modal fade" id="modal-from_table" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Data Dari Table
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <div class="modal-body">
                    <label for="table">Table Rujukan</label>
                    <select name="table" id="table" onchange="selectTableSource()"
                        class="custom-select2 form-control select-table database-table" style="width: 100%;">
                        <option value="">Pilih tabel</option>
                    </select>
                </div>
                <div class="modal-body">
                    <label for="field_get">Kolom diambil</label>
                    <select name="field_get" id="field_get" class="custom-select2 form-control select-field_get"
                        style="width: 100%;">
                        <option value="">Pilih kolom</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="save-from-table" data-dismiss="modal"
                        onclick="saveFromTable()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal GABUNGAN --}}
    <div class="modal fade" id="modal-GABUNGAN" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Buat Field Gabungan
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <input type="text" name="" id="sort-gabungan" value="-1">
                    <div class="row">

                        <div class="col-12 text-center" id="button-add-gabungan">
                            <button onclick="addGabungan()" class="col-12 btn-bloc btn btn-primary">tambah</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="save-from-table" data-dismiss="modal"
                        onclick="saveGabunganField()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal agrement --}}
    <div class="modal fade" id="modal-agrement" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Buat Persetujuan
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>

                <div class="modal-body">
                    <input type="text" name="" id="sort-persetujuan" value="0">
                    <div class="row" id="field-kehadiran">

                        <div class="col-12 row row-persetujuan">

                        </div>



                        <div class="col-12 text-center" id="button-add-persetujuan">
                            <button onclick="addPersetujuan()" class="col-12 btn-bloc btn btn-primary">tambah</button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="save-from-table" onclick="addAgrement()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script>
        let element_option_type_data = '';
        let element_option_chosee_table = '';
        let from_table_form = [];
        let gabungan_field = [];
        let tableData = {};
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
            data: []
        }
        let dataArrParameter = {
            tableId: 'menu_table',
            tableDataDetails: {},
            datasetTable: null,
            paggingDatatable: true,
            staticName: null,
            isDeleteAction:true,
        };




        getReadyDatabase().then(function(db) {



            Object.entries(db['database_tables']['TYPE-DATA']['data']).forEach(([key, value]) => {
                element_option_type_data =
                    `${element_option_type_data }  
                <option value="${key}">${value['TYPE-DATA']['text_data']}</option>`;
            });
            $(`.type-data`).append(element_option_type_data);

            // database table option
            Object.entries(db['database_tables']).forEach(([key, value]) => {
                $(`.database-table`).append(`
                        <option value="${key}">${value['description_table']}</option>
                    `);
            });

            // menu option
            Object.values(db['data_group_forms']).forEach(element => {
                // conLog('xx', element)
                $(`#menu_table`).append(`
                    <option value="${element.uuid}">${element.description}</option>
                `);
            });

            // create table datatable
            datasetDatatable = Object.values(db['database_tables']);
            templateDataTable.data = datasetDatatable;
            dataArrParameter.tableDataDetails = templateDataTable;
            GROUP_DATA = 'database_tables';

            initDataTable(dataArrParameter);
            conLog('dataArrParameter', dataArrParameter)
        });


        function selectParent() {
            let code_table = $('#parent_table').val();
            let primary_field_table = db['database_tables'][code_table]['primary_table'];
            if (code_table) {
                $('#primary_table').val(db['database_tables'][code_table]['fields'][primary_field_table][
                    'description_field'
                ]);
            }
        }

        function addFormField() {
            let countField = $('#count_field').val();
            countField++;
            $('#count_field').val(countField);

            let element_form_field_full = `
                <div class="fields row profile-info" id="field-form-${countField}">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Nama Field</label>
                                <input type="text" class="form-control" id="description_field-${countField}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12 mb-2">
                            <div class="form-group">
                                <label>Type Data Field</label>
                                <select onchange="selectSource(${countField})" style="width: 100%;" name="type_data_field-${countField}" id="type_data_field-${countField}" class="s2 form-control type-data">
                                    <option value="">Tipe Data Field</option>
                                    ${element_option_type_data}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 mb-2">
                            <div class="form-group">
                                <label>Level</label>
                                <select  name="level_data_field-${countField}"
                                    id="level_data_field-${countField}" class="form-control">
                                    <option value="1">1 | Public</option>
                                    <option value="2">2 | Admin Divisi</option>
                                    <option value="3">3 | HR</option>
                                    <option value="4">4 | Manajemen</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2 col-sm-12 mb-2">
                            <div class="form-group">
                                <label>Visibility</label>
                                <select name="visibility_data_field-${countField}" id="visibility_data_field-${countField}" class="form-control">
                                    <option value="show">Show</option>
                                    <option value="hide">Hide</option>
                                    <option value="filter">Filter</option>
                                    <option value="block">Block</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Urutan Field</label>
                                <input type="text" class="form-control" value="${countField}" id="sort_field-${countField}">
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-6">
                            <div class="form-group">
                                <label>Action</label>
                                <button onclick="deleteFiled(${countField})" type="button" id="${countField}" name="btn-delete-${countField}"
                                    class="form-control btn btn-danger btn-delete">
                                    <i class="icon-copy dw dw-delete-3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            $('.btn-add-form-field').before(element_form_field_full);
            // $(`#type_data_field-${countField}`).select2();
        }

        function deleteFiled(id_field) {
            var clickedButtonId = id_field;
            console.log('btn delete', clickedButtonId);
            $(`#field-form-${clickedButtonId}`).remove();
        }

        function selectSource(id_form) {
            let val_option_source_field = $(`#type_data_field-${id_form}`).val();
            switch (val_option_source_field) {
                case 'DARI-TABEL':
                    $('#save-from-table').attr('onclick', `saveFromTable('${id_form}')`)
                    $('#modal-from_table').modal('show');
                    return false;
                    break;
                case 'INPUT-AUTOCOMPLITE':
                    $('#save-from-table').attr('onclick', `saveFromTable('${id_form}')`)
                    $('#modal-from_table').modal('show');
                    return false;
                    break;
                case 'GABUNGAN':
                    $('#save-from-table').attr('onclick', `saveFromTable('${id_form}')`);
                    $(`#sort-gabungan`).val(-1);
                    $('.gabungan-fields').remove();
                    // addGabungan();
                    $('#modal-GABUNGAN').modal('show');
                    return false;
                    break;
                default:
                    return false;
            }
        }

        function selectTableSource() {
            let table_source = $(`#table`).val();

            $('#field_get').empty();
            if (db['database_tables'][table_source]['join_fields'] == undefined) {
                Object.entries(db['database_tables'][table_source]['fields']).forEach(([key, value]) => {
                    $('#field_get').append(`
                        <option value="${value.code_field}">${value.description_field}</option>
                    `);
                });
            } else {
                Object.entries(db['database_tables'][table_source]['join_fields']).forEach(([key, value]) => {
                    $('#field_get').append(`
                        <option value="${value.code_field}">${value.description_field}</option>
                    `);
                });
            }
            return false;
        }

        function saveFromTable(id_form) {
            CL('save data source')
            from_table_form[`data-source-${id_form}`] = {
                table_data_source: $(`#table`).val(),
                field_get_data_source: $(`#field_get`).val(),
                primary_field_data_source: $(`#field_get`).val(),
            }
            // close modal
            $('#modal-from_table').modal('hide');
        }

        function storeForm() {
            //create-form
            CL('create-from');
            let countField = $('#count_field').val();
            let data_form = [];
            let count_field_form = 0;
            let form_detail = {
                'description_table': $(`#description_table`).val(),
                'menu_table': $(`#menu_table`).val(),
                'primary_table': $(`#primary_table`).val(),
                'parent_table': $(`#parent_table`).val()
            }
            startLoading();

            for (var i = 0; i < countField; i++) {
                if ($(`#description_field-${i+1}`).val()) {
                    let data_field = {
                        'description_field': $(`#description_field-${i+1}`).val(),
                        'type_data_field': $(`#type_data_field-${i+1}`).val(),
                        'level_data_field': $(`#level_data_field-${i+1}`).val(),
                        'visibility_data_field': $(`#visibility_data_field-${i+1}`).val(),
                        'sort_field': count_field_form,
                        'code_field': toUUID($(`#description_field-${i+1}`).val()),
                    }
                    if ($(`#type_data_field-${i+1}`).val() == 'DARI-TABEL' || $(
                            `#type_data_field-${i+1}`).val() == 'INPUT-AUTOCOMPLITE') {
                        data_field.data_source = from_table_form[`data-source-${i+1}`]
                    }

                    if ($(`#type_data_field-${i+1}`).val() == 'GABUNGAN') {
                        data_field.gabungan = gabungan_field[`gabungan-filed-${i+1}`]
                    }
                    data_form.push(data_field);
                    count_field_form++;
                }
            }
            form_detail.fields = data_form;
            conLog('data_form', form_detail);

            // form_detail.persetujuan = persetujuan;
            // refreshSession();
            // return false;
            // S T O R E
            $.ajax({
                url: '/api/database/form/storedatadatatable',
                type: "POST",
                data: {
                    data: form_detail
                },
                success: function(response) {
                    CL(response)
                    refreshSession().then(() => {
                        datasetDatatable = Object.values(db['database_tables']);

                        templateDataTable.data = datasetDatatable;
                        dataArrParameter.tableDataDetails = templateDataTable;
                        initDataTable(dataArrParameter);
                    });
                    stopLoading()
                },
                error: function(response) {
                    conLog('error', response)
                    //alertModal();
                }
            });
        }

        function destroyForm(code_table) {
            //delete-form
            $.ajax({
                url: '/api/database/form/deletedatadatatable',
                type: "POST",
                data: {
                    code_table: code_table
                },
                success: function(response) {
                    CL(response)
                    refreshSession().then(() => {
                        datasetDatatable = Object.values(db['database_tables']);
                        templateDataTable.data = datasetDatatable;
                        dataArrParameter.tableDataDetails = templateDataTable;
                        initDataTable(dataArrParameter);
                    });
                },
                error: function(response) {
                    conLog('error', response)
                    //alertModal()
                }
            });
        }

        function addGabungan() {
            let id_form = $('#count_field').val();
            let new_id = parseInt($(`#sort-gabungan`).val()) + 1;
            let element_option_gabungan = ``;
            for (for_fields = 1; for_fields < id_form; for_fields++) {
                try {

                    let value_ = toUUID($(`#description_field-${for_fields}`).val());
                    let description_ = $(`#description_field-${for_fields}`).val();
                    element_option_gabungan =
                        `${element_option_gabungan} <option value="${value_}">${description_}</option>`;
                } catch (error) {

                }
            }
            const parentJoin =
                db['database_tables']?.[tableData.parent_table]?.join_fields;

            const codeJoin =
                db['database_tables']?.[tableData.code_table]?.join_fields;

            let tableParent =
                parentJoin && Object.keys(parentJoin).length > 0 ?
                parentJoin :
                codeJoin && Object.keys(codeJoin).length > 0 ?
                codeJoin : {};


            conLog('tableParent', tableParent);
            if (Object.keys(tableParent).length > 0) {
                let parent_table = $(`#parent_table`).val();
                Object.entries(tableParent).forEach(([key, value]) => {
                    element_option_gabungan =
                        `${element_option_gabungan }  
                <option value="${key}">${value['description_field']}</option>`;
                });
            }
            console.log('element_option_gabungan', element_option_gabungan);
            $(`#sort-gabungan`).val(new_id)
            $('#button-add-gabungan').before(`
                <div class="col-12 gabungan-fields" id="gabungan-${new_id}">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="table">Tabel Referensi</label>
                                <select name="table_references" id="table_references-${new_id}"
                                    onchange="chooseTableReferenceJoin()" 
                                    class="custom-select2 form-control select-table database-table"
                                    style="width: 100%;">
                                    <option value="">Pilih Field</option>
                                    ${element_option_chosee_table}
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="table">Field</label>
                                <select name="gabungan-fields" id="gabungan-fields-${new_id}" 
                                    class="custom-select2 form-control select-table database-table"
                                    style="width: 100%;">
                                    <option value="">Pilih Field</option>
                                    ${element_option_gabungan}
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <label for="field_get">Pembatas ${new_id}</label>
                                <input type="text" class="form-control" name="gabungan-pembatas-${new_id}" id="gabungan-pembatas-${new_id}">
                            </div>
                        </div>
                        <div class="col-2">
                            <label for="field_get">hapus</label>
                            <button type="button"  onclick="deleteFieldGabungan(${new_id})" class="form-control btn btn-danger btn-delete">
                                <i class="icon-copy dw dw-delete-3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
            $(`#sort-gabungan`).val(new_id);
            $(`#gabungan-fields-${new_id}`).select2();
            $(`#table_references-${new_id}`).select2();
        }

        function saveGabunganField() {
            let id_form = $('#count_field').val();
            let count_field_gabungan = $('#sort-gabungan').val();
            conLog('count_field_gabungan', count_field_gabungan)
            let field_gabungan = {};
            let sort_field = 0;
            for (loop_save_gabungan = 0; loop_save_gabungan <= count_field_gabungan; loop_save_gabungan++) {
                if ($(`#gabungan-fields-${loop_save_gabungan}`).val()) {
                    field_gabungan[sort_field] = {
                        field_show_code: $(`#gabungan-fields-${loop_save_gabungan}`).val(),
                        table_show_code: ($(`#table_references-${loop_save_gabungan}`).val()) ? $(
                            `#table_references-${loop_save_gabungan}`).val() :
                        null, //kalau null berrti nnti adalah nama table ini
                        split_by: $(`#gabungan-pembatas-${loop_save_gabungan}`).val(),
                        sort_field: sort_field,
                    }
                    sort_field++;
                }
            }
            gabungan_field[`gabungan-filed-${id_form}`] = field_gabungan;
            conLog('gabungan_field', gabungan_field);
        }
    </script>

    <script>
        // FUNCTION DATATABLE KHUSUS
        function dataShow(tableId, element) {
            const elementId = element;
            if (!elementId) {
                console.error("❌ Element ID tidak ditemukan.");
                return;
            }



            // Ambil data dari objek global db (atau dari IndexedDB / API, sesuaikan)
            tableData = db['database_tables'][elementId];
            if (!tableData) {
                console.error(`❌ Data '${elementId}' tidak ditemukan.`);
                return;
            }
            console.log("🟦 Edit table:", tableData);

            // Reset field gabungan
            gabungan_field = {};

            // Isi bagian utama form
            $("#description_table").val(tableData.description_table || "");
            $("#menu_table").val(tableData.menu_table || "").trigger('change');
            $("#parent_table").val(tableData.parent_table || "");
            $("#primary_table").val(tableData.primary_table || "");

            // Primary field
            const primaryField = tableData.primary_table;
            const fieldObj = tableData.fields[primaryField];
            if (fieldObj) {
                $("#primary_table").val(fieldObj.description_field || "");
            } else {
                $("#primary_table").val("");
                console.warn(`⚠️ Primary field '${primaryField}' tidak ditemukan.`);
            }

            // Bersihkan field lama
            $(".fields").remove();

            // Urutkan field berdasarkan sort_field
            const sortedFields = Object.values(tableData.fields).sort(
                (a, b) => parseInt(a.sort_field) - parseInt(b.sort_field)
            );

            conLog('sortedFields', sortedFields)
            let countField = 0;
            $('#count_field').val(countField);
            sortedFields.forEach((edit_field) => {

                // Tambahkan form field baru
                const elementAddFieldForm = addFormField();
                countField = $('#count_field').val();
                $(".btn-add-form-field").before(elementAddFieldForm);

                // Isi nilai-nilai ke field
                $(`#description_field-${countField}`).val(edit_field.description_field || "");
                $(`#type_data_field-${countField}`).val(edit_field.type_data_field || "");
                $(`#level_data_field-${countField}`).val(edit_field.level_data_field || "");
                $(`#visibility_data_field-${countField}`).val(edit_field.visibility_data_field || "");
                $(`#sort_field-${countField}`).val(edit_field.sort_field || "");

                // Update tampilan select2 agar sesuai
                $(`#select2-type_data_field-${countField}-container`).text(edit_field.type_data_field || "");

                // Inisialisasi select2 (jika perlu)
                $(`#type_data_field-${countField}`).select2();
            });

            console.log("✅ Form edit berhasil dimuat untuk:", tableData.code_table);
        }
    </script>
@endsection()
