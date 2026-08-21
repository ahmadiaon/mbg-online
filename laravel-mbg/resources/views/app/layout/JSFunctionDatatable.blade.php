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
    let datatableIdElement = '';


    let showIcon = 'show-field icon-copy bi bi-eye';
    let hideIcon = 'show-field icon-copy bi bi-eye-slash';
    let freezeIcon = 'frozen icon-copy bi bi-snow2';
    let unfreezeIcon = 'frozen icon-copy bi bi-snow';




    // Fungsi untuk menginisialisasi DataTable
    function initDataTable(arrParameter) {
        conLog('FUNCTIOM ====== initDataTable', arrParameter);
        let ON_FILTER = getLocalStorage('FILTER-APP');
        let tableId = arrParameter.tableId || '';
        let tableDataDetails = arrParameter.tableDataDetails || null;
        let datasetTable = arrParameter.datasetTable || {};
        let paggingDatatable = arrParameter.paggingDatatable !== undefined ? arrParameter.paggingDatatable : true;
        let staticName = arrParameter.staticName || null;
        let header_table_support = '';
        datatableIdElement = staticName ? staticName : tableId;
        TABLE_ID = tableId;
        conLog('allDataFilter', allDataFilter);
        dataSetFilter = allDataFilter?.[TABLE_ID]?.dataSetFilter;
        conLog('dataSetFilter awal', dataSetFilter);
        conLog('db init', db);
        let idElementTable = arrParameter.staticName ? arrParameter.staticName : tableId;

        if (Array.isArray(dataSetFilter) && dataSetFilter.length >
            0) {

            // conLog('dataSetFilter on initial if', dataSetFilter);
            let table = $('#datatable-' + datatableIdElement).DataTable();

            table.clear();
            table.rows.add(dataSetFilter);
            table.draw(false); // tidak reset paging
            return false;
        }

        let primary_key_field = null;

        // if (tableDataDetails == null) {

        // }


        // conLog('tableDataDetails before sort', tableDataDetails);
        // Mengurutkan berdasarkan sort_field (dikonversi ke angka)
        if (tableDataDetails == null || !tableDataDetails['fields'] || tableDataDetails.fields.length === 0) {
            tableDataDetails = db['database_tables'][TABLE_ID]; // arrParameter['tableDataDetails'];
            primary_key_field = tableDataDetails['primary_table'];
            conLog('tableDataDetails on null', tableDataDetails)
        } else {
            primary_key_field = tableDataDetails['primary_table'];
            arrParameter['tableDataDetails'] = tableDataDetails;
            let new_data_dataset_object = {};
            Object.entries(tableDataDetails['data']).forEach(([key_nrp, data_array_dataset]) => {
                new_data_dataset_object[key_nrp] = data_array_dataset;
            });
            // conLog('new_data_dataset_object', new_data_dataset_object)
            tableDataDetails['data'] = new_data_dataset_object;
            if (!arrParameter['tableDataDetails']) {
                setDatabase('DATABASE', db);
            }

        }

        conLog('tableDataDetails before sort', tableDataDetails);


        // conLog('db new', db)
        // if (typeof tableDataDetails['fields'] == 'object') {
        //     tableDataDetails['fields'] = Object.values(tableDataDetails['fields']);
        // }
        // tableDataDetails['fields'].sort((a, b) => Number(a.sort_field) - Number(b.sort_field));

        hasChild = (db['database_tables_child'][tableId]) ? true : false;

        // conLog('tableDataDetails after sort', tableDataDetails);

        let tableDataDetails_temp = structuredClone(tableDataDetails);

        if (!tableDataDetails.data || tableDataDetails.data.length === 0) {
            // conLog('dataset null', tableDataDetails.data);
            if (hasChild) {
                tableDataDetails.data = arrParameter['tableDataDetails']['join_data'];
            } else {
                tableDataDetails.data = arrParameter['tableDataDetails']['data'];
            }
            // conLog('tableDataDetails.data NEW', tableDataDetails.data);

            let result = null;

        }
        if (tableDataDetails.data === undefined) {
            tableDataDetails.data = [];
        }
        // conLog('tableDataDetails temp', tableDataDetails_temp);

        // conLog('tableDataDetails done', tableDataDetails.data);
        let lengthField = tableDataDetails['fields'].length;
        let result_field = tableDataDetails['fields'];
        let data_new = tableDataDetails['data'];
        Object.values(tableDataDetails['fields']).forEach(item_dari_table => {
            if (item_dari_table.type_data_field == 'DARI-TABEL') {
                // TUJUAN MENAMBAH FIELD DARI TABEL YANG DI GET
                // Kecuali field yang di get
                // conLog('item_dari_table', item_dari_table);
                let data_source_field_add_field = item_dari_table.data_source;
                let table_data_source = db['database_tables'][data_source_field_add_field.table_data_source];
                // conLog('xxxxxxxxxxxxxxxxxx=table_data_source :'+data_source_field_add_field.table_data_source, table_data_source);


                Object.entries(tableDataDetails['data']).forEach(([key_data_datatable_dari_table,
                    data_datatable_dari_table
                ]) => {
                    // conLog('data_datatable_dari_table :' + key_data_datatable_dari_table,
                    //     data_datatable_dari_table);
                    // conLog('item_dari_table.code_field', item_dari_table.code_field);
                    // conLog('data_datatable_dari_table[item_dari_table.code_field][code_data]',
                    //     data_datatable_dari_table[
                    //         item_dari_table.code_field
                    //     ]['value_data']);
                    // conLog('table_data_source', table_data_source['data']);
                    let data_sandingan_dari_table = table_data_source['data'][data_datatable_dari_table[
                        item_dari_table.code_field
                    ]['value_data']];

                    // conLog('data_sandingan_dari_table ' + key_data_datatable_dari_table,
                    //     data_sandingan_dari_table)
                    if (data_sandingan_dari_table) {
                        const prefixedObj = Object.fromEntries(
                            Object.entries(data_sandingan_dari_table).map(([key, value]) => [
                                `${item_dari_table.code_field}-${key}`,
                                value
                            ])
                        );

                        let result = {
                            ...data_datatable_dari_table,
                            ...prefixedObj
                        };
                        data_new[key_data_datatable_dari_table] = result;
                    }




                    // conLog('result', result);
                    // conLog('data_new', data_new);
                });
                // conLog('data_new', data_new);


                let ObjNewField = {
                    ...table_data_source['fields']
                };
                conLog('ObjNewField', ObjNewField);
                delete ObjNewField[data_source_field_add_field.field_get_data_source];
                delete ObjNewField[table_data_source.primary_table];
                Object.keys(ObjNewField).forEach(key => {
                    ObjNewField[key] = {
                        ...ObjNewField[key],
                        visibility_data_field: 'hide',
                        code_field: `${item_dari_table.code_field}-${key}`
                    };
                });

                conLog('ObjNewField', ObjNewField);


                result_field = {
                    ...tableDataDetails['fields'],
                    ...Object.values(ObjNewField)
                };

                // conLog('result_field', result_field);
            }

            tableDataDetails['fields'] = result_field;
            tableDataDetails['data'] = data_new;
        });


        if (typeof tableDataDetails['fields'] == 'object') {
            tableDataDetails['fields'] = Object.values(tableDataDetails['fields']);
        }
        tableDataDetails['fields'].sort((a, b) => Number(a.sort_field) - Number(b.sort_field));

        // conLog('datatbale hasil join', tableDataDetails);

        if (tableDataDetails.data && tableDataDetails.menu_table != 'STAND-ALONE') {
            result = Object.values(tableDataDetails.data).map(fields =>
                Object.fromEntries(
                    Object.entries(fields).map(([field, value]) => [field, value.value_data])
                )
            );

            dataSetFilter = datasetTable = result;
        } else {
            dataSetFilter = datasetTable = Object.values(tableDataDetails.data);
        }

        let num_header = 0;
        let header_table = [];
        let ui_header_table = ``;

        // # CREATE HEADER TABLE
        $('#filter-datatable-list-field').empty();
        fieldsTableFilterDatatable[TABLE_ID] = tableDataDetails['fields'];




        let countIdColor = 0;
        let countField = 0;
        let table_field_datatable = [];

        // conLog('tableDataDetails ONE', tableDataDetails);
        tableDataDetails['fields'].forEach(item => {

            // conLog('item field', item)

            let val_visibility = false;
            if (item.visibility_data_field === 'show') {
                val_visibility = true;
            } else {
                val_visibility = false;
            }
            if (item.visibility_data_field !== 'block') {

                ui_header_table += `<th data-orderable="false" class="no-sort">
                                            ${item.description_field} 
                                            <div class="float-end"> 
                                                <button onclick="filterDataTable('${tableId}','${item.code_field}')" class="btn btn-sm btn-outline-primary"> 
                                                    <i class="icon-copy bi bi-funnel float-end"></i>
                                                </button> 
                                                <button onclick="sortDataTable('${tableId}',${num_header})"  class="btn btn-sm btn-outline-primary"> 
                                                    <i class="bi bi-filter"></i>
                                                </button>
                                            </div>
                                        </th>`;


                let iconShowField = showIcon;
                if (tableDataDetails['field_show']) {
                    if (!tableDataDetails['field_show'].includes(item.code_field)) {
                        if (item.visibility_data_field != 'block') {
                            item.visibility_data_field = 'hide';
                            iconShowField = hideIcon;
                        }
                    }
                }

                if (!val_visibility) {
                    iconShowField = hideIcon;
                }
                // INI UNTUK HEADER TABLE SUPPORT SHOW FILTER TOGGLE
                header_table_support += `
                                        <div class="btn-group mb-15">
                                            <button type="button" disabled class="btn btn-${COLOR_BOOTSTRAP[countIdColor]}">${item.description_field}</button>
                                            <button type="button" class="btn btn-light" >
                                                <i onclick="filterDataTable('${tableId}','${item.code_field}')" class="icon-copy bi bi-funnel  mr-10"></i>
                                                <i id="toggle-column-${tableId}-${num_header}" data-value="${item.visibility_data_field}" class="mr-10 ${iconShowField}" onclick="toggleColumnAuto('${tableId}', ${num_header})"></i>
                                                
                                                <i id="freeze-${num_header}" class=" ${unfreezeIcon}" onclick="toggleFreezeColumn('datatable-${tableId}', ${num_header})"></i>
                                            </button>
                                        </div>
                                    `;
                table_field_datatable.push({
                    field: item.code_field,
                    count: num_header
                });

                // INI UNTUK VALUE DATA
                let data_header = {
                    data: item.code_field,
                    render: function(data, type, row) {
                        // conLog('data',data); // ini adalah value_data per field


                        // conLog('toUUID(row[primary_key_field])',toUUID(row[primary_key_field]))
                        let data_to_row = tableDataDetails['data'][toUUID(row[primary_key_field])];
                        // conLog('data_to_row ' + row[primary_key_field], data_to_row)
                        if (data_to_row === undefined) {
                            // conLog('data_to_row ' + row[primary_key_field], db['database_tables'][
                            //     'KARYAWAN'
                            // ]['join_data'][toUUID(row[primary_key_field])])
                            // return 'errr'+data;

                        }
                        if (data == null) {
                            return '';
                        }

                        if (item.code_field == 'NRP' || item.code_field == 'nrp' || item.code_field ==
                            'employee_uuid') {
                            item.type_data_field = 'DARI-TABEL';

                        }

                        switch (item.type_data_field) {
                            case 'DARI-TABEL':
                                // conLog('dataaaaaaaaaaaaa', data);
                                if (item?.data_source?.table_data_source == 'KARYAWAN' || item
                                    .code_field == 'nrp' || item.code_field == 'employee_uuid' || item
                                    .code_field == 'NRP') {
                                    // if(){}
                                    let data_KARYAWAN = db['database_tables']['KARYAWAN']['join_data'][
                                        toUUID(data)
                                    ];
                                    if (data_KARYAWAN === undefined) {
                                        return toUUID(data);
                                    }
                                    // conLog('data_KARYAWAN', data_KARYAWAN);
                                    let bgIsActive = (data_KARYAWAN?.['STATUS']?.['value_data'] ==
                                        'AKTIF') ? '' : 'secondary';
                                    return `<div class="name-avatar  bg-${bgIsActive}  d-flex align-items-center pr-2 pl-2 card-box w-100">
                                                <div class="avatar mr-2 flex-shrink-0">
                                                    <img src="/assets/vendors/images/photo5.jpg"
                                                        class="border-radius-100 box-shadow"
                                                        width="50" height="50" alt="">
                                                </div>

                                                <div class="flex-grow-1" style="min-width:0;">
                                                    <!-- BADGE KANAN ATAS -->
                                                    <div class="d-flex justify-content-end mb-0">
                                                        <span class="badge badge-pill badge-primary mb-0 lh-1">
                                                            ${data_KARYAWAN['PROJECT']?.['text_data'] ?? ""} |
                                                            ${data_KARYAWAN['DEPARTEMEN']?.['text_data'] ?? ""} |
                                                            ${data_KARYAWAN['DIVISI']?.['text_data'] ?? ""}
                                                        </span>
                                                    </div>

                                                    <!-- NAMA -->
                                                    <div class="font-14 weight-600 mb-0 lh-sm">
                                                        ${data_KARYAWAN['NAMA-KARYAWAN']['text_data']}
                                                    </div>

                                                    <!-- NRP -->
                                                    <div class="font-12 weight-500 mb-0 lh-sm">
                                                        <span class="badge badge-pill badge-primary mb-0 lh-1">
                                                            ${data_KARYAWAN['PERUSAHAAN']?.['text_data'] ?? ""}
                                                        </span>
                                                        ${data_KARYAWAN['NRP']?.['text_data'] ?? ""}
                                                    </div>

                                                    <!-- JABATAN -->
                                                    <div class="font-12 weight-500 text-muted mb-0 lh-sm">
                                                        ${data_KARYAWAN['JABATAN']?.['text_data'] ?? ""}
                                                    </div>
                                                </div>
                                            </div>

                                        `;
                                } else if (tableDataDetails_temp.menu_table == 'STAND-ALONE') {
                                    let data_source_field = item.data_source;
                                    if
                                    // mengambil data dari db berdasarkan data_source
                                    (data_source_field) {
                                        let code_table_data_source = data_source_field[
                                            'table_data_source'];
                                        let field_get_data_source = data_source_field[
                                            'field_get_data_source'];
                                        let data_options =
                                            db['database_tables'][code_table_data_source]?.join_data ??
                                            db['database_tables'][code_table_data_source]?.data;
                                        // conLog('data_options', data_options);
                                        if (data_options) {
                                            let data_from_source = data_options[toUUID(data)];
                                            if (data_from_source) {
                                                return data_from_source[field_get_data_source][
                                                    'text_data'
                                                ];
                                            } else {
                                                return toUUID(data);
                                            }
                                        } else {
                                            return toUUID(data);
                                        }
                                    }
                                } else {
                                    if (!data_to_row?.[item.code_field]?.['text_data']) {
                                        return 'errv ' + data;
                                    }
                                    return data_to_row[item.code_field]['text_data'];
                                }
                                break;
                            case 'TEXT':
                                return `<div class="font-10 weight-300">
                                            ${data}
                                        </div>`;
                                break;
                            case 'DETAIL-ABSENSI':
                                // data.forEach(data_absensi => {

                                // });
                                // conLog('data_absensies', data);
                                return `<div class="font-10 weight-300">
                                            ${data}
                                        </div>`;
                                break;
                            case 'PDF':
                                // data = 'abcd.pdf'; // ini hanya untuk testing
                                let noPdf = data;//removePdf(data);
                                return `
                                        <div class="d-inline-flex">
                                            <div class="font-10 weight-300">
                                                <a href="#" onclick="openPdf('${tableId}','${noPdf}')">
                                                    <div class="btn btn-sm btn-outline-warning mr-1">
                                                        <i class="icon-copy bi bi-file-pdf"></i>
                                                    </div>
                                                </a>
                                                <a href="#" onclick="openPdfNewTab('${tableId}','${noPdf}')">
                                                    <div class="btn btn-sm btn-outline-warning mr-1">
                                                        <i class="icon-copy bi bi-file-arrow-up"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>`;
                                break;
                            case 'STATUS-RECRUITMENT':
                                let color_status = 'outline-secondary'
                                if (data == 'Disimpan') {
                                    color_status = 'primary';
                                } else if (data == 'Ditolak') {
                                    color_status = 'danger';
                                } else if (data == 'Diterima') {
                                    color_status = 'success';
                                } else if (data == 'Proses Interview') {
                                    color_status = 'warning';
                                }
                                return `<div class="font-12 weight-500">
                                            <button class="btn btn-${color_status} btn-sm" >${data}</button>
                                        </div>`;
                                break;
                            case 'MONTH':
                                const monthNames = [
                                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                    "Juli", "Agustus", "September", "Oktober", "November",
                                    "Desember"
                                ];
                                let monthIndex = parseInt(data) - 1;
                                let monthName = monthNames[monthIndex] || data;
                                return `<div class="font-12 weight-500">
                                            ${monthName}
                                        </div>`;
                                break;
                            case 'ABSENSI': {

                                let year = ON_FILTER.year;
                                let month = ON_FILTER.month; // 1–12

                                let data_absensies = Object.values(row[item.code_field] ?? []);
                                let absenByDay = {};
                                let nrp = row['nrp'] || row['NRP'] || row['employee_uuid'] || '';
                                let count_absensi = {};

                                // INIT COUNT DARI DATABASE ABSENSI
                                Object.keys(db['database_tables']['DATABASE-ABSENSI']['data']).forEach(
                                    k => {
                                        count_absensi[k] = 0;
                                    });

                                // MAPPING DATA + HITUNG STATUS
                                data_absensies.forEach(item => {
                                    let day = parseInt(item.date.split('-')[2]);
                                    absenByDay[day] = item;

                                    let status = item.status_absen_uuid;
                                    if (count_absensi[status] !== undefined) {
                                        count_absensi[status]++;
                                    }
                                });

                                // JUMLAH HARI
                                let totalDays = new Date(year, month, 0).getDate();

                                // HARI PERTAMA BULAN
                                let firstDay = new Date(year, month - 1, 1).getDay();
                                firstDay = firstDay === 0 ? 7 : firstDay;

                                let calendarHtml = '';

                                // ================= BAR HEADER =================
                                // conLog(nrp, absenByDay);
                                let maxValue = Math.max(...Object.values(count_absensi), 1);
                                let headerBarHtml = '';
                                Object.entries(count_absensi).forEach(([key, value]) => {

                                    let warna = db['database_tables']['DATABASE-ABSENSI'][
                                            'data'
                                        ][key]
                                        ?.['WARNA-ABSENSI']?.['value_data'] ?? '#999';

                                    let widthPercent = (value / maxValue) * 100;
                                    if (value > 0) {
                                        headerBarHtml += `
                                        <div class="absen-bar">
                                            <div class="absen-label" style="background:${warna}">
                                                ${key}
                                            </div>

                                            <div class="absen-track">
                                                <div class="absen-fill"
                                                    style="width:${widthPercent}%; background:${warna}">
                                                </div>
                                            </div>

                                            <div class="absen-value">${value}</div>
                                        </div>
                                    `;
                                    }

                                });

                                // SPACER
                                for (let i = 1; i < firstDay; i++) {
                                    calendarHtml += `<div class="calendar-cell empty"></div>`;
                                }

                                // LOOP TANGGAL
                                for (let day = 1; day <= totalDays; day++) {

                                    let shift = '-';
                                    let status = '-';
                                    let time = '- | -';
                                    let late = 0;
                                    let work = 0;
                                    let bg = '#e9ecef';
                                    // conLog('absenByDay[day] : ' + day, absenByDay);

                                    if (absenByDay[day]) {
                                        const absen = absenByDay[day];

                                        shift = absen.shift ?? '-';
                                        status = absen.status_absen_uuid ?? '-';
                                        late = absen.late_points ?? 0;
                                        work = absen.working_hours ?? 0;

                                        if (absen.entry || absen.exit || absen.mid) {
                                            time =
                                                `${absen.entry ?? '-'} | ${absen.mid != null ? absen.mid + '|' : ''} ${absen.exit ?? '-'}`;
                                        }

                                        bg = db['database_tables']['DATABASE-ABSENSI']['data'][status]
                                            ?.['WARNA-ABSENSI']?.['value_data'] ?? bg;
                                    }

                                    calendarHtml += `
                                        <a href="#">
                                            <div class="absen-mini" id="${tableId}-absen-day-${nrp}-${day}">
                                                <div class="row-1">
                                                    <span class="shift">${shift}</span>
                                                    <span class="status" style="background:${bg}">${status}</span>
                                                    <span class="day">${day}</span>
                                                </div>

                                                <div class="row-2">${time}</div>

                                                <div class="row-3">
                                                    <span>L:${late}</span>
                                                    <span>W:${work}</span>
                                                </div>
                                            </div>
                                        </a>
                                    `;
                                }

                                // RETURN FINAL
                                return `
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="d-flex flex-wrap gap-1">
                                                    ${headerBarHtml}
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <div class="calendar-grid calendar-header">
                                                    <div class="day sen">Sen</div>
                                                    <div class="day sel">Sel</div>
                                                    <div class="day rab">Rab</div>
                                                    <div class="day kam">Kam</div>
                                                    <div class="day jum">Jum</div>
                                                    <div class="day sab">Sab</div>
                                                    <div class="day min">Min</div>
                                                </div>

                                                <div class="calendar-grid calendar-body">
                                                    ${calendarHtml}
                                                </div>
                                            </div>
                                        </div>`;

                            }
                            break;

                            default:
                                return `<div class="font-12 weight-500 text-muted">
                                ${item.type_data_field + ': ' + data}
                                                    </div>`;
                                break;
                        }

                    },
                    visible: item.visibility_data_field === 'show' ? true : false
                };

                header_table.push(data_header);

                // ===========================================================================
                //== Filter Table
                $('#filter-datatable-list-field').append(
                    `<button  onclick="filterDataTable('${tableId}','${item.code_field}')"  class="btn btn-outline-primary">${item.description_field}</button>`
                );
                //== Filter Table
                // ===========================================================================
                num_header++;
            }
        });

        conLog('table_field_datatable', table_field_datatable);
        $('.field-show-' + datatableIdElement).empty();
        $('.field-show-' + datatableIdElement).append(header_table_support);
        let text_actionButtonTable = `<th data-orderable="false" class="no-sort">AKSI</th>`;


        let actionButtonTable = {
            render: function(data, type, row, meta) {
                const index = meta.row;
                let dataShow_element = `<a href="#" onclick="dataShow('${tableId}','${toUUID(row[primary_key_field])}', ${index})">
                                <div class="btn btn-sm btn-outline-warning mr-1">
                                    <i class="icon-copy bi bi-arrow-up-right-square"></i>
                                </div>
                            </a>`;
                let delete_element = `<a href="#" onclick="dataDelete('${tableId}','${toUUID(row[primary_key_field])}')">
                                <div class="btn btn-sm btn-outline-danger">
                                    <i class="icon-copy bi bi-trash"></i>
                                </div>
                            </a>`;
                return `
                        <div class="d-inline-flex">
                            ${dataShow_element}

                            ${arrParameter?.isDeleteAction ? delete_element : ''}
                        </div>

                    `;
            }
        };
        if (arrParameter?.isAction !== false) {
            header_table.push(actionButtonTable);
            ui_header_table += text_actionButtonTable;
        }


        if (header_table.length === 0) {
            console.error('Header table kosong — tidak ada kolom yang dapat ditampilkan.');
            return;
        }
        conLog('datatableidelement', datatableIdElement);
        $('#datatable-' + datatableIdElement + '-wrapper').empty();
        // if (tableId == 'absensies') {
        //     conLog('tableDataDetails PERUSAHAAN', tableDataDetails);
        //     if(abc == false){
        //         return false;
        //     }
        //     // return false;
        // }
        let element_datatable = `
            <table id="datatable-${datatableIdElement}" class="display table table-striped table-bordered" style="width:100%">
                <thead>
                ${ui_header_table}
                </thead>
            </table>
        `;
        $('#datatable-' + datatableIdElement + '-wrapper').append(element_datatable);
        initializeFilters();
        // Siapkan opsi dasar DataTable
        // conLog('dataSetFilter to datatable LATEST', dataSetFilter);
        let options = {
            data: dataSetFilter,
            columns: header_table,
            paging: paggingDatatable,
        };

        // Jika paggingDatatable true → tambahkan scroll

        if (paggingDatatable) {} else {
            options.scrollY = '400px';
            options.scrollX = true;
        }

        // Inisialisasi DataTable
        conLog('options DataTable :' + datatableIdElement, datatableIdElement);
        let table = $('#datatable-' + datatableIdElement).DataTable(options);
    }

    function toggleColumnAuto(tableId, columnIndex) {


        // Ambil status saat ini
        const isVisible = $(`#toggle-column-${tableId}-${columnIndex}`).data('value') == 'show' ? true : false;
        console.log('isVisible', $(`#toggle-column-${tableId}-${columnIndex}`).data('value'));
        console.log(tableId, columnIndex);

        // if (DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] == 'show' ||
        //     DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] == 'hide') {
        //     DATA_fieldsTableFilterDatatable[columnIndex]['visibility_data_field'] = isVisible ? 'hide' : 'show';
        // }
        const table = $('#datatable-' + datatableIdElement).DataTable(); // ambil instance aktif
        // Toggle tampil / sembunyi
        table.column(columnIndex).visible(!isVisible);
        // Ubah ikon bila ada
        const icon = document.getElementById(`toggle-column-${tableId}-${columnIndex}`);
        icon.className = isVisible ? hideIcon : showIcon;
        $(`#toggle-column-${tableId}-${columnIndex}`).data('value', isVisible ? 'hide' : 'show');
    }


    function sortDataTable(tableId, columnIndex) {
        let table = $('#datatable-' + datatableIdElement).DataTable();
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
                        code_field: DELETE_ID_CODE_DATA
                    },
                    success: function(response) {
                        console.log('Data deleted successfully:', response);

                        let table = $(`#datatable-${datatableIdElement}`).DataTable();
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
                            datasetDatatable = datasetDatatable.filter(item => item
                                .code_table !==
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
        conLog('FUNCTION createFormTable ======= ' + tableCode, tableCode);
        const container = $(".accordion");
        container.empty(); // bersihkan isi accordion
        let tableData = db['database_tables'][tableCode];
        // conLog('tableData', tableData);
        // 🟢 CEK APAKAH TABEL PUNYA CHILD
        const hasChild = (db['database_tables_child'][tableCode]) ? true : false;
        // conLog('hasChild', hasChild);
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
            // conLog('tableChilds', tableChilds);



            tableChilds.forEach(tableChild => {
                tableData = db['database_tables'][tableChild];
                // conLog('tbales', tableData);
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

        conLog('function createFormFieldTable dataTableField :', dataTableField);

        if (!dataTableField) {
            console.error(`❌ Field untuk table '${tableCode}' tidak ditemukan.`);
            return '';
        }
        let formField = '';

        for (const key in dataTableField) {
            if (!dataTableField.hasOwnProperty(key)) continue;

            const field = dataTableField[key];

            let {
                code_table_field,
                code_field,
                data_source,
                description_field,
                type_data_field,
                full_code_field,
                option_field = [],
            } = field;
            // conLog('dataTableField : ' + key + ': ' + type_data_field, field)

            // Atur atribut disabled (bisa disesuaikan nanti)
            let is_disabled = '';
            // if (field.level_data_field > 1 && ui_dataset.ui_dataset.user_authentication.FIELD_LEVEL < 3) {
            //     is_disabled = 'disabled';
            // }

            // Buat atribut umum agar tidak diulang di tiap case
            const baseAttr =
                `${is_disabled} name="${code_field}" id="${full_code_field}" class="form-control ${tableCode}"`;

            type_data_field = type_data_field ?? 'TEXT';
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
                    const dataTableField_data_source = data_source;
                    let element_option_data_source = '';
                    if (dataTableField_data_source) {
                        let data_source = dataTableField_data_source;
                        let code_table_data_source = data_source['table_data_source'];
                        let field_get_data_source = data_source['field_get_data_source'];

                        let data_options =
                            db['database_tables'][code_table_data_source]?.join_data ??
                            db['database_tables'][code_table_data_source]?.data;
                        // conLog('data_options', data_options);
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

    function setValueFilter() {
        allDataFilter[TABLE_ID] = {
            originalDataSet: originalDataSet,
            filteredData: filteredData,
            activeFilters: activeFilters,
            availableFilters: availableFilters,
            allFilterOptions: allFilterOptions,
            filterableFields: filterableFields,
            dataSetFilter: dataSetFilter,
            datasetDatatable: datasetDatatable
        }
    }

    function getValueFilter() {

        originalDataSet = allDataFilter[TABLE_ID]['originalDataSet'];
        filteredData = allDataFilter[TABLE_ID]['filteredData'];
        activeFilters = allDataFilter[TABLE_ID]['activeFilters'];
        availableFilters = allDataFilter[TABLE_ID]['availableFilters'];
        allFilterOptions = allDataFilter[TABLE_ID]['allFilterOptions'];
        filterableFields = allDataFilter[TABLE_ID]['filterableFields'];
        dataSetFilter = allDataFilter[TABLE_ID]['dataSetFilter'];
        datasetDatatable = allDataFilter[TABLE_ID]['datasetDatatable'];
    }

    function filterDataTable(tableId, fieldId) {
        TABLE_ID = tableId;

        console.log('FUNCTION ====== filterDataTable');
        getValueFilter();

        conLog('data filter', (allDataFilter[TABLE_ID]));
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
        console.log('FUNCTION ====== applySelectedFilters');
        conLog('TABLE_ID', TABLE_ID);
        if (!currentFilterField) return;

        const selectedValues = [];
        $('#popupTable').find('.filter-check:checked').each(function() {
            selectedValues.push($(this).val());
        });

        applyFilter(currentFilterField, selectedValues);
        conLog('dataSetFilter', dataSetFilter);
        // return false;
        let dataArrParameter = {
            tableId: TABLE_ID,
        };
        initDataTable(dataArrParameter);
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

    let allDataFilter = {};



    // 🔹 Ambil nilai unik dari field tertentu
    function getUniqueValues(source, field) {
        if (!Array.isArray(source) || source.length === 0) return [];
        return [...new Set(source.map(item => item[field]))];
    }

    // 🔹 Ambil hanya field yang bukan visibility_data_field: 'block'
    function getFilterableFields() {
        return fieldsTableFilterDatatable[TABLE_ID]
            .filter(f => f.visibility_data_field !== 'block')
            .map(f => f.code_field);
    }

    // 🔹 Inisialisasi awal filter (dipanggil sekali setelah data dimuat)
    function initializeFilters() {

        originalDataSet = []; // backup data awal (tidak berubah)
        filteredData = []; // hasil filter terakhir
        activeFilters = {}; // filter aktif
        availableFilters = {}; // opsi yang bisa dipilih saat ini
        allFilterOptions = {}; // opsi asli dari data awal (tidak difilter)
        filterableFields = [];

        if (!Array.isArray(dataSetFilter) || dataSetFilter.length === 0) {
            console.warn("⚠️ dataSetFilter kosong — tidak bisa inisialisasi filter");
            return;
        }
        // conLog('dataSetFilter ', dataSetFilter);
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
        // conLog('originalDataSet', originalDataSet);

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
        setValueFilter();
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
        let dataArrParameter = {
            tableId: TABLE_ID,
        };
        initDataTable(dataArrParameter);
    }

    // 🔹 Debug status filter di console
    function showFilterStatus() {
        console.group("🔍 STATUS FILTER");
        console.log("📂 Tabel ID:", TABLE_ID);
        console.log("🗂️ Data asli (originalDataSet):", originalDataSet);
        console.log("🔄 Data terfilter (filteredData):", filteredData);
        console.log("✅ datasetfilter:", dataSetFilter);
        console.log("✅ fieldsTableFilterDatatable:", fieldsTableFilterDatatable);
        console.log("✅ datasetDatatable:", datasetDatatable);
        console.log("🧭 Filter aktif:", activeFilters);
        console.log("🎯 Filter tersedia:", availableFilters);
        console.log("📚 Semua opsi asli:", allFilterOptions);
        console.log("📊 Jumlah data aktif:", dataSetFilter.length);
        console.log("🧩 Field yang difilter:", filterableFields);
        console.groupEnd();
        setValueFilter();
    }
</script>

<script src="https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.min.js"></script>

{{-- PDF Modal Script   --}}
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://unpkg.com/pdfjs-dist@3.11.174/build/pdf.worker.min.js';

    function showPdfModal(url, filename, originalFile = null) {
        // Hapus modal sebelumnya jika ada
        const oldModal = document.getElementById('pdfModal');
        if (oldModal) oldModal.remove();

        // Siapkan URL download (gunakan route proxy jika originalFile diberikan)
        const downloadName = filename;
        const downloadUrl = originalFile ?
            `/slip-download/${originalFile}/${encodeURIComponent(downloadName)}` :
            url;

        const modalId = 'pdfModal-' + Date.now();
        const modalHtml = `
                <div class="modal fade" id="${modalId}" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${filename || 'Preview PDF'}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body bg-light" style="overflow:auto; -webkit-overflow-scrolling:touch;">
                                <canvas id="pdfCanvas-${modalId}" style="display:block; margin:0 auto;"></canvas>
                            </div>
                            <div class="modal-footer">
                                <div class="btn-group btn-group-sm me-auto">
                                    <button class="btn btn-outline-secondary" id="zoomin-${modalId}" title="Perbesar"><i class="bi bi-zoom-in"></i></button>
                                    <button class="btn btn-outline-secondary" id="zoomout-${modalId}" title="Perkecil"><i class="bi bi-zoom-out"></i></button>
                                    <button class="btn btn-outline-secondary" id="fitwidth-${modalId}" title="Sesuaikan Lebar"><i class="bi bi-arrows-fullscreen"></i></button>
                                </div>
                                <a class="btn btn-outline-secondary" id="download-${modalId}" href="${downloadUrl}" download><i class="bi bi-download"></i></a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modalEl = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        const canvas = document.getElementById(`pdfCanvas-${modalId}`);
        const btnZoomIn = document.getElementById(`zoomin-${modalId}`);
        const btnZoomOut = document.getElementById(`zoomout-${modalId}`);
        const btnFitWidth = document.getElementById(`fitwidth-${modalId}`);

        let pdfDoc = null;
        let currentPage = 1;
        let currentScale = 1.5;

        async function renderPage(pageNum) {
            if (!pdfDoc) return;
            const page = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({
                scale: currentScale
            });
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            canvas.style.width = viewport.width + 'px';
            canvas.style.height = viewport.height + 'px';
            const ctx = canvas.getContext('2d');
            await page.render({
                canvasContext: ctx,
                viewport
            }).promise;
        }

        async function loadPDF(pdfUrl) {
            try {
                pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                // Fit width awal
                const page = await pdfDoc.getPage(1);
                const containerWidth = canvas.parentElement.clientWidth;
                const viewport = page.getViewport({
                    scale: 1
                });
                currentScale = containerWidth / viewport.width;
                await renderPage(1);
            } catch (err) {
                console.error(err);
                alert('Gagal memuat PDF.');
                modal.hide();
            }
        }

        btnZoomIn.addEventListener('click', async () => {
            if (!pdfDoc) return;
            currentScale += 0.25;
            await renderPage(currentPage);
        });

        btnZoomOut.addEventListener('click', async () => {
            if (!pdfDoc) return;
            if (currentScale > 0.5) {
                currentScale -= 0.25;
                await renderPage(currentPage);
            }
        });

        btnFitWidth.addEventListener('click', async () => {
            if (!pdfDoc) return;
            const page = await pdfDoc.getPage(currentPage);
            const containerWidth = canvas.parentElement.clientWidth;
            const viewport = page.getViewport({
                scale: 1
            });
            currentScale = containerWidth / viewport.width;
            await renderPage(currentPage);
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            modalEl.remove();
            pdfDoc = null;
        });

        loadPDF(url);
    }
</script>
