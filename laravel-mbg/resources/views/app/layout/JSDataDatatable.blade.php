{{-- @include('app.layout.JSDataDatatable') --}}
<script>
    // =========================
    // FUNGSI UMUM
    // =========================
    let DATA_dataSetFilter = [];
    let DATA_fieldsTableFilterDatatable = [];
    let DATA_datasetDatatable = [];
    let DATA_TABLE_ID = null;
    let DATA_CODE_TABLE = null;
    let DATA_GROUP_DATA = 'database_datas';
    let DATA_DELETE_ID_CODE_DATA = "";
    let DATA_paggingDatatable = false;
    let hasChild = false;
    let isShowAllField = false;
    let detail_data_table = [];

    function DATA_createCellByTypeData(field_data, value_data) {

        // conLog('field_data', field_data);
        // conLog('value_data', value_data)
        // conLog('detail_data_table', detail_data_table);
        let data_field = detail_data_table?.join_fields?.[field_data] ??
            detail_data_table?.fields?.[field_data];
        // conLog('data_field', data_field);
        switch (data_field.type_data_field) {
            case 'TEXT':
                return value_data
                break;
            case 'DARI-TABEL':
                let data_source = data_field['data_source'];
                // conLog('dataTableField_data_source', dataTableField_data_source)
                let element_option_data_source = '';

                if (data_source) {
                    let code_table_data_source = data_source['table_data_source'];
                    let field_get_data_source = data_source['field_get_data_source'];
                    let data_options = value_data;
                    try {
                        data_options = db['database_tables'][code_table_data_source]['data'][value_data][
                            field_get_data_source
                        ]['text_data'];
                    } catch (error) {
                        return value_data;
                    }
                    return data_options
                    break;
                }
            default:
                return value_data
                break;

        }
    }

    // Fungsi untuk menginisialisasi DataTable
    function DATA_initDataDatatable(tableId, data_field_table = null, datasetTable = null, DATA_paggingDatatable =
        false) {
        console.log('FUNCTION ::: ' + tableId);
        // conLog('data_field_table', data_field_table);
        // conLog('datasetTable', datasetTable);
        conLog('DATA_CODE_TABLE', DATA_CODE_TABLE);
        // conLog('DATA_datasetDatatable', DATA_datasetDatatable);
        detail_data_table = db['database_tables'][DATA_CODE_TABLE];
        hasChild = (db['database_tables_child'][DATA_CODE_TABLE]) ? true : false;
        let showIcon = 'show-field icon-copy bi bi-eye';
        let hideIcon = 'show-field icon-copy bi bi-eye-slash';
        let freezeIcon = 'frozen icon-copy bi bi-snow2';
        let unfreezeIcon = 'frozen icon-copy bi bi-snow';
        let header_table_support = '';
        if (!data_field_table || data_field_table.length === 0) {
            data_field_table = Object.values(db['database_tables'][DATA_CODE_TABLE]['fields']);
        }

        if (!datasetTable || datasetTable.length === 0) {
            conLog('dataset harus null', datasetTable);
            if (hasChild) {
                datasetTable = db['database_tables'][DATA_CODE_TABLE]['join_data'];
            } else {
                datasetTable = db['database_tables'][DATA_CODE_TABLE]['data'];
            }
            conLog('datasetTable NEW', datasetTable);

            let result = null;
            if (datasetTable) {
                result = Object.values(datasetTable).map(fields =>
                    Object.fromEntries(
                        Object.entries(fields).map(([field, value]) => [field, value.value_data])
                    )
                );
            }
            datasetTable = DATA_dataSetFilter = result;
            // DATA_dataSetFilter = datasetTable;
        }


        // FIELD SEGMEN
        // Mengurutkan berdasarkan sort_field (dikonversi ke angka)
        data_field_table.sort((a, b) => Number(a.sort_field) - Number(b.sort_field));

        let countIdColor = 0;
        let countField = 0;
        let data_field_table_new = [];
        data_field_table.forEach(item => {
            let iconShowField = showIcon;
            if (detail_data_table['field_show']) {

                if (!detail_data_table['field_show'].includes(item.code_field)) {
                    if (item.visibility_data_field != 'block') {
                        item.visibility_data_field = 'hide';
                        iconShowField = hideIcon;
                    }
                }
            }
            header_table_support += `
                                    <div class="btn-group mb-15">
										<button type="button" disabled class="btn btn-${COLOR_BOOTSTRAP[countIdColor]}">${item.description_field}</button>
										<button type="button" class="btn btn-light" >
                                            <i onclick="DATA_filterDataTable('${tableId}','${item.code_field}')" class="icon-copy bi bi-funnel  mr-10"></i>
                                            <i id="toggle-column-${DATA_TABLE_ID}-${countField}" class="mr-10 ${iconShowField}" onclick="toggleColumnAuto('datatable-${tableId}', ${countField})"></i>
                                            
                                            <i id="freeze-${countField}" class=" ${unfreezeIcon}" onclick="toggleFreezeColumn('datatable-${tableId}', ${countField})"></i>
                                        </button>
									</div>
                       
            `;
            data_field_table_new.push(item);
            countField++;
        });
        data_field_table = data_field_table_new;

        if (hasChild) {
            countIdColor = 0;
            db['database_tables_child'][DATA_CODE_TABLE].forEach(table_id_child => {
                // Clone fields agar delete tidak memodifikasi db asli
                let remove_child_index = {
                    ...db['database_tables'][table_id_child]['fields']
                };
                delete remove_child_index[detail_data_table.primary_table];


                let data_field_child_table = Object.values(remove_child_index);
                // conLog('data field chield ori', data_field_child_table);
                data_field_child_table.sort((a, b) => Number(a.sort_field) - Number(b.sort_field));
                // conLog('data field chield sort', data_field_child_table);
                // data_field_child_table.shift();
                data_field_child_table = data_field_child_table.filter(item => item.sort_field !== null);
                // conLog('data field chield shift', data_field_child_table);


                let data_field_child_table_new = [];
                Object.values(remove_child_index).forEach(item => {
                    let iconShowField = showIcon;
                    if (detail_data_table['field_show']) {
                        if (!detail_data_table['field_show'].includes(item.code_field)) {
                            if (item.visibility_data_field != 'block') {
                                item.visibility_data_field = 'hide';
                                iconShowField = hideIcon;
                            }
                        }
                    }

                    data_field_child_table_new.push(item);
                    header_table_support += `
                                    <div class="btn-group mb-15">
										<button type="button" disabled class="btn btn-${COLOR_BOOTSTRAP[countIdColor]}">${item.description_field}</button>
										<button type="button" class="btn btn-light" >
                                            <i onclick="DATA_filterDataTable('${tableId}','${item.code_field}')" class="icon-copy bi bi-funnel "></i>
                                            <i id="toggle-column-${DATA_TABLE_ID}-${countField}" class="${iconShowField}  mr-10" onclick="toggleColumnAuto('datatable-${tableId}',${countField})"></i>
                                            <i id="freeze-${countField}" class="mr-20 ${unfreezeIcon}" onclick="toggleFreezeColumn('datatable-${tableId}', ${countField})"></i>
                                        </button>
									</div>
                        `;

                });
                countIdColor++;
                countField++;
                if (countIdColor > 6) {
                    countIdColor = 0;
                }
                data_field_child_table = data_field_child_table_new;
                data_field_table = [...data_field_table, ...data_field_child_table];
            });

        }


        DATA_fieldsTableFilterDatatable = data_field_table;
        conLog('DATA_fieldsTableFilterDatatable', DATA_fieldsTableFilterDatatable);
        let num_header = 0;
        let header_table = [];
        let ui_header_table = ``;

        // # CREATE HEADER TABLE
        $('#DATA_filter-datatable-list-field').empty();
        $('.field-show-header').empty();
        conLog('data_field_table', data_field_table)
        let arr_field = [];
        data_field_table.forEach(item => {
            arr_field[num_header] = item;
            let val_visibility = false;
            if (item.visibility_data_field != 'block') {
                if (item.visibility_data_field == 'show') {
                    val_visibility = true;
                }
                ui_header_table += `<th data-orderable="false" class="no-sort">
                                            ${item.description_field} 
                                            <div class="float-end"> 
                                                <button onclick="DATA_filterDataTable('${tableId}','${item.code_field}')" class="btn btn-sm btn-outline-primary"> 
                                                    <i class="icon-copy bi bi-funnel float-end"></i>
                                                </button> 
                                                <button onclick="DATA_sortDataTable('${tableId}',${num_header})"  class="btn btn-sm btn-outline-primary"> 
                                                    <i class="bi bi-filter"></i>
                                                </button>
                                            </div>
                                        </th>`;


                let data_header = {
                    render: function(data, type, row) {
                        if (row[item.code_field] === null || row[item.code_field] === undefined) {
                            return '';
                        } else {
                            // return row[item.code_field];
                            return `   <div class="row-datatable-${toUUID(row[item.code_field])}">
                                            ${DATA_createCellByTypeData(item.code_field, row[item.code_field])}
                                        </div>
                                    `
                        }
                    },
                    visible: val_visibility
                };

                header_table.push(data_header);

                // ===========================================================================
                //== Filter Table
                $('#DATA_filter-datatable-list-field').append(
                    `<button  onclick="DATA_filterDataTable('${tableId}','${item.code_field}')"  class="btn btn-outline-primary">${item.description_field}</button>`
                );
                //== Filter Table
                // ===========================================================================
                num_header++;
            }
        });
        conLog('arr_field', arr_field)
        $('.field-show-header').append(header_table_support);
        let text_actionButtonTable = `<th data-orderable="false" class="no-sort">AKSI</th>`;
        ui_header_table += text_actionButtonTable;


        let actionButtonTable = {
            render: function(data, type, row) {
                return `
                        <a data-value="${toUUID(row[detail_data_table['primary_table']])}" href="#" onclick="DATA_dataShow('${toUUID(row[detail_data_table['primary_table']])}')">
                            <div class="btn btn-sm btn-outline-warning"><i class="icon-copy bi bi-arrow-up-right-square"></i> </div>
                        </a>

                        <a href="#" onclick="DATA_dataDelete('${row[detail_data_table['primary_table']]}')">
                            <div class="btn btn-sm btn-outline-danger">  <i class="icon-copy bi bi-trash"></i> </div>
                        </a>
                    `;
            }
        };
        header_table.push(actionButtonTable);

        if (header_table.length === 0) {
            console.error('Header table kosong — tidak ada kolom yang dapat ditampilkan.');
            return;
        }



        $('#datatable-' + tableId + '-wrapper').empty();
        let element_datatable = `
            <table id="datatable-${tableId}" class="display cell-border" style="width:100%">
                <thead>
                ${ui_header_table}
                </thead>
            </table>
        `;
        $('#datatable-' + tableId + '-wrapper').append(element_datatable);

        conLog('DATA_initDataDatatable - datasetTable:', datasetTable);
        // Siapkan opsi dasar DataTable
        let options = {
            data: datasetTable,
            columns: header_table,
            paging: false,
        };

        // Jika DATA_paggingDatatable true → tambahkan scroll
        if (!DATA_paggingDatatable) {
            options.scrollY = '600px';
            options.scrollX = true;
        }

        // Inisialisasi DataTable
        let table = $('#datatable-' + tableId).DataTable(options);

        DATA_initializeFilters();
    }


    function DATA_sortDataTable(tableId, columnIndex) {
        let table = $('#datatable-' + tableId).DataTable();
        let currentOrder = table.order(); // Ambil urutan saat ini
        let newOrder;

        if (currentOrder.length > 0 && currentOrder[0][0] === columnIndex) {
            // Jika kolom yang dipilih sudah diurutkan, ubah arah sorting (asc/desc)
            newOrder = [
                [columnIndex, currentOrder[0][1] === 'asc' ? 'desc' : 'asc']
            ];
        } else {
            // Jika belum diurutkan, set default sorting ke ascending
            newOrder = [
                [columnIndex, 'asc']
            ];
        }

        // Terapkan sorting baru dan redraw tabel
        table.order(newOrder).draw();
    }

    function DATA_confirmDeleteData() {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success margin-5',
            cancelButtonClass: 'btn btn-danger margin-5',
            buttonsStyling: false
        }).then(function(result) {
            if (result.value == true) {
                $.ajax({
                    url: '/api/database/form/deletedatadatatable',
                    type: 'POST', // Ganti ke 'DELETE' jika API kamu pakai metode DELETE
                    data: {
                        DATA_GROUP_DATA: DATA_GROUP_DATA,
                        code_table: DATA_TABLE_ID,
                        code_data: DATA_DELETE_ID_CODE_DATA
                    },
                    success: function(response) {
                        console.log('Data deleted successfully:', response);

                        let table = $(`#datatable-${DATA_TABLE_ID}`).DataTable();
                        let id_code_data = DATA_DELETE_ID_CODE_DATA;

                        // =============================
                        // 🔹 1️⃣ Hapus dari db.database_tables
                        // =============================
                        conLog('Deleting from db.database_tables:', id_code_data);
                        if (db?.database_tables?.[id_code_data]) {
                            conLog('Deleting from db.database_tables:', db['database_tables'][
                                id_code_data
                            ]);
                            delete db['database_tables'][id_code_data];
                        }

                        // =============================
                        // 🔹 2️⃣ Hapus dari DATA_datasetDatatable
                        // =============================
                        if (Array.isArray(DATA_datasetDatatable)) {
                            let beforeLength = DATA_datasetDatatable.length;
                            DATA_datasetDatatable = DATA_datasetDatatable.filter(item => item
                                .code_table !==
                                id_code_data);
                        }

                        // =============================
                        // 🔹 3️⃣ Hapus dari dataset filter (DATA_dataSetFilter, DATA_originalDataSet, DATA_filteredData)
                        // =============================
                        if (Array.isArray(DATA_dataSetFilter)) {
                            DATA_dataSetFilter = DATA_dataSetFilter.filter(item => item
                                .code_table !==
                                id_code_data);
                        }
                        if (Array.isArray(DATA_originalDataSet)) {
                            DATA_originalDataSet = DATA_originalDataSet.filter(item => item
                                .code_table !==
                                id_code_data);
                        }
                        if (Array.isArray(DATA_filteredData)) {
                            DATA_filteredData = DATA_filteredData.filter(item => item
                                .code_table !==
                                id_code_data);
                        }

                        // =============================
                        // 🔹 4️⃣ Update DataTable tampilan
                        // =============================
                        let rowIndex = table.rows().indexes().filter(function(index) {
                            let rowData = table.row(index).data();
                            return rowData && rowData.code_table === id_code_data;
                        });

                        if (rowIndex.length > 0) {
                            table.row(rowIndex[0]).remove().draw(false);
                        }
                        setDatabase("DATABASE", db)
                            .then(() => conLog("💾 IndexedDB updated for key DATABASE"))
                            .catch(err => console.error("❌ Failed to update IndexedDB:", err));

                        // =============================
                        // 🔹 5️⃣ Update ulang opsi filter
                        // =============================
                        if (typeof updateAvailableFilters === "function") {
                            DATA_updateAvailableFilters(); // perbarui opsi yang tersisa
                        }
                        if (typeof showFilterStatus === "function") {
                            DATA_showFilterStatus(); // tampilkan status filter baru
                        }
                        swal('Deleted!', 'Your data has been deleted.', 'success');
                    },

                    error: function(xhr) {
                        console.error('Error deleting data:', xhr.responseText);
                        errorModalSweet();
                    }
                });

            }


            if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your data is safe :)',
                    'error'
                );
            }
        })
    }

    function DATA_dataDelete(id_code_data) {
        DATA_DELETE_ID_CODE_DATA = id_code_data;
        return false;
        DATA_confirmDeleteData();
    }

    function DATA_dataShow(id_code_data) {
        console.log('FUNCTION :::  == DATA_dataShow - id_code_data:', id_code_data);
        resetForm(DATA_CODE_TABLE);
        let detail_data_table = db['database_tables'][DATA_CODE_TABLE];
        let data_datatable_detail = db['database_tables'][DATA_CODE_TABLE]['data'][id_code_data];
        let first_data_datatable_detail = data_datatable_detail[detail_data_table['primary_table']];
        $(`#${DATA_CODE_TABLE}-uuid_data`).val(first_data_datatable_detail.uuid_data);

        Object.entries(data_datatable_detail).forEach(([key, value]) => {
            $('#' + DATA_CODE_TABLE + '-' + key).val(value.value_data).trigger(
                'change'); // Trigger change event for select2
        });

        let uuid_parent = first_data_datatable_detail['uuid_data'];

        $(`#${DATA_CODE_TABLE}-${detail_data_table['primary_table']}`).prop('disabled', true);


        if (hasChild) {
            // Lakukan sesuatu jika ada child
            db['database_tables_child'][DATA_CODE_TABLE].forEach(table_id_child => {
                let data_datatable_child_detail =
                    db['database_tables'][table_id_child]?.['data']?.[id_code_data] ?? null;

                conLog('table_id_child', table_id_child)


                let first_data_datatable_child_detail = detail_data_table['primary_table'];
                $(`#${table_id_child}-uuid_data`).val(uuid_parent);
                $(`#${table_id_child}-uuid_data`).val(uuid_parent);
                if (!data_datatable_child_detail) {
                    console.warn(
                        `No child data found for table ${table_id_child} and code data ${id_code_data}`);
                    return; // Lewati iterasi ini jika tidak ada data child
                } else {
                    conLog('data_datatable_child_detail', data_datatable_child_detail);
                    Object.entries(data_datatable_child_detail).forEach(([key, value]) => {
                        $('#' + table_id_child + '-' + key).val(value.value_data).trigger(
                            'change'); // Trigger change event for select2
                    });
                }
                $(`#${table_id_child}-${detail_data_table['primary_table']}`).val(id_code_data);
                $(`#${table_id_child}-${detail_data_table['primary_table']}`).prop('disabled', true);

            });
        }


    }

    function toggleColumnAuto(tableId, columnIndex) {
        const table = $('#' + tableId).DataTable(); // ambil instance aktif

        // Ambil status saat ini
        const isVisible = DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] == 'show' ? true :
            false;
        if (DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] == 'show' ||
            DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] == 'hide') {
            DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] = isVisible ? 'hide' : 'show';
        }


        conLog('isVisible', tableId)
        // table.column(columnIndex).visible();

        // Toggle tampil / sembunyi
        table.column(columnIndex).visible(!isVisible);

        // Ubah ikon bila ada
        const icon = document.getElementById(`toggle-column-${DATA_TABLE_ID}-${columnIndex}`);
        if (icon) {
            icon.className = isVisible ? 'show-field bi bi-eye-slash' : 'show-field bi bi-eye';
        }

        console.log(
            `Kolom ke-${columnIndex} pada #datatable-${tableId} sekarang ${
            !isVisible ? 'tampil ✅' : 'disembunyikan ❌'
        }`
        );
    }

    function toggleShowField() {
        isShowAllField = !isShowAllField;
        if (isShowAllField) {
            $('.show-field').removeClass('bi-eye-slash').addClass('bi bi-eye'); // show all

        } else {
            $('.show-field').removeClass('bi-eye').addClass('bi bi-eye-slash'); // hide all
        }
    }

    function storeFieldShow() {
        let arr_checkbox_filter = [];
        let obj_checkbox_filter = [];
        var checkboxValues = $('.field-show:checked').map(function() {
            let code_field = $(this).val();
            let code_table_field = $(`#code_table_field-${code_field}`).val();
            arr_checkbox_filter.push(db['db']['database_field'][code_table_field][code_field]);
            obj_checkbox_filter[code_field] = db['db']['database_field'][code_table_field][code_field];
        }).get();
        conLog('arr_checkbox_filter', arr_checkbox_filter);
        conLog('obj_checkbox_filter', obj_checkbox_filter);
        $.ajax({
            url: '/api/mbg/manage/database/store-template',
            type: "POST",
            headers: {
                'x-auth-login': ui_dataset.ui_dataset.user_authentication.auth_login
            },
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                'show-fields': arr_checkbox_filter,
                'code_table_get': $('#id-code_table').val(),
            },
        }).done(function(response) {
            conLog('respone', response)
            db['db']['table_show_template'][ui_dataset.ui_dataset.user_authentication.employee_uuid][$(
                '#id-code_table').val()] = response.data
            refreshSession();
            actionCard($('#id-code_table').val());
        }).fail(function(response) {
            conLog('error', response);
            stopLoading();
            //alertModal()
        });

    }
</script>

<script>
    let DATA_currentFilterField = null;
    let DATA_popupTable = null;

    // -------------------------
    // Tampilkan modal filter
    // -------------------------
    function DATA_filterDataTable(tableId, fieldId) {
        DATA_currentFilterField = fieldId;
        conLog('DATA_filterDataTable - fieldId:', fieldId);
        conLog('DATA_filterDataTable - tableId:', tableId);
        const data = DATA_availableFilters[fieldId] || [];

        // Inisialisasi DataTable hanya sekali
        if (!$.fn.DataTable.isDataTable('#DATA_popupTable')) {
            DATA_popupTable = $('#DATA_popupTable').DataTable({
                columns: [{
                        data: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description'
                    }
                ],
                paging: false,
                destroy: true
            });
        }

        // Isi ulang data tabel
        const tableData = data.map(value => ({
            select: `<input type="checkbox" onchange="DATA_applySelectedFilters()" class="filter-check" value="${value}" ${isChecked(fieldId, value) ? 'checked' : ''}>`,
            description: value
        }));

        DATA_popupTable.clear();
        DATA_popupTable.rows.add(tableData);
        DATA_popupTable.draw();

        $('#DATA_filter-datatable-modal').modal('show');
    }

    // -------------------------
    // Event button dan modal
    // -------------------------
    function DATA_initFilterModalEvents() {
        // Check All
        $('#DATA_checkAll').off('click').on('click', function() {
            $('#DATA_popupTable').find('.filter-check').prop('checked', true);
        });

        // Uncheck All
        $('#DATA_uncheckAll').off('click').on('click', function() {
            $('#DATA_popupTable').find('.filter-check').prop('checked', false);
        });

        // Apply button
        $('#DATA_applyFilterBtn').off('click').on('click', function() {
            DATA_applySelectedFilters();
        });

        // Apply juga saat modal ditutup
        $('#DATA_filter-datatable-modal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            DATA_applySelectedFilters();
        });
    }

    // -------------------------
    // Terapkan filter
    // -------------------------
    function DATA_applySelectedFilters() {
        if (!DATA_currentFilterField) return;

        const selectedValues = [];
        $('#DATA_popupTable').find('.filter-check:checked').each(function() {
            selectedValues.push($(this).val());
        });

        DATA_applyFilter(DATA_currentFilterField, selectedValues);

        DATA_initDataDatatable(DATA_TABLE_ID, DATA_fieldsTableFilterDatatable, DATA_dataSetFilter,
            DATA_paggingDatatable);
    }

    // -------------------------
    // Ceklis aktif sebelumnya
    // -------------------------
    function isChecked(field, value) {
        const active = DATA_activeFilters[field];
        return active && active.includes(value);
    }

    // -------------------------
    // Inisialisasi saat dokumen siap
    // -------------------------
    $(document).ready(function() {
        DATA_initFilterModalEvents();
    });
</script>


<script>
    // ################ FILTER ################

    // Dataset global
    let DATA_originalDataSet = []; // backup data awal (tidak berubah)
    let DATA_filteredData = []; // hasil filter terakhir
    let DATA_activeFilters = {}; // filter aktif
    let DATA_availableFilters = {}; // opsi yang bisa dipilih saat ini
    let DATA_allFilterOptions = {}; // opsi asli dari data awal (tidak difilter)
    let DATA_filterableFields = []; // field yang bisa difilter

    // 🔹 Ambil nilai unik dari field tertentu
    function DATA_getUniqueValues(source, field) {
        if (!Array.isArray(source) || source.length === 0) return [];
        return [...new Set(source.map(item => item[field]))];
    }

    // 🔹 Ambil hanya field yang bukan visibility_data_field: 'block'
    function DATA_getFilterableFields() {
        return DATA_fieldsTableFilterDatatable
            .filter(f => f.visibility_data_field !== 'block')
            .map(f => f.code_field);
    }

    // 🔹 Inisialisasi awal filter (dipanggil sekali setelah data dimuat)
    function DATA_initializeFilters() {
        if (!Array.isArray(DATA_dataSetFilter) || DATA_dataSetFilter.length === 0) {
            console.warn("⚠️ DATA_dataSetFilter kosong — tidak bisa inisialisasi filter");
            return;
        }

        // Tentukan field yang akan digunakan untuk filter
        DATA_filterableFields = DATA_getFilterableFields();
        conLog('DATA_filterableFields', DATA_filterableFields)

        // Simpan backup data asli jika belum pernah disimpan
        if (DATA_originalDataSet.length === 0) {
            DATA_originalDataSet = [...DATA_dataSetFilter];
        }

        DATA_filteredData = [...DATA_dataSetFilter];

        // Simpan semua opsi asli untuk setiap field filterable
        DATA_allFilterOptions = {};
        DATA_filterableFields.forEach(field => {
            DATA_allFilterOptions[field] = DATA_getUniqueValues(DATA_originalDataSet, field);
        });

        DATA_updateAvailableFilters();
        DATA_showFilterStatus();
    }

    // 🔹 Terapkan filter berdasarkan field dan value
    function DATA_applyFilter(field, value) {
        if (!Array.isArray(DATA_originalDataSet) || DATA_originalDataSet.length === 0) {
            console.warn("⚠️ Data asli kosong — applyFilter diabaikan");
            return [];
        }

        // Hapus filter jika kosong
        if (!value || (Array.isArray(value) && value.length === 0)) {
            delete DATA_activeFilters[field];
        } else {
            DATA_activeFilters[field] = value;
        }

        // Filter dari original dataset agar hasil selalu konsisten
        DATA_filteredData = DATA_originalDataSet.filter(item => {
            return Object.entries(DATA_activeFilters).every(([key, filterValue]) => {
                if (!DATA_filterableFields.includes(key)) return true; // skip kolom non-filterable
                if (Array.isArray(filterValue)) {
                    return filterValue.includes(item[key]);
                } else {
                    return item[key] === filterValue;
                }
            });
        });

        // Sinkronkan hasil ke dataset aktif
        DATA_dataSetFilter = [...DATA_filteredData];

        // Perbarui opsi filter yang masih tersedia
        DATA_updateAvailableFilters();

        DATA_showFilterStatus();
        return DATA_filteredData;
    }

    // 🔹 Update daftar filter yang masih bisa dipilih
    function DATA_updateAvailableFilters() {
        if (!Array.isArray(DATA_dataSetFilter) || DATA_dataSetFilter.length === 0) {
            DATA_availableFilters = {};
            return;
        }

        DATA_availableFilters = {};

        // Hanya perbarui untuk field yang diizinkan
        DATA_filterableFields.forEach(field => {
            // Jika sudah aktif, gunakan opsi asli (biar tetap bisa dicentang ulang)
            if (DATA_activeFilters[field]) {
                DATA_availableFilters[field] = DATA_allFilterOptions[field] || [];
            } else {
                // Jika belum aktif, hanya tampilkan opsi dari hasil filter
                DATA_availableFilters[field] = DATA_getUniqueValues(DATA_dataSetFilter, field);
            }
        });
    }

    // 🔹 Reset semua filter dan kembalikan ke data asli
    function DATA_resetFilters() {
        if (!Array.isArray(DATA_originalDataSet) || DATA_originalDataSet.length === 0) return;

        DATA_activeFilters = {};
        DATA_filteredData = [...DATA_originalDataSet];
        DATA_dataSetFilter = [...DATA_originalDataSet];

        // Kembalikan daftar opsi ke semua opsi asli
        DATA_availableFilters = JSON.parse(JSON.stringify(DATA_allFilterOptions));

        DATA_showFilterStatus();

        // Re-render tabel (opsional)
        DATA_initDataDatatable(DATA_TABLE_ID, DATA_fieldsTableFilterDatatable, DATA_dataSetFilter,
            DATA_paggingDatatable);
    }

    // 🔹 Debug status filter di console
    function DATA_showFilterStatus() {
        // console.group("🔍 STATUS FILTER");
        // console.log("🧭 Filter aktif:", DATA_activeFilters);
        // console.log("🎯 Filter tersedia:", DATA_availableFilters);
        // console.log("📚 Semua opsi asli:", DATA_allFilterOptions);
        // console.log("📊 Jumlah data aktif:", DATA_dataSetFilter.length);
        // console.log("🧩 Field yang difilter:", DATA_filterableFields);
        // console.groupEnd();
    }

    
</script>
