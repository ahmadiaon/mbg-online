@extends('app.layout.main')

@section('src_css')
    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .absen-mini {
            background: #f4f6f9;
            color: #2c2c2c;
            border-radius: 8px;
            padding: 4px 6px;
            font-size: 10px;
            line-height: 1.15;
            text-align: center;
            box-shadow: inset 0 0 0 1px #e0e4ea;
        }

        /* ROW ATAS */
        .absen-mini .row-1 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #555;
        }

        /* JAM */
        .absen-mini .row-2 {
            display: flex;
            justify-content: center;
            align-items: center;

            font-size: 9px;
            color: #222;

            white-space: nowrap;
            /* ⬅️ kunci 1 baris */
            overflow: hidden;
            /* kalau kepanjangan */
            text-overflow: ellipsis;
            /* jadi ... */
            line-height: 1;
        }

        /* ROW BAWAH */
        .absen-mini .row-3 {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }

        /* STATUS */
        .absen-mini .status {
            padding: 1px 7px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            color: #fff
        }

        /* ===== HEADER ===== */
        .absen-header {
            padding: 6px 8px;
        }

        /* ===== BAR ABSENSI ===== */
        .absen-bar-wrapper {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .absen-bar {
            display: grid;
            grid-template-columns: 44px 1fr 32px;
            align-items: center;
            gap: 6px;
        }

        .absen-label {
            font-size: 11px;
            font-weight: 600;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 3px 0;
        }

        .absen-track {
            background: #edf0f5;
            height: 14px;
            border-radius: 8px;
            overflow: hidden;
        }

        .absen-fill {
            height: 100%;
            border-radius: 8px;
        }

        .absen-value {
            font-size: 11px;
            font-weight: 600;
        }

        .absen-bar {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
        }

        .absen-label {
            min-width: 42px;
            text-align: center;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 0;
            border-radius: 6px;
        }

        .absen-track {
            flex: 1;
            height: 14px;
            background: #e9edf3;
            border-radius: 10px;
            overflow: hidden;
        }

        .absen-fill {
            height: 100%;
            border-radius: 10px;
        }

        .absen-value {
            min-width: 24px;
            font-size: 11px;
            font-weight: 600;
        }

        .calendar-header {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .calendar-header .day {
            padding: 6px 0;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            border-right: 1px solid #e5e7eb;
        }

        .calendar-header .day:last-child {
            border-right: none;
        }

        /* weekend */
        .calendar-header .sab,
        .calendar-header .min {
            color: #dc2626;
            background: #fef2f2;
        }
    </style>
@endsection()

@section('content')
    {{--
        hanya HR yang bisa ubah data absensi karyawan lain
        admin lain hanya bisa update fingerprint
        export data absensi
        lalu bagaimana yang lupa absen
        membuat atau mengajukan perubahan data absensi yang di approve oleh HR dan atasan
        HR pun tidak bisa berrti mengubah data seenaknya
        harus ada catatan kenapa data diubah

        saya ada fitur
        1. import data absensi dari fingerprint
        2. import data absensi manual atau laporan harian 
        3. pengajuan perubahan data absensi
        4. export harian
        5. export bulanan

    
    
    --}}
    <div class="row mt-20 mb-20" id="data-database">
        {{-- LIST TABLE id="datatable-data" -- TABLE : datatable-Group-Form --}}

        {{-- FIELD DATA SHOW --}}
        <div class="col-md-12 mt-20"> {{-- LIST data --}}
            <div class=" card-box mb-30">
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-left ">
                        <h4 class="text-blue h4">Global Filter Karyawan</h4>
                        <div class="row">
                            <div class="col-12">
                                <div class="field-show-header field-show-PUBLIC-KARYAWAN">

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="pull-right">
                        <div class="btn-group">

                            <div class="row mb -20 pd-20">
                                <div class="col-auto ms-auto text-end">
                                    <button type="button" class="btn btn-success" onclick="refreshDataAbsensi()">
                                        <i class="icon-copy bi bi-file-lock"></i> Save Filter
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
                <div class="mb-20" id="datatable-PUBLIC-KARYAWAN-wrapper">
                    <table id="datatable-PUBLIC-KARYAWAN" class="display cell-border" style="width:100%">
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

        <div class="col-md-12 mt-20">
            <div class=" card-box mb-30">
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-right">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body py-2">
                                <div class="form-row align-items-center">

                                    <div class="col-auto font-weight-bold text-muted">
                                        Periode
                                    </div>

                                    <div class="col-auto">
                                        <select id="filterMonth" class="form-control form-control-sm">
                                            <option value="01">Januari</option>
                                            <option value="02">Februari</option>
                                            <option value="03">Maret</option>
                                            <option value="04">April</option>
                                            <option value="05">Mei</option>
                                            <option value="06">Juni</option>
                                            <option value="07">Juli</option>
                                            <option value="08">Agustus</option>
                                            <option value="09">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>

                                    <div class="col-auto">
                                        <select id="filterYear" class="form-control form-control-sm">
                                            <option value="2024">2024</option>
                                            <option>2025</option>
                                            <option selected>2026</option>
                                            <option>2027</option>
                                        </select>
                                    </div>

                                    <div class="btn-group">
                                        <button onclick="refreshDataAbsensi()" type="button"
                                            class="btn btn-sm btn-primary">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> Filter
                                        </button>

                                        <button id="btn-import" data-toggle="modal" data-target="#import-absensi"
                                            type="button" class="btn btn-sm btn-success">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> Import
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> Menu
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
                <div class="mb-20" id="datatable-absensies-wrapper">
                    <table id="datatable-absensies" class="display cell-border" style="width:100%">
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

        {{-- LINKED IMPORT --}}
        <div class="col-md-12 mt-20">
            <div class=" card-box mb-30">
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-right">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body py-2">
                                <div class="form-row align-items-center">

                                    <div class="col-auto font-weight-bold text-muted">
                                        Hasil Import Absensi
                                    </div>

                                    <div class="btn-group">
                                        <button id="btn-import" data-toggle="modal" data-target="#import-absensi"
                                            type="button" class="btn btn-sm btn-success">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> EXPORT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="mb-20" id="datatable-after-import-wrapper">
                    <table id="datatable-after-import" class="display cell-border" style="width:100%">
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

         {{-- UNLINKED IMPORT --}}
        <div class="col-md-12 mt-20">
            <div class=" card-box mb-30">
                <div class="pd-20 clearfix mb-10">
                    <div class="pull-right">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body py-2">
                                <div class="form-row align-items-center">

                                    <div class="col-auto font-weight-bold text-muted">
                                        Hasil Un Linked Import Absensi - Karyawan belum terkonfigurasi
                                    </div>

                                    <div class="btn-group">
                                        <button id="btn-import" data-toggle="modal" data-target="#import-absensi"
                                            type="button" class="btn btn-sm btn-success">
                                            <i class="icon-copy bi bi-journal-arrow-down"></i> EXPORT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="mb-20" id="datatable-un-have-employees-wrapper">
                    <table id="datatable-un-have-employees" class="display cell-border" style="width:100%">
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

    <!-- import -->
    <div class="modal fade" id="import-absensi" role="dialog" aria-labelledby="import-modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="form-import-absensi" action="/user/absensi/import" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Import
                            Absensi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih Absensi</label>
                            <input autofocus name="uploaded_file" type="file"
                                class="form-control-file form-control height-auto" />
                        </div>
                        <div class="form-group row date-setup">
                            <div class="col-6">
                                <label>Mulai tanggal</label>
                                <select onchange="loopDate()" name="date_absen_start" style="width: 100%"
                                    id="date_absen_start" class="custom-select2 form-control">

                                </select>
                            </div>
                            <div class="col-6">
                                <label>Sampai tanggal</label>
                                <select name="date_absen_end" style="width: 100%" id="date_absen_end"
                                    class="custom-select2 form-control">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="importAbsensi()" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection()


@section('js_code')
    <script>
        readyDB(function(db) {
            // DATA TABLE
            let datatablePublicKaryawan = {
                tableId: 'PUBLIC-KARYAWAN',
                tableDataDetails: null,
                datasetTable: null,
                paggingDatatable: true,
                staticName: 'PUBLIC-KARYAWAN',
                isDeleteAction: false,

            };
            initDataTable(datatablePublicKaryawan);
            let ON_FILTER = getLocalStorage('FILTER-APP');
            conLog('ON_FILTER loaded from localStorage', ON_FILTER);

            const monthEl = document.getElementById('filterMonth');
            const yearEl = document.getElementById('filterYear');

            monthEl.value = ON_FILTER['month'];
            yearEl.value = ON_FILTER['year'];

            // Trigger event change agar sistem tahu ada perubahan (opsional)
            monthEl.dispatchEvent(new Event('change'));
            yearEl.dispatchEvent(new Event('change'));

            refreshDataAbsensi();
            // abc = false;

        });

        let abc = true;

        function refreshDataAbsensi() {
            let month = document.getElementById('filterMonth').value;
            let year = document.getElementById('filterYear').value;
            conLog('month', month);
            conLog('year', year);
            conLog('ON_FILTER', db['FILTER_APP']['ON_FILTER']);
            conLog('activeFilters', activeFilters);
            conLog('allDataFilter', allDataFilter);
            let ON_FILTER = db['FILTER_APP']['ON_FILTER'];

            let arrFilterField = [
                'PERUSAHAAN', 'PROJECT', 'DEPARTEMEN', 'DIVISI', 'JABATAN', 'NRP'
            ]

            arrFilterField.forEach(element => {
                if (element in availableFilters) {
                    ON_FILTER[element] = availableFilters[element];
                }
            });

            arrFilterField.forEach(element => {
                if (element in activeFilters) {
                    ON_FILTER[element] = activeFilters[element];
                }
            });

            if (month != ON_FILTER['month'] || year != ON_FILTER['year']) {
                let period = buildPeriod(month, year);
                ON_FILTER['date_start'] = period['date_start'];
                ON_FILTER['date_end'] = period['date_end'];
                ON_FILTER['day'] = period['day'];
                ON_FILTER['month'] = period['month'];
                ON_FILTER['year'] = period['year'];
            }

            db['FILTER_APP']['ON_FILTER'] = ON_FILTER;
            localStorage.setItem('FILTER-APP', JSON.stringify(ON_FILTER));
            conLog('FINAL ON_FILTER', ON_FILTER);

            // ===================================================
            // G E T     D A T A       A B S E N S I
            // ===================================================
            $.ajax({
                url: '/manage/absensi/getDataAbsensi',
                type: "POST",
                headers: {
                    'auth_token': localStorage.getItem('auth_token'),
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    on_filter: JSON.stringify(ON_FILTER),
                    is_self: 'all',
                },
                success: function(response) {
                    console.log('adfaskd');
                    conLog('response', response);

                    let datasetDatatable = response['data']['data_absensi'];
                    conLog('datasetDatatable', datasetDatatable);
                    let templateDataTableAbsensies = {
                        code_table: "absensies",
                        parent_table: null,
                        primary_table: "nrp",
                        menu_table: "STAND-ALONE",
                        description_table: "Absensi Table",
                        fields: {
                            'nrp': {
                                sort_field: '0',
                                code_field: 'nrp',
                                description_field: 'NRP',
                                visibility_data_field: 'show',
                                type_data_field: 'TEXT',
                            },
                            'absensies': {
                                sort_field: '1',
                                code_field: 'absensies',
                                description_field: 'Data Absensi',
                                visibility_data_field: 'show',
                                type_data_field: 'ABSENSI',
                            },
                        },
                        data: Object.values(datasetDatatable),
                    }

                    let datatableAbsensi = {
                        tableId: 'absensies',
                        tableDataDetails: templateDataTableAbsensies,
                        datasetTable: null,
                        paggingDatatable: true,
                        staticName: 'absensies',
                        isDeleteAction: false,
                        isAction: true,
                    };
                    // $('#datatable-absensies-wrapper').empty();
                    // abc = false;
                    delete allDataFilter['absensies'];
                    $('#datatable-absensies-wrapper').empty();
                    initDataTable(datatableAbsensi);
                },
                error: function(response) {
                    console.log('error', response);
                }
            });

            // ===================================================
            // G E T        D A T A       A B S E N S I
            // ===================================================
        }

        function buildPeriod(month, year) {
            console.log('buildPeriod :', month, " : ", year);
            // pastikan format 2 digit
            month = month.toString().padStart(2, '0');

            // tanggal awal
            const date_start = `${year}-${month}-01`;

            // cari hari terakhir bulan
            const lastDay = new Date(year, month, 0).getDate();

            const date_end = `${year}-${month}-${lastDay}`;

            return {
                date_start: date_start,
                date_end: date_end,
                day: lastDay.toString(),
                from: null,
                month: month,
                year: year.toString()
            };
        }

        function reloadTableAbsensi() {
            let tableId = 'datatable-absensies';
            let dataTable = $(`#${tableId}`).DataTable();
            dataTable.ajax.reload();
        }

        // function importAbsensi() {
        //     startLoading();
        //     let _url = '/manage/absensi/import-absensi';
        //     $('#after-import').empty();

        //     let data_column = [];
        //     let data_null_employees_column = [];
        //     let data_absen;
        //     let date_absen_start = $('#date_absen_start').val();
        //     var form = $('#form-import-absensi')[0];
        //     var form_data = new FormData(form);
        //     conLog('form_data');

        //     $.ajax({
        //         url: _url,
        //         type: "POST",
        //         contentType: false,
        //         processData: false,
        //         data: form_data,
        //         success: function(response) {
        //             conLog('responseess', response);

        //             $('#loading-modal').modal('hide');
        //         },
        //         error: function(response) {
        //             conLog('errr', response);
        //             alertModal();
        //         }
        //     });
        // }
    </script>



    <script>
        function exportAbsen() {
            startLoading();
            let filteredData = getDataFilteredKaryawan().reduce((acc, index) => {
                if (db['db']['database_data']['ABSENSI_COUNT'].hasOwnProperty(index)) {
                    acc[index] = db['db']['database_data']['ABSENSI_COUNT'][index];
                }
                return acc;
            }, {});

            conLog('filteredData', filteredData);

            $.ajax({
                url: '/user/absensi/export+data',
                type: "POST",
                headers: {
                    'x-auth-login': ui_dataset.ui_dataset.user_authentication.auth_login,
                    'default-filter-absensi': default_filter_absensi
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data_absensi: JSON.stringify(filteredData),
                    filter_absensi: default_filter_absensi,
                },
                success: function(response) {
                    cg('response', response);
                    var dlink = document.createElement("a");
                    dlink.href = `/${response.data}`;
                    dlink.setAttribute("download", "");
                    dlink.click();
                    stopLoading();
                },
                error: function(response) {
                    cg('err export', response)
                    alertModal()
                }
            });
        }

        function loopDate() {
            var start = new Date(dt_start);
            var end = new Date(dt_end);

            var loop = new Date(start);

            let date_absen_start = $('#date_absen_start').val();
            if (date_absen_start) {
                $(`#date_absen_end`).empty();
            }

            while (loop <= end) {
                if (date_absen_start) {
                    var loop_date_start = new Date(date_absen_start);
                    if (loop > loop_date_start) {
                        $(`#date_absen_end`).prepend(` <option>${formatDate(loop)}</option>`)
                    }
                } else {
                    $(`#date_absen_start`).append(` <option>${formatDate(loop)}</option>`);
                    $(`#date_absen_end`).prepend(` <option>${formatDate(loop)}</option>`)
                }
                var newDate = loop.setDate(loop.getDate() + 1);
                loop = new Date(newDate);
            }
            $('#date_absen_end').val(dt_end);
        }



        function importAbsensi() {
            startLoading();
            $('#after-import').empty();

            let data_column = [];
            let data_null_employees_column = [];
            let data_absen;
            $('#after-import').append(`
                        <div class="card-box mb-30 ">
                            <div class="row pd-20">
                                <div class="col-auto">
                                    <h4 class="text-blue h4">Absensi Karyawan</h4>
                                </div>
                                <div class="col text-right">
                                    <div class="btn-group">
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect" data-toggle="dropdown"
                                                aria-expanded="false">
                                                Menu <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" id="btn-export" onclick="exportEmployee()"  href="#">Export Karyawan</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <table id="table-have-employees" class="display nowrap stripe hover table" style="width:100%">
                                <thead>
                                    <tr id="header-table-have-employees">
                                        <th>Detail Data Karyawan</th>
                                        <th>Nama fingger</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    
                    
                        <div class="card-box mb-30 ">
                            <div class="row pd-20">
                                <div class="col-auto">
                                    <h4 class="text-blue h4">Absensi Karyawan belum terkonfigurasi</h4>
                                </div>
                                <div class="col text-right">
                                    <div class="btn-group">
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect" data-toggle="dropdown"
                                                aria-expanded="false">
                                                Menu <span class="caret"></span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" id="btn-export" href="/user/absensi/export/">Export + Data</a>
                                                <a class="dropdown-item" id="btn-import" data-toggle="modal" data-target="#import-modal"
                                                    href="">Import</a>
                                            </div>
                                        </div>
                    
                                    </div>
                                </div>
                            </div>
                    
                            <table id="table-null-employees" class="display nowrap stripe hover table" style="width:100%">
                                <thead>
                                    <tr id="header-table-null-employees">
                                        <th>Nama fingger</th>
                                        <th>Detail Data Karyawan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
            `);


            let date_absen_start = $('#date_absen_start').val();
            let _url = '/manage/absensi/import-absensi';
            var form = $('#form-import-absensi')[0];
            var form_data = new FormData(form);

            $.ajax({
                url: _url,
                type: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {
                    $('#loading-modal').modal('hide');
                    conLog('responseess', response);
                    // return false;
                    if (response.message == 'excel') {
                        showModalMessage('import success');
                        return false;
                    }

                    if (!date_absen_start) {
                        $('.date-setup').attr('hidden', false);
                        $('#loading-modal').modal('hide');
                        dt_start = response.data.date_absen_start;
                        dt_end = response.data.date_absen_end;
                        loopDate();
                    } else {

                        datasetDatatable = Object.values(response.data.have_employees.data);

                        let templateDataTableAfterImport = {
                            code_table: "after-import",
                            parent_table: null,
                            primary_table: "nrp",
                            menu_table: "STAND-ALONE",
                            description_table: "Hasil Import Absensi",
                            fields: {
                                'nrp': {
                                    sort_field: '1',
                                    code_field: 'nrp',
                                    description_field: 'Employee UUID',
                                    visibility_data_field: 'show',
                                    type_data_field: 'TEXT',
                                },
                                'machine_id': {
                                    sort_field: '1',
                                    code_field: 'machine_id',
                                    description_field: 'Machine ID',
                                    visibility_data_field: 'show',
                                    type_data_field: 'TEXT',
                                },
                                'data_absensi': {
                                    sort_field: '1',
                                    code_field: 'data_absensi',
                                    description_field: 'Data Absensi',
                                    visibility_data_field: 'show',
                                    type_data_field: 'ABSENSI',
                                },
                            },
                            data: datasetDatatable
                        }

                        let datatableAfterImport = {
                            tableId: 'after-import',
                            tableDataDetails: templateDataTableAfterImport,
                            datasetTable: null,
                            paggingDatatable: true,
                            staticName: null,
                            isDeleteAction: false,
                        };
                        initDataTable(datatableAfterImport);

                        if (response.data.un_have_employees) {
                            datasetDatatable = Object.values(response.data.un_have_employees.data);
                            let templateDataTableAfterImportUnHave = {
                                code_table: "un-have-employees",
                                parent_table: null,
                                primary_table: "nrp",
                                menu_table: "STAND-ALONE",
                                description_table: "Hasil Import Absensi - Karyawan belum terkonfigurasi",
                                fields: {
                                    'nrp': {
                                        sort_field: '1',
                                        code_field: 'nrp',
                                        description_field: 'Employee UUID',
                                        visibility_data_field: 'show',
                                        type_data_field: 'TEXT',
                                    },
                                    'machine_id': {
                                        sort_field: '1',
                                        code_field: 'machine_id',
                                        description_field: 'Machine ID',
                                        visibility_data_field: 'show',
                                        type_data_field: 'TEXT',
                                    },
                                    'data_absensi': {
                                        sort_field: '1',
                                        code_field: 'data_absensi',
                                        description_field: 'Data Absensi',
                                        visibility_data_field: 'show',
                                        type_data_field: 'ABSENSI',
                                    },
                                },
                                data: datasetDatatable
                            }

                            let datatableAfterImportUnHave = {
                                tableId: 'un-have-employees',
                                tableDataDetails: templateDataTableAfterImportUnHave,
                                datasetTable: null,
                                paggingDatatable: true,
                                staticName: null,
                                isDeleteAction: false,
                            };
                            initDataTable(datatableAfterImportUnHave);
                            

                        }
                        return false;

                        if (!(ui_dataset.ui_dataset.user_authentication.feature).includes('SUPERADMIN')) {
                            $('#loading-modal').modal('hide');
                            stopLoading();
                            return false;
                        }

                        after_import_data = response.data;
                        conLog('after_import_after_absen', after_import_data);
                        let data_absen = response.data;

                        let name_fingger_unknown = {
                            mRender: function(data, type, row) {
                                return row
                            }
                        };
                        data_null_employees_column.push(name_fingger_unknown);
                        name_fingger_unknown = {
                            mRender: function(data, type, row) {
                                return `   <button onclick="updateFingger('${row}', '')" type="button" class="btn btn-secondary mr-1  py-1 px-2">
                                                <i class="icon-copy ion-gear-b"></i>
                                            </button>`
                            }
                        };
                        data_null_employees_column.push(name_fingger_unknown)

                        // return false;
                        let nrp = {
                            mRender: function(data, type, row) {
                                return emmp(row);
                            }
                        };

                        data_column.push(nrp);
                        nrp = {
                            mRender: function(data, type, row) {
                                return data_absen.identification[row][
                                    'machine_id'
                                ];
                            }
                        };
                        data_column.push(nrp);


                        var end = new Date(data_absen['have_employees']['configuration']['end_date']);
                        var loop = new Date(data_absen['have_employees']['configuration']['first_date']);
                        let x = 1;

                        let variable_header = [];
                        while (loop < end) {
                            let format_date_day = formatDate(loop);
                            variable_header.push(format_date_day);
                            $(`#header-table-have-employees`).append(` <th>${format_date_day}</th>`);
                            $(`#header-table-null-employees`).append(` <th>${format_date_day}</th>`);

                            element_profile_empl = {
                                mRender: function(data, type, row) {
                                    let status_absen = "-";
                                    let status_absen_ceklog = "-";
                                    try {
                                        status_absen = data_absen.have_employees['detail'][row][
                                                format_date_day
                                            ]
                                            ['status_absen_uuid'];
                                        status_absen_ceklog = data_absen.have_employees['detail'][
                                            row
                                        ][
                                            format_date_day
                                        ]['cek_log'];
                                        if (status_absen == null) {
                                            status_absen = '-';
                                        }

                                        if (!status_absen_ceklog) {
                                            status_absen_ceklog = '-';
                                        }
                                    } catch (error) {
                                        status_absen = '-';
                                        status_absen_ceklog = '-';
                                    }

                                    return `<div>
                                                    <h4 class="mb-0 h4">${status_absen}</h4>
                                                        <small>${status_absen_ceklog}</small>
                                                </div>`
                                }
                            };

                            element_profile_null_empl = {
                                mRender: function(data, type, row) {
                                    let status_absen = "-";
                                    let status_absen_ceklog = "-";

                                    try {
                                        status_absen = data_absen.null_employees[row][
                                                'data'
                                            ][format_date_day]
                                            ['status_absen_uuid'];

                                        status_absen_ceklog = data_absen.null_employees[row]['data']
                                            [
                                                format_date_day
                                            ]
                                            ['cek_log'];

                                        if (status_absen == null) {
                                            status_absen = '-';
                                        }

                                        if (!status_absen_ceklog) {
                                            status_absen_ceklog = '-';
                                        }
                                    } catch (error) {
                                        status_absen = '-';
                                        status_absen_ceklog = '-';
                                    }
                                    // cg(row.employee_uuid,data_absen.null_employees[row.employee_uuid]);

                                    return `<div>
                                                    <h4 class="mb-0 h4">${status_absen}</h4>
                                                        <small>${status_absen_ceklog}</small>
                                                </div>`
                                }
                            };

                            data_column.push(element_profile_empl);
                            data_null_employees_column.push(element_profile_null_empl)


                            var newDate = loop.setDate(loop.getDate() + 1);
                            loop = new Date(newDate);
                        }
                        let have_employees = Object.keys(data_absen['have_employees']['detail']);
                        conLog('have_employees', have_employees)

                        $(`#table-have-employees`).DataTable({
                            scrollX: true,
                            data: have_employees,
                            columns: data_column,
                        });

                        let data_null_employees = [];

                        if (data_absen['null_employees']) {
                            data_null_employees = Object.keys(data_absen['null_employees']);
                        }

                        conLog('data_null_employees', data_null_employees);

                        $(document).ready(function() {
                            $(`#table-null-employees`).DataTable({
                                scrollX: true,
                                data: data_null_employees,
                                columns: data_null_employees_column,
                            });
                        });

                        cg('variable_header', variable_header);




                        $('#after-import').show();
                        return false;
                        getDataAbsensi();
                        // response.data.
                        // refreshTableAfterImport();
                        return false;
                        window.location.href = "/user/absensi/after-import";
                    }
                    $('#loading-modal').modal('hide');
                },
                error: function(response) {
                    cg('errr', response);
                    alertModal()
                }
            });
        }

        function updateFingger(employee_uuid, nik_employee) {
            cg(employee_uuid, nik_employee);
            $('#employee_uuid').val(employee_uuid);
            $('#update-fingger-modal').modal('show');
        }

        function reportOpenModalReportStatusAbsen() {
            let button = document.getElementById('btn-list-ketidakhadiran');
            button.click();
            $('#modal-report-status-absen').modal('show');
        }

        function storeFingger(idForm) {
            if (isRequiredCreate(['employee_uuid']) > 0) {
                return false;
            }

            globalStoreNoTable(idForm).then((data) => {
                console.log('data store employees')
                let user = data.data;
                console.log(data);
                stopLoading();
                $('#success-modal-id').modal('show')
            })
        }
    </script>
@endsection
