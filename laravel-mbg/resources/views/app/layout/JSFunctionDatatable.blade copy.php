<script>
    // =========================
    // FUNGSI UMUM
    // =========================
    let dataSetFilter = [];
    let fieldsTableFilterDatatable = [];
    let datasetDatatable = [];
    let TABLE_ID = null;
    let GROUP_DATA = 'database_datas';
    let DELETE_ID_CODE_DATA = "";

    // Fungsi untuk menginisialisasi DataTable
    function initDataTable(tableId, data_field_table = null, datasetTable = null, paggingDatatable = true, primary_key_field = 'code_table') {
        conLog('FUNCTIOM ======', 'initDataTable');
        dataSetFilter = datasetTable;
        // Mengurutkan berdasarkan sort_field (dikonversi ke angka)
        data_field_table.sort((a, b) => Number(a.sort_field) - Number(b.sort_field));
        conLog('data_field_table', data_field_table)


        let num_header = 0;
        let header_table = [];
        let ui_header_table = ``;

        // # CREATE HEADER TABLE
        $('#filter-datatable-list-field').empty();
        data_field_table.forEach(item => {
            // conLog('item field', item)
            let val_visibility = false;
            if (item.visibility_data_field != 'block') {
                if (item.visibility_data_field == 'show') {
                    val_visibility = true;
                }
                ui_header_table += `<th data-orderable="false" class="no-sort">
                                            ${item.description_field} 
                                            <div class="float-end"> 
                                                <button onclick="filterDataTable('${tableId}','${item.code_data}')" class="btn btn-sm btn-outline-primary"> 
                                                    <i class="icon-copy bi bi-funnel float-end"></i>
                                                </button> 
                                                <button onclick="sortDataTable('${tableId}',${num_header})"  class="btn btn-sm btn-outline-primary"> 
                                                    <i class="bi bi-filter"></i>
                                                </button>
                                            </div>
                                        </th>`;


                let data_header = {
                    data: item.code_data,
                    render: function(data, type, row) {
                        return data;
                    },
                    visible: val_visibility
                };

                header_table.push(data_header);

                // ===========================================================================
                //== Filter Table
                $('#filter-datatable-list-field').append(
                    `<button  onclick="filterDataTable('${tableId}','${item.code_data}')"  class="btn btn-outline-primary">${item.description_field}</button>`
                );
                //== Filter Table
                // ===========================================================================
                num_header++;
            }
        });
        let text_actionButtonTable = `<th data-orderable="false" class="no-sort">AKSI</th>`;
        ui_header_table += text_actionButtonTable;


        let actionButtonTable = {
            render: function(data, type, row) {
                // conLog('row action', row);
                return `
                        <a href="#" onclick="dataShow('${row[primary_key_field]}')">
                            <div class="btn btn-sm btn-outline-warning"><i class="icon-copy bi bi-arrow-up-right-square"></i> </div>
                        </a>

                        <a href="#" onclick="dataDelete('${row[primary_key_field]}')">
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

        // Siapkan opsi dasar DataTable
        let options = {
            data: datasetTable,
            columns: header_table,
            paging: paggingDatatable,
        };

        // Jika paggingDatatable true → tambahkan scroll
        if (paggingDatatable) {
            options.scrollY = '400px';
            options.scrollX = true;
        }

        // Inisialisasi DataTable
        let table = $('#datatable-' + tableId).DataTable(options);

        initializeFilters();
    }


    function sortDataTable(tableId, columnIndex) {
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

    function confirmDeleteData() {
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
                        group_data: GROUP_DATA,
                        code_table: TABLE_ID,
                        code_data: DELETE_ID_CODE_DATA
                    },
                    success: function(response) {
                        console.log('Data deleted successfully:', response);

                        let table = $(`#datatable-${TABLE_ID}`).DataTable();
                        let id_code_data = DELETE_ID_CODE_DATA;

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
                        // 🔹 2️⃣ Hapus dari datasetDatatable
                        // =============================
                        if (Array.isArray(datasetDatatable)) {
                            let beforeLength = datasetDatatable.length;
                            datasetDatatable = datasetDatatable.filter(item => item.code_table !==
                                id_code_data);
                        }

                        // =============================
                        // 🔹 3️⃣ Hapus dari dataset filter (dataSetFilter, originalDataSet, filteredData)
                        // =============================
                        if (Array.isArray(dataSetFilter)) {
                            dataSetFilter = dataSetFilter.filter(item => item.code_table !==
                                id_code_data);
                        }
                        if (Array.isArray(originalDataSet)) {
                            originalDataSet = originalDataSet.filter(item => item.code_table !==
                                id_code_data);
                        }
                        if (Array.isArray(filteredData)) {
                            filteredData = filteredData.filter(item => item.code_table !==
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
                            updateAvailableFilters(); // perbarui opsi yang tersisa
                        }
                        if (typeof showFilterStatus === "function") {
                            showFilterStatus(); // tampilkan status filter baru
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

    function dataDelete(id_code_data) {
        DELETE_ID_CODE_DATA = id_code_data;
        confirmDeleteData();
    }


    function freezeColumn(tableId, colIndex) {
        const table = document.getElementById(tableId);
        if (!table) {
            console.warn(`❌ Table #${tableId} tidak ditemukan`);
            return;
        }

        const rows = table.querySelectorAll('tr');
        if (!rows.length) return;

        // Hitung posisi left untuk kolom ini
        const firstRow = rows[0];
        let leftOffset = 0;

        for (let i = 0; i < colIndex; i++) {
            if (firstRow.cells[i]) leftOffset += firstRow.cells[i].offsetWidth;
        }

        // Terapkan freeze pada semua sel di kolom tersebut
        rows.forEach(row => {
            const cell = row.cells[colIndex];
            if (cell) {
                cell.classList.add('sticky-col');
                cell.style.position = 'sticky';
                cell.style.left = `${leftOffset}px`;
                cell.style.background = '#fff';
                cell.style.zIndex = row.parentElement.tagName === 'THEAD' ? 3 : 2;
            }
        });
        //change icon
        const freezeIcon = document.getElementById(`freeze-${colIndex}`);
        // freezeIcon.classList.add('freezeColumn');
    }

    function toggleFreezeColumn(tableId, colIndex) {
        const table = document.getElementById(tableId);
        if (!table) {
            console.warn(`❌ Table #${tableId} tidak ditemukan`);
            return;
        }

        const rows = table.querySelectorAll('tr');
        if (!rows.length) return;

        const firstRow = rows[0];
        let leftOffset = 0;

        for (let i = 0; i < colIndex; i++) {
            if (firstRow.cells[i]) leftOffset += firstRow.cells[i].offsetWidth;
        }

        const alreadyFrozen = firstRow.cells[colIndex]?.classList.contains('sticky-col');

        rows.forEach(row => {
            const cell = row.cells[colIndex];
            if (!cell) return;

            if (alreadyFrozen) {
                // Unfreeze
                cell.classList.remove('sticky-col');
                cell.style.position = '';
                cell.style.left = '';
                cell.style.background = '';
                cell.style.zIndex = '';
            } else {
                // Freeze
                cell.classList.add('sticky-col');
                cell.style.position = 'sticky';
                cell.style.left = `${leftOffset}px`;
                cell.style.background = '#fff';
                cell.style.zIndex = row.parentElement.tagName === 'THEAD' ? 3 : 2;
            }
        });

        // Ganti ikon
        const icon = document.getElementById(`freeze-${colIndex}`);
        if (icon) {
            if (alreadyFrozen) {
                icon.className = 'frozen icon-copy bi bi-snow'; // Unfreeze
                icon.title = 'Freeze';
            } else {
                icon.className = 'frozen icon-copy bi bi-snow2'; // Freeze
                icon.title = 'Unfreeze';
            }
        }
    }


    function createFormTable(tableCode) {
        const container = $(".accordion");
        container.empty(); // bersihkan isi accordion
        let tableData = db['database_tables'][tableCode];
        // 🟢 CEK APAKAH TABEL PUNYA CHILD
        const hasChild = (db['database_tables_child'][tableCode]) ? true : false;
        conLog('hasChild', hasChild);
        $('.description_table').text(tableData.description_table); //header name form
        let formFieldHTML = createFormFieldTable(tableCode);
        let faqHTML = '';
        faqHTML = `
                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-block" data-toggle="collapse" data-target="#faq-${tableCode}">
                                        ${tableData.description_table}
                                    </button>
                                </div>
                                <div id="faq-${tableCode}" class="collapse show" data-parent="#accordion-form-group">
                                    <div class="card-body">
                                        <form id="form-table-detail-${tableCode}">
                                            <input type="text" id="${tableCode}-uuid_data" name="uuid_data" value="" class="form-group uuid_data_value ${tableCode}">
                                            ${formFieldHTML}
                                            <div class="d-flex justify-content-end gap-2">
                                                <button onclick="resetForm('${tableCode}')" type="button" class="btn btn-warning me-2">Reset</button>
                                                <button onclick="storeDataTableDatabase('${tableCode}')" type="button" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `;
        $('#accordion-form-group').html(faqHTML);
        if (db['database_tables_child'][tableCode]) {
            // ADA CHILD TABLE
            let tableChilds = db['database_tables_child'][tableCode];
            conLog('tableChilds', tableChilds);



            tableChilds.forEach(tableChild => {
                tableData = db['database_tables'][tableChild];
                conLog('tbales', tableData);
                formFieldHTML = createFormFieldTable(tableChild);
                faqHTML = `
                                    <div class="card">
                                        <div class="card-header">
                                            <button class="btn btn-block collapsed" data-toggle="collapse" data-target="#faq-${tableChild}">
                                                ${tableData.description_table}
                                            </button>
                                        </div>
                                        <div id="faq-${tableChild}" class="collapse" data-parent="#accordion-form-group">
                                            <div class="card-body">
                                                <form id="form-table-detail-${tableChild}">
                                                    <input type="text" id="${tableChild}-uuid_data" name="uuid_data" value="" class="form-group ${tableCode}">
                                                    ${formFieldHTML}
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-warning me-2">Reset</button>
                                                        <button type="button" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                        `;
                $('#accordion-form-group').append(faqHTML);
            });
        }
        $(`.custom-select2`).select2();
    }

    function createFormFieldTable(tableCode) {
        const dataTable = db['database_tables'][tableCode];
        const dataTableField = db['database_tables'][tableCode]?.['fields'];

        conLog('dataTableField', dataTableField);

        if (!dataTableField) {
            console.error(`❌ Field untuk table '${tableCode}' tidak ditemukan.`);
            return '';
        }
        let formField = '';

        for (const key in dataTableField) {
            conLog('dataTableField',key)
            if (!dataTableField.hasOwnProperty(key)) continue;

            const field = dataTableField[key];
            conLog('dataTableField',field)
            const {
                code_table_field,
                code_field,
                description_field,
                type_data_field,
                full_code_field,
                option_field = [],
            } = field;

            // Atur atribut disabled (bisa disesuaikan nanti)
            let is_disabled = '';
            // if (field.level_data_field > 1 && ui_dataset.ui_dataset.user_authentication.FIELD_LEVEL < 3) {
            //     is_disabled = 'disabled';
            // }

            // Buat atribut umum agar tidak diulang di tiap case
            const baseAttr =
                `${is_disabled} name="${code_field}" id="${full_code_field}" class="form-control ${tableCode}"`;


            switch (type_data_field.toUpperCase()) {
                case 'TEXT':
                    formField += `
                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" ${baseAttr} >
                                    <label for="${full_code_field}">${description_field}</label>
                                </div>
                            </div>`;
                    break;

                case 'NUMBER':
                    formField += `
                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="number" ${baseAttr} >
                                    <label for="${full_code_field}">${description_field}</label>
                                </div>
                            </div>`;
                    break;

                case 'DATE':
                    formField += `
                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="date" ${baseAttr} >
                                    <label for="${full_code_field}">${description_field}</label>
                                </div>
                            </div>`;
                    break;

                case 'SELECT':
                    const options = Array.isArray(option_field) && option_field.length > 0 ?
                        option_field.map(opt => `<option value="${opt.value}">${opt.label}</option>`).join('') :
                        `<option value="">-- Pilih ${description_field} --</option>`;

                    formField += `
                            <div class="form-group">
                                <label for="${full_code_field}" class="form-label">${description_field}</label>
                                <select ${baseAttr} class="form-select">
                                    ${options}
                                </select>
                            </div>`;
                    break;

                case 'TEXTAREA':
                    formField += `
                            <div class="form-group">
                                <label for="${full_code_field}" class="form-label">${description_field}</label>
                                <textarea ${baseAttr}  rows="3"></textarea>
                            </div>`;
                    break;

                case 'CHECKBOX':
                    formField += `
                            <div class="form-check mb-2">
                                <input type="checkbox" ${baseAttr} class="form-check-input">
                                <label class="form-check-label" for="${full_code_field}">${description_field}</label>
                            </div>`;
                    break;

                case 'RADIO':
                    if (Array.isArray(option_field) && option_field.length > 0) {
                        const radios = option_field.map(opt => `
                                <div class="form-check form-check-inline">
                                    <input type="radio" ${is_disabled} class="form-check-input"
                                        name="${code_field}"
                                        id="${full_code_field}-${opt.value}"
                                        value="${opt.value}">
                                    <label class="form-check-label" for="${full_code_field}-${opt.value}">
                                        ${opt.label}
                                    </label>
                                </div>`).join('');

                        formField += `
                                <div class="form-group mb-2">
                                    <label class="form-label d-block">${description_field}</label>
                                    ${radios}
                                </div>`;
                    }
                    break;
                case 'DARI-TABEL':
                    const dataTableField_data_source = dataTableField[code_field]?.['data_source'];
                    // conLog('dataTableField_data_source', dataTableField_data_source)
                    let element_option_data_source = '';

                    if (dataTableField_data_source) {
                        let data_source = dataTableField_data_source;
                        let code_table_data_source = data_source['table_data_source'];
                        let field_get_data_source = data_source['field_get_data_source'];

                        let data_options = db['database_tables'][code_table_data_source]['data'];
                        if (!data_options) {
                            // conLog(`Data source '${code_table_data_source}' tidak ditemukan.`);
                            formField += `
                                            <div class="form-group">
                                                <div class="form-floating mb-3">
                                                    <input type="text" ${baseAttr} >
                                                    <label for="${full_code_field}">${description_field}</label>
                                                </div>
                                            </div>`;
                        } else {
                            // conLog(`Data source '${code_table_data_source}' ditemukan.`, data_options);
                            Object.entries(data_options).forEach(([key, value]) => {
                                element_option_data_source +=
                                    `<option value="${key}">${value[field_get_data_source]['text_data']}</option>`
                            });



                            formField += `
                                        <div class="form-group">
                                            <div class=" form-floating">
                                                <label>${description_field}</label>
                                                <select ${is_disabled} style="width: 100%;" name="${code_field}" id="${full_code_field}" class="${code_field} ${tableCode} custom-select2 form-control">
                                                    <option value="">${description_field}</option>
                                                    ${element_option_data_source}
                                                </select>
                                            </div>
                                        </div>
                                        `;
                        }



                    }

                    break;


                default:
                    // fallback ke text
                    formField += `
                            <div class="form-group">
                                <div class="form-floating mb-3">
                                    <input type="text" ${baseAttr} >
                                    <label for="${full_code_field}">${description_field}</label>
                                </div>
                            </div>`;
                    break;
            }

            // $(`#${id_field}`).append(formField);
            // $(`.custom-select2`).select2();
        }
        return formField;
    }
</script>

<script>
    let currentFilterField = null;
    let popupTable = null;

    // -------------------------
    // Tampilkan modal filter
    // -------------------------
    function filterDataTable(tableId, fieldId) {
        currentFilterField = fieldId;

        const data = availableFilters[fieldId] || [];

        // Inisialisasi DataTable hanya sekali
        if (!$.fn.DataTable.isDataTable('#popupTable')) {
            popupTable = $('#popupTable').DataTable({
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
            select: `<input type="checkbox" onchange="applySelectedFilters()" class="filter-check" value="${value}" ${isChecked(fieldId, value) ? 'checked' : ''}>`,
            description: value
        }));

        popupTable.clear();
        popupTable.rows.add(tableData);
        popupTable.draw();

        $('#filter-datatable-modal').modal('show');
    }

    // -------------------------
    // Event button dan modal
    // -------------------------
    function initFilterModalEvents() {
        // Check All
        $('#checkAll').off('click').on('click', function() {
            $('#popupTable').find('.filter-check').prop('checked', true);
        });

        // Uncheck All
        $('#uncheckAll').off('click').on('click', function() {
            $('#popupTable').find('.filter-check').prop('checked', false);
        });

        // Apply button
        $('#applyFilterBtn').off('click').on('click', function() {
            applySelectedFilters();
        });

        // Apply juga saat modal ditutup
        $('#filter-datatable-modal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            applySelectedFilters();
        });
    }

    // -------------------------
    // Terapkan filter
    // -------------------------
    function applySelectedFilters() {
        if (!currentFilterField) return;

        const selectedValues = [];
        $('#popupTable').find('.filter-check:checked').each(function() {
            selectedValues.push($(this).val());
        });

        applyFilter(currentFilterField, selectedValues);
        conLog('applySelectedFilters fieldsTableFilterDatatable', fieldsTableFilterDatatable)
        initDataTable('tableDatatable', fieldsTableFilterDatatable, dataSetFilter);
    }

    // -------------------------
    // Ceklis aktif sebelumnya
    // -------------------------
    function isChecked(field, value) {
        const active = activeFilters[field];
        return active && active.includes(value);
    }

    // -------------------------
    // Inisialisasi saat dokumen siap
    // -------------------------
    $(document).ready(function() {
        initFilterModalEvents();
    });
</script>


<script>
    // ################ FILTER ################

    // Dataset global
    let originalDataSet = []; // backup data awal (tidak berubah)
    let filteredData = []; // hasil filter terakhir
    let activeFilters = {}; // filter aktif
    let availableFilters = {}; // opsi yang bisa dipilih saat ini
    let allFilterOptions = {}; // opsi asli dari data awal (tidak difilter)
    let filterableFields = []; // field yang bisa difilter

    // 🔹 Ambil nilai unik dari field tertentu
    function getUniqueValues(source, field) {
        if (!Array.isArray(source) || source.length === 0) return [];
        return [...new Set(source.map(item => item[field]))];
    }

    // 🔹 Ambil hanya field yang bukan visibility_data_field: 'block'
    function getFilterableFields() {
        return fieldsTableFilterDatatable
            .filter(f => f.visibility_data_field !== 'block')
            .map(f => f.code_data);
    }

    // 🔹 Inisialisasi awal filter (dipanggil sekali setelah data dimuat)
    function initializeFilters() {
        if (!Array.isArray(dataSetFilter) || dataSetFilter.length === 0) {
            console.warn("⚠️ dataSetFilter kosong — tidak bisa inisialisasi filter");
            return;
        }
        conLog('dataSetFilter ', dataSetFilter);
        // Tentukan field yang akan digunakan untuk filter
        filterableFields = getFilterableFields();

        // Simpan backup data asli jika belum pernah disimpan
        if (originalDataSet.length === 0) {
            originalDataSet = [...dataSetFilter];
        }

        filteredData = [...dataSetFilter];

        // Simpan semua opsi asli untuk setiap field filterable
        allFilterOptions = {};
        filterableFields.forEach(field => {
            allFilterOptions[field] = getUniqueValues(originalDataSet, field);
        });

        updateAvailableFilters();
        showFilterStatus();
    }

    // 🔹 Terapkan filter berdasarkan field dan value
    function applyFilter(field, value) {
        if (!Array.isArray(originalDataSet) || originalDataSet.length === 0) {
            console.warn("⚠️ Data asli kosong — applyFilter diabaikan");
            return [];
        }

        // Hapus filter jika kosong
        if (!value || (Array.isArray(value) && value.length === 0)) {
            delete activeFilters[field];
        } else {
            activeFilters[field] = value;
        }

        // Filter dari original dataset agar hasil selalu konsisten
        filteredData = originalDataSet.filter(item => {
            return Object.entries(activeFilters).every(([key, filterValue]) => {
                if (!filterableFields.includes(key)) return true; // skip kolom non-filterable
                if (Array.isArray(filterValue)) {
                    return filterValue.includes(item[key]);
                } else {
                    return item[key] === filterValue;
                }
            });
        });

        // Sinkronkan hasil ke dataset aktif
        dataSetFilter = [...filteredData];

        // Perbarui opsi filter yang masih tersedia
        updateAvailableFilters();

        showFilterStatus();
        return filteredData;
    }

    // 🔹 Update daftar filter yang masih bisa dipilih
    function updateAvailableFilters() {
        if (!Array.isArray(dataSetFilter) || dataSetFilter.length === 0) {
            availableFilters = {};
            return;
        }

        availableFilters = {};

        // Hanya perbarui untuk field yang diizinkan
        filterableFields.forEach(field => {
            // Jika sudah aktif, gunakan opsi asli (biar tetap bisa dicentang ulang)
            if (activeFilters[field]) {
                availableFilters[field] = allFilterOptions[field] || [];
            } else {
                // Jika belum aktif, hanya tampilkan opsi dari hasil filter
                availableFilters[field] = getUniqueValues(dataSetFilter, field);
            }
        });
    }

    // 🔹 Reset semua filter dan kembalikan ke data asli
    function resetFilters() {
        if (!Array.isArray(originalDataSet) || originalDataSet.length === 0) return;

        activeFilters = {};
        filteredData = [...originalDataSet];
        dataSetFilter = [...originalDataSet];

        // Kembalikan daftar opsi ke semua opsi asli
        availableFilters = JSON.parse(JSON.stringify(allFilterOptions));

        showFilterStatus();

        // Re-render tabel (opsional)
        initDataTable('tableDatatable', fieldsTableFilterDatatable, dataSetFilter);
    }

    // 🔹 Debug status filter di console
    function showFilterStatus() {
        console.group("🔍 STATUS FILTER");
        console.log("🧭 Filter aktif:", activeFilters);
        console.log("🎯 Filter tersedia:", availableFilters);
        console.log("📚 Semua opsi asli:", allFilterOptions);
        console.log("📊 Jumlah data aktif:", dataSetFilter.length);
        console.log("🧩 Field yang difilter:", filterableFields);
        console.groupEnd();
    }
</script>
