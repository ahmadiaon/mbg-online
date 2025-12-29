@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="faq-wrap">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-block" data-toggle="collapse" data-target="#faq1">
                                Informasi data diri
                            </button>
                        </div>
                        <div id="faq1" class="collapse show" data-parent="#accordion">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                                    <div class="profile-photo">
                                        <a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i
                                                class="fa fa-pencil"></i></a>
                                        <img src="/assets/vendors/images/photo1.jpg" alt="" class="avatar-photo" />
                                        <div class="modal fade" id="modal" tabindex="-1" role="dialog"
                                            aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body pd-5">
                                                        <div class="img-container">
                                                            <img id="image" src="/assets/vendors/images/photo2.jpg"
                                                                alt="Picture" />
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" value="Update" class="btn btn-primary" />
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="text-center h5 mb-0">MBLE-0422003</h5>
                                    <p class="text-center text-muted font-16 user-name">
                                        {nama}
                                    </p>
                                    <p class="text-center text-muted font-14 user-jabatan">
                                        {jabatan}
                                    </p>
                                    <div class="profile-info">
                                        <h5 class="mb-20 h5 text-blue">Informasi Karyawan</h5>
                                        <ul>
                                            <li>
                                                <span>Divisi</span>
                                                <div class="user-divisi"></div>
                                                
                                            </li>
                                            <li>
                                                <span>Departemen</span>
                                                <div class="user-departemen"></div>
                                            </li>
                                            <li>
                                                <span>Project</span>
                                                <div class="user-project"></div>
                                            </li>
                                            <li>
                                                <span>perusahaan</span>
                                                <div class="user-perusahaan"></div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div hidden class="profile-info">
                                        <div class="timeline-month mt-20">
                                            <h5 class="mb-20 h5 text-blue">Status Kontrak (status di kanan)</h5>
                                        </div>
                                        <div class="profile-timeline-list mb-20">
                                            <ul>
                                                <li>
                                                    <div class="date">TMK</div>
                                                    <div class="task-name display-tmk">
                                                        <i class="ion-android-alarm-clock"></i> -

                                                    </div>
                                                    <p class="display-tmk-desc">
                                                        Anda sudah bekerja - tahun - bulan - hari.
                                                    </p>
                                                </li>
                                            </ul>
                                            <ul class="display-contract PKWT">
                                                <li>
                                                    <div class="date">Mulai <br> Kontrak</div>
                                                    <div class="task-name display-start-contract">
                                                        <i class="ion-android-alarm-clock"></i> 12 okt 2023 (24 Bulan)
                                                    </div>
                                                    <p class="display-start-contract-desc">
                                                        Kontrak berjalan 1 tahun 3 bulan 10 hari.
                                                    </p>
                                                </li>
                                                <li>
                                                    <div class="date">Akhir <br>Kontrak</div>
                                                    <div class="task-name display-end-contract">
                                                        <i class="ion-ios-chatboxes "></i> 26 Okt 2024
                                                    </div>
                                                    <p class="display-end-contract-desc">
                                                        Kontrak terisa 1 tahun 3 bulan 10 hari.
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script>
        function showFieldTableProfile(codeTabel, keyDataTable) {

            // Cek jika sudah ada, jangan tambah lagi
            if ($(`#faq-wrap-${codeTabel}`).length) return;

            let tableDetailsShow = db['database_tables']?.[codeTabel];
            if (!tableDetailsShow) {
                console.error('Tabel tidak ditemukan:', codeTabel);
                return;
            }



            let dataTableDetailsShow = tableDetailsShow['data']?.[keyDataTable];
            if (!dataTableDetailsShow || Object.keys(dataTableDetailsShow).length === 0) {

                return;
            }

            let element_fields = ``;
            Object.values(tableDetailsShow.fields).forEach(fields => {
                if (dataTableDetailsShow[fields.code_field] == null) {
                    return;
                }
                element_fields += `
                            <li class="li-${fields.code_field}">
                                <span>${fields.description_field}</span>
                                <p class="display-end-contract-desc">
                                    ${dataTableDetailsShow[fields.code_field]['text_data'] || '-'}
                                </p>
                            </li>
                        `;
            });

            $(`#accordion`).append(`
                <div class="card">
                    <div class="card-header">
                        <button class="btn btn-block collapsed" data-toggle="collapse" data-target="#${codeTabel}">
                            ${tableDetailsShow.description_table}
                        </button>
                    </div>
                    <div id="${codeTabel}" class="collapse" data-parent="#accordion">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="profile-info">
                                    <ul id="fields-${codeTabel}">
                                        ${element_fields}
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            `);

        }

        function showDataTableProfile(codeTabel, keyDataTable) {
            showFieldTableProfile(codeTabel, keyDataTable); // parent nya
            const dataTableParent = db['database_tables_child']?.[codeTabel];
            if (dataTableParent && dataTableParent.length >= 0) {
                for (const tableChild of dataTableParent) {
                    conLog('tableChild', tableChild);
                    showFieldTableProfile(tableChild , keyDataTable);
                }
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            console.log('db :', db)
        });
    </script>

    <script>
        readyDB(function(db) {
            conLog('NRP', NRP_USER)
            showDataTableProfile('KARYAWAN', toUUID(db['FILTER_APP']['PROFILE']['NRP']['value_data']));
            $(`.li-NRP`).remove();
        });

        // Only on profile: proses data kontrak dan TMK
        // (async () => {
        //     let date_tmk_end = getDateToday();

        //     // Ambil data dari dua sumber dan gabungkan
        //     const data_for_field_edit_1 = await getDataDatabaseAsync(1, ['public', 'public_value', 'KARYAWAN',
        //         'MBLE-0422003'
        //     ]);
        //     const data_for_field_edit_2 = await getDataDatabaseAsync(2, ['public', 'public_value',
        //         'KONTRAK-KARYAWAN', 'MBLE-0422003'
        //     ]);
        //     const data_for_field_edit = {
        //         ...data_for_field_edit_1,
        //         ...data_for_field_edit_2
        //     };

        //     // Konversi tanggal kontrak
        //     data_for_field_edit['TANGGAL-AWAL-KONTRAK'] = excelSerialToDate(data_for_field_edit[
        //         'TANGGAL-AWAL-KONTRAK']);
        //     data_for_field_edit['TANGGAL-AKHIR-KONTRAK'] = excelSerialToDate(data_for_field_edit[
        //         'TANGGAL-AKHIR-KONTRAK']);

        //     // Jika PHK, update tanggal akhir TMK
        //     if (data_for_field_edit['STATUS-KERJA'] === 'PHK') {
        //         $('.display-contract').remove();
        //         date_tmk_end = excelSerialToDate(data_for_field_edit['TANGGAL-BERAKHIR-KONTRAK--TBK-STATUS-KERJA']);
        //     }else{
        //         $('#faq-wrap-PHK-KARYAWAN').remove();
        //     }

        //     // Hitung lama kerja
        //     const count_day_tmk = dateDiff(data_for_field_edit['TANGGAL-MASUK-KERJA--TMK-'], date_tmk_end);
        //     $('.display-tmk').text(toShortStringDate_fromFormatDate(data_for_field_edit[
        //         'TANGGAL-MASUK-KERJA--TMK-']));
        //     $('.display-tmk-desc').text(
        //         `Anda sudah bekerja ${count_day_tmk.years} tahun ${count_day_tmk.months} bulan ${count_day_tmk.days} hari.`
        //     );

        //     // Hitung kontrak
        //     const count_month_contract = calculateMonthsBetweenDates(
        //         data_for_field_edit['TANGGAL-AWAL-KONTRAK'],
        //         data_for_field_edit['TANGGAL-AKHIR-KONTRAK']
        //     );
        //     $('.display-start-contract').text(
        //         `${toShortStringDate_fromFormatDate(data_for_field_edit['TANGGAL-AWAL-KONTRAK'])} (${count_month_contract} BULAN)`
        //     );

        //     const count_day_contract = dateDiff(
        //         data_for_field_edit['TANGGAL-AWAL-KONTRAK'],
        //         getDateToday()
        //     );
        //     $('.display-start-contract-desc').text(
        //         `Kontrak berjalan ${count_day_contract.years} tahun ${count_day_contract.months} bulan ${count_day_contract.days} hari.`
        //     );

        //     $('.display-end-contract').text(
        //         `${toShortStringDate_fromFormatDate(data_for_field_edit['TANGGAL-AKHIR-KONTRAK'])}`
        //     );

        //     // Hitung sisa/telat kontrak
        //     let desc_contract = 'tersisa';
        //     let count_contract;
        //     if (getDateToday() > data_for_field_edit['TANGGAL-AKHIR-KONTRAK']) {
        //         desc_contract = 'telat';
        //         count_contract = dateDiff(data_for_field_edit['TANGGAL-AKHIR-KONTRAK'], getDateToday());
        //     } else {
        //         count_contract = dateDiff(getDateToday(), data_for_field_edit['TANGGAL-AKHIR-KONTRAK']);
        //     }
        //     $('.display-end-contract-desc').text(
        //         `Kontrak ${desc_contract} ${count_contract.years} tahun ${count_contract.months} bulan ${count_contract.days} hari.`
        //     );
        // })();
    </script>
@endsection()
