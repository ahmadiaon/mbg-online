<script>
    /*
    // =========================
    // DATATABLE & FILTER OLD
    // =========================

    // Event: Checkbox filter berubah
    $(document).on('change', '.filter-nama', function() {
        const valDataColIndex = $(this).data('col-index');
        // Hapus field dari indexUnFiltered jika ada perubahan pada kolom tersebut
        delete indexUnFiltered[valDataColIndex];

        const $all = $(`#filterTable-column-${valDataColIndex} .filter-nama`);
        const $checked = $all.filter(':checked');
        $(`#filterTable-column-${valDataColIndex} .pilih-semua-filter-nama`).prop('checked', $all.length > 0 &&
            $checked.length === $all.length);


        let filters = {};
        // Loop semua kolom filter
        $('[id^="filterTable-column-"]').each(function() {
            const colIndex = $(this).attr('id').replace('filterTable-column-', '');
            let checked = [];
            $(this).find('.filter-nama:checked').each(function() {
                checked.push($(this).val());
            });
            filters[colIndex] = checked;

            // Jika tidak ada yang dicentang, tambahkan kembali field yang pernah terhapus (jika belum ada)
            if (checked.length === 0 && !(colIndex in indexUnFiltered)) {
                let field = indexUnFilteredBase[colIndex];
                if (field) {
                    indexUnFiltered[colIndex] = field;
                }
            }
        });

        // Terapkan filter ke DataTable utama
        const mainTable = $('#datatable-Group-Form').DataTable();
        Object.keys(filters).forEach(function(colIdx) {
            if (filters[colIdx].length > 0) {
                mainTable.column(colIdx).search(filters[colIdx].join('|'), true, false);
            } else {
                mainTable.column(colIdx).search('');
            }
        });

        // Update DataTable filter kolom yang belum diubah
        mainTable.draw();
        const filteredDataSet = mainTable.rows({
            search: 'applied'
        }).data().toArray();

        Object.entries(indexUnFiltered).forEach(([colIndex, field]) => {
            // Ambil nilai unik dari kolom yang sedang difilter
            const uniqueValues = [...new Set(filteredDataSet.map(d => d[field.code]))];

            // Buat ulang data filter dengan checkbox
            const filterData = uniqueValues.map(val => ({
                pilih: `<input type="checkbox" class="filter-nama" value="${val}" data-col-index="${colIndex}">`,
                nama: val
            }));

            // Update DataTable filter kolom
            const filterTable = $('#filterTable-column-' + colIndex).DataTable();
            filterTable.clear();
            filterTable.rows.add(filterData).draw();
        });
    });

    // Event: Checkbox field tampil diubah
    $(document).on('change', '.pilih-field-tampil-checkbox', function() {
        // Ambil kode field yang diubah
        const fieldCode = $(this).val();
        // Cari index field di dataFieldTable
        const idx = dataFieldTable.findIndex(f => f.code === fieldCode);
        if (idx === -1) return;

        // Update visibility di dataFieldTable
        dataFieldTable[idx].visible = this.checked ? 'show' : 'hide';

        // Re-render DataTable utama agar kolom tampil sesuai visibility terbaru
        // writeDatatableFormOnly();
    });


    // update pilih semua 

    // Update "Pilih Semua" checkbox status whenever any checkbox changes
    $(document).on('change', '.pilih-field-tampil-checkbox', function() {
        const $checkboxes = $('#filterTable-column-field-tampil .pilih-field-tampil-checkbox');
        const $checked = $checkboxes.filter(':checked');
        $('#pilih-semua-field-tampil').prop('checked', $checkboxes.length > 0 && $checked.length === $checkboxes
            .length);
    });

    // Initial update after DataTable draw (for search/filter)
    $(document).on('draw.dt', function(e, settings) {
        if (settings.nTable && settings.nTable.id === 'filterTable-column-field-tampil') {
            const $checkboxes = $('#filterTable-column-field-tampil .pilih-field-tampil-checkbox');
            const $checked = $checkboxes.filter(':checked');
            $('#pilih-semua-field-tampil').prop('checked', $checkboxes.length > 0 && $checked.length ===
                $checkboxes.length);
        }
    });

    $(document).on('change', '.pilih-semua-filter-nama', function() {
        const colIndex = $(this).data('col-index');
        const checked = this.checked;
        $(`#filterTable-column-${colIndex} .filter-nama`).prop('checked', checked).trigger('change');
    });

    $(document).on('shown.bs.collapse', '.collapse', function() {
        $(this).find('table.data-table').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().columns.adjust();
            }
        });
    });


    // =========================
    // FUNGSI UTAMA: MEMBUAT DATATABLE & FILTER
    // =========================
    let dataSet;
    // Untuk filter kolom
    let indexUnFiltered = {};
    let indexUnFilteredBase = {};
    let isFirst = true;
    let from_table_form = {};
    let gabungan_field = {};
    let persetujuan = {};
    let type_data_fields;

    let dataFieldTable = [{
            order: 0,
            code: 'code_table',
            label: 'Kode Tabel',
            visible: 'block'
        },
        {
            order: 4,
            code: 'parent_table',
            label: 'Tabel Utama',
            visible: 'show'
        },
        {
            order: 2,
            code: 'primary_table',
            label: 'Field Primary',
            visible: 'hidden'
        },
        {
            order: 1,
            code: 'menu_table',
            label: 'Group Form',
            visible: 'show'
        },
        {
            order: 5,
            code: 'description_table',
            label: 'Deskripsi',
            visible: 'show'
        }
    ];

    async function writeDatatableFormOnly(dataDatatable = []) {
        conLog('function call', 'writeDatatableFormOnly');
        // return false;
        // Ambil data dari database
        dataDatatable = await getDataDatabaseAsync(2, ['database_table']);
        dataSet = Object.values(dataDatatable);
        // Urutkan header


        // Urutkan header
        dataFieldTable.sort((a, b) => a.order - b.order);

        // Generate header HTML dan kolom DataTable
        let headerHtml = '';
        let columns = [];
        let colIndex = 0;

        $('#header_table').empty();
        $('#filter-columns').empty();
        if (isFirst) {
            // Membuat Bagian Pilih Field Tampil
            // Membuat data untuk DataTable: setiap field jadi satu baris dengan checkbox dan label
            const dataFieldTampil = dataFieldTable
                .filter(field => field.visible !== 'block')
                .map((field, idx) => ({
                    pilih: `<input type="checkbox" class="pilih-field-tampil-checkbox" value="${field.code}" data-idx="${idx}"
                        ${field.visible === 'show' ? 'checked' : '' }>`,
                    label: field.label
                }));

            // Render tabel pilih field tampil
            $(`.body-modal-tampil-datatable`).append(`
                <div class="index-field-tampil mb-2">
                    <input type="text" id="search-field-tampil" class="form-control mb-2" placeholder="Cari Field..." />
                    <table id="filterTable-column-field-tampil" class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="pilih-semua-field-tampil" />
                                    Pilih
                                </th>
                                <th>
                                    Field Tampil
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                `);

            // Event: Pilih semua checkbox
            $(document).off('change', '#pilih-semua-field-tampil').on('change', '#pilih-semua-field-tampil',
                function() {
                    const checked = this.checked;
                    $('#filterTable-column-field-tampil .body-modal-tampil-datatable-checkbox').prop('checked',
                            checked)
                        .trigger('change');
                });

            // Event: Search field tampil
            $(document).off('keyup', '#search-field-tampil').on('keyup', '#search-field-tampil', function() {
                $('#filterTable-column-field-tampil').DataTable().search(this.value).draw();
            });

            // DataTable untuk field tampil
            $('#filterTable-column-field-tampil').DataTable({
                data: dataFieldTampil,
                columns: [{
                        data: 'pilih',
                        orderable: false
                    },
                    {
                        data: 'label'
                    }
                ],
                paging: false,
                searching: true,
                info: false,
                scrollY: '200px',
                scrollCollapse: true
            });


        }

        $(`#body-modal-filter-datatable`).append(`
                <div class="faq-wrap">
                    <div id="filter-faq">
                        
                    </div>
                </div>
            `);




        dataFieldTable.forEach(field => {
            if (field.visible != 'block') {
                // Header kolom
                headerHtml += `
                    <th data-orderable="false" class="no-sort">
                        ${field.label}
                        <div>
                            <button onclick="filterDatatable(${colIndex})" class="btn btn-sm btn-outline-primary">
                                <i class="icon-copy bi bi-funnel"></i>
                            </button>
                            <button onclick="orderTable(${colIndex})" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </th>
                    `;
                // Definisi kolom DataTable
                columns.push({
                    data: field.code,
                    visible: field.visible === 'show',
                    render: (data) => data ?? ''
                });




                if (isFirst) {

                    // Modal filter (select2)
                    $(`#filter-faq`).append(`
                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-block collapsed" data-toggle="collapse" data-target="#faq-${colIndex}">
                                    ${field.label}
                                    </button>
                                </div>
                                <div id="faq-${colIndex}" class="collapse" data-parent="#filter-faq">
                                    <table id="filterTable-column-${colIndex}" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" class="pilih-semua-filter-nama" data-col-index="${colIndex}" />
                                                    Pilih
                                                </th>
                                                <th>${field.label}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        `);
                    // Ambil nilai unik dari kolom
                    const uniqueNama = [...new Set(dataSet.map(d => d[field.code]))];

                    // Buat data filter dengan checkbox
                    const filterData = uniqueNama.map(nama => ({
                        pilih: `<input type="checkbox" class="filter-nama" value="${nama}" data-col-index="${colIndex}">`,
                        nama: nama
                    }));

                    // DataTable untuk filter kolom
                    const filterTable = $('#filterTable-column-' + colIndex).DataTable({
                        data: filterData,
                        columns: [{
                                data: 'pilih',
                                orderable: false
                            },
                            {
                                data: 'nama'
                            }
                        ],
                        paging: false,
                        searching: true,
                        info: false,
                        scrollY: '200px',
                        scrollCollapse: true
                    });

                    // Simpan field ke indexUnFiltered dan indexUnFilteredBase
                    indexUnFiltered[colIndex] = field;
                    indexUnFilteredBase[colIndex] = field;
                }

                colIndex++;
            }

        });
        isFirst = false;
        // Kolom aksi
        columns.push({
            data: 'id',
            render: function(data, type, row) {
                return `
                        <a href="#" onclick="tableDetail('${row.code_table}')">
                            <div class="btn btn-sm btn-outline-warning"><i class="icon-copy bi bi-arrow-up-right-square"></i></div>
                        </a>
                        <a href="#" onclick="dataDelete('${row.code_table}')">
                            <div class="btn btn-sm btn-outline-danger"><i class="icon-copy bi bi-trash"></i></div>
                        </a>
                    `;
            }
        });
        headerHtml += `<th data-orderable="false" class="no-sort">AKSI</th>`;

        // Render header
        $('#header_table').empty().append(headerHtml);

        // Inisialisasi DataTable utama
        if ($.fn.DataTable.isDataTable('#datatable-Group-Form')) {
            $('#datatable-Group-Form').DataTable().clear().destroy();
        }

        $('#datatable-Group-Form').DataTable({
            data: dataSet,
            columns: columns,
            columnDefs: [{
                orderable: false,
                targets: [0]
            }],
            createdRow: function(row, data, dataIndex) {
                // Misal, gunakan data.id atau data.code_table sebagai penanda unik
                $(row).attr('data-row-key', 'row-' + data
                    .code_table); // atau data.id sesuai field unik kamu
            }
        });

        // Inisialisasi select2 untuk filter
        $(".multi-filter").select2({
            placeholder: "Pilih...",
            width: "100%",
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $("#modal-filter-datatable")
        });
    }

    // =========================
    // INISIALISASI SAAT LOAD
    // =========================

     let variableDatatable = {};
    async function writeDatatableGeneral(idTable) {
        // Ambil data dari database
        
            1. Get data DataTable
            2. Persiapkan element HTML datatable sesuai id datatable-table
            3. Gunakan DataTable untuk menampilkan data

              // kebutuhan form:
                - fields
                - fields_tampil
            
              // kebutuhan data:
                - s
                - database_data 
        
        // 1. Ambil data DataTable
        variableDatatable['database_table'] = await getDataDatabaseAsync(2, ['database_table', idTable]);
        variableDatatable['database_data'] = await getDataDatabaseAsync(2, ['database_data', idTable]);
        variableDatatable['database_data'] = await getDataDatabaseAsync(2, ['database_data', idTable]);

        conLog('variableDatatable', variableDatatable);

        dataSet = Object.values(variableDatatable['database_table']);

        // Urutkan header
        dataFieldTable.sort((a, b) => a.order - b.order);

        // Generate header HTML dan kolom DataTable
        let headerHtml = '';
        let columns = [];
        let colIndex = 0;

        $('#header_table').empty();
        $('#filter-columns').empty();
        if (isFirst) {
            // Membuat Bagian Pilih Field Tampil
            // Membuat data untuk DataTable: setiap field jadi satu baris dengan checkbox dan label
            const dataFieldTampil = dataFieldTable
                .filter(field => field.visible !== 'block')
                .map((field, idx) => ({
                    pilih: `<input type="checkbox" class="pilih-field-tampil-checkbox" value="${field.code}" data-idx="${idx}"
                        ${field.visible === 'show' ? 'checked' : '' }>`,
                    label: field.label
                }));

            // Render tabel pilih field tampil
            $(`.body-modal-tampil-datatable`).append(`
                <div class="index-field-tampil mb-2">
                    <input type="text" id="search-field-tampil" class="form-control mb-2" placeholder="Cari Field..." />
                    <table id="filterTable-column-field-tampil" class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="pilih-semua-field-tampil" />
                                    Pilih
                                </th>
                                <th>
                                    Field Tampil
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                `);

            // Event: Pilih semua checkbox
            $(document).off('change', '#pilih-semua-field-tampil').on('change', '#pilih-semua-field-tampil',
                function() {
                    const checked = this.checked;
                    $('#filterTable-column-field-tampil .body-modal-tampil-datatable-checkbox').prop('checked',
                            checked)
                        .trigger('change');
                });


            // DataTable untuk field tampil
            $('#filterTable-column-field-tampil').DataTable({
                data: dataFieldTampil,
                columns: [{
                        data: 'pilih',
                        orderable: false
                    },
                    {
                        data: 'label'
                    }
                ],
                paging: false,
                info: false,
                scrollY: '200px',
                scrollCollapse: true
            });


        }

        $(`#body-modal-filter-datatable`).append(`
                <div class="faq-wrap">
                    <div id="filter-faq">
                        
                    </div>
                </div>
            `);




        dataFieldTable.forEach(field => {
            if (field.visible != 'block') {
                // Header kolom
                headerHtml += `
                    <th data-orderable="false" class="no-sort">
                        ${field.label}
                        <div>
                            <button onclick="filterDatatable(${colIndex})" class="btn btn-sm btn-outline-primary">
                                <i class="icon-copy bi bi-funnel"></i>
                            </button>
                            <button onclick="orderTable(${colIndex})" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </th>
                    `;
                // Definisi kolom DataTable
                columns.push({
                    data: field.code,
                    visible: field.visible === 'show',
                    render: (data) => data ?? ''
                });




                if (isFirst) {

                    // Modal filter (select2)
                    $(`#filter-faq`).append(`
                            <div class="card">
                                <div class="card-header">
                                    <button class="btn btn-block collapsed" data-toggle="collapse" data-target="#faq-${colIndex}">
                                    ${field.label}
                                    </button>
                                </div>
                                <div id="faq-${colIndex}" class="collapse" data-parent="#filter-faq">
                                    <table id="filterTable-column-${colIndex}" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" class="pilih-semua-filter-nama" data-col-index="${colIndex}" />
                                                    Pilih
                                                </th>
                                                <th>${field.label}</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        `);
                    // Ambil nilai unik dari kolom
                    const uniqueNama = [...new Set(dataSet.map(d => d[field.code]))];

                    // Buat data filter dengan checkbox
                    const filterData = uniqueNama.map(nama => ({
                        pilih: `<input type="checkbox" class="filter-nama" value="${nama}" data-col-index="${colIndex}">`,
                        nama: nama
                    }));

                    // DataTable untuk filter kolom
                    const filterTable = $('#filterTable-column-' + colIndex).DataTable({
                        data: filterData,
                        columns: [{
                                data: 'pilih',
                                orderable: false
                            },
                            {
                                data: 'nama'
                            }
                        ],
                        paging: false,
                        searching: true,
                        info: false,
                        scrollY: '200px',
                        scrollCollapse: true
                    });

                    // Simpan field ke indexUnFiltered dan indexUnFilteredBase
                    indexUnFiltered[colIndex] = field;
                    indexUnFilteredBase[colIndex] = field;
                }

                colIndex++;
            }

        });
        isFirst = false;
        // Kolom aksi
        columns.push({
            data: 'id',
            render: function(data, type, row) {
                return `
                        <a href="#" onclick="tableDetail('${row.code_table}')">
                            <div class="btn btn-sm btn-outline-warning"><i class="icon-copy bi bi-arrow-up-right-square"></i></div>
                        </a>
                        <a href="#" onclick="dataDelete('${row.code_table}')">
                            <div class="btn btn-sm btn-outline-danger"><i class="icon-copy bi bi-trash"></i></div>
                        </a>
                    `;
            }
        });
        headerHtml += `<th data-orderable="false" class="no-sort">AKSI</th>`;

        // Render header
        $('#header_table').empty().append(headerHtml);

        // Inisialisasi DataTable utama
        if ($.fn.DataTable.isDataTable('#datatable-Group-Form')) {
            $('#datatable-Group-Form').DataTable().clear().destroy();
        }

        $('#datatable-Group-Form').DataTable({
            data: dataSet,
            columns: columns,
            columnDefs: [{
                orderable: false,
                targets: [0]
            }],
            createdRow: function(row, data, dataIndex) {
                // Misal, gunakan data.id atau data.code_table sebagai penanda unik
                $(row).attr('data-row-key', data.code_table); // atau data.id sesuai field unik kamu
            }
        });

        // Inisialisasi select2 untuk filter
        $(".multi-filter").select2({
            placeholder: "Pilih...",
            width: "100%",
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $("#modal-filter-datatable")
        });

    }

    async function deleteDataDatabaseAsync(isRefresh, arrKey) {
        if (isRefresh >= 1) {
            const response = await $.ajax({
                url: '/api/database/delete-data-form',
                type: "POST",
                headers: {
                    'x-auth-login': 'token-auth',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    arrKey,
                    isRefresh
                }
            });
            let valueToSet = response.data;
            conLog('delete data row', 'row-' + arrKey[1]);
            $('#datatable-Group-Form').DataTable()
                .row($('[data-row-key="row-' + arrKey[1] + '"]'))
                .remove()
                .draw();
            closeModal();
            return valueToSet;
        } else {
            return arrKey.reduce((acc, key) => acc?.[key], db['db']);
        }
    }
    */
</script>
