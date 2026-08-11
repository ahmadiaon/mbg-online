<!-- Modal DB Error -->
<div class="modal fade" id="modal-db-error" tabindex="-1" role="dialog" aria-labelledby="modalDbErrorLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDbErrorLabel">Database Error</h5>
            </div>
            <div class="modal-body">
                Data database tidak tersedia atau terjadi error.<br>
                Silakan refresh halaman atau hubungi admin.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    if (typeof jQuery === 'undefined') {
        var script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.type = 'text/javascript';
        document.head.appendChild(script);
    }
    // Inisialisasi DB
    let indexedDBInstance = null;
    let db = null;

    // Semua JS lain wajib tunggu ini
    // window.dbReady = initDatabase();

    function hideLoadingContent() {
        $('.loading-content').hide();
    }

    function deepMerge(obj1, obj2) {
        const result = {
            ...obj1
        };

        for (const key in obj2) {
            if (
                typeof obj2[key] === 'object' &&
                obj2[key] !== null &&
                !Array.isArray(obj2[key])
            ) {
                result[key] = deepMerge(obj1[key] ?? {}, obj2[key]);
            } else {
                result[key] = obj2[key];
            }
        }

        return result;
    }


    function refreshSession() {
        console.log('SESSION START');
        console.log('======================================================================================');
        $('.refresh-data').show();
        const now = new Date();

        // helper biar selalu 2 digit
        const pad = n => String(n).padStart(2, '0');

        // tanggal
        const day = pad(now.getDate());
        const month = pad(now.getMonth() + 1); // month dimulai dari 0
        const year = String(now.getFullYear());

        // date_start = awal bulan
        const date_start = `${year}-${month}-01`;

        // date_end = akhir bulan
        const lastDayOfMonth = new Date(year, now.getMonth() + 1, 0).getDate();
        const date_end = `${year}-${month}-${pad(lastDayOfMonth)}`;

        const default_filter_date = {
            date_start,
            date_end,
            day,
            month,
            year,
            from: null
        };





        let auth = localStorage.getItem('auth_token');
        // return penting agar bisa di-await atau di-then
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/database/refresh-session',
                type: "POST",
                headers: {
                    'x-auth-login': auth,
                    'token': auth,
                    'X-Auth-Login': auth,
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    auth_token: auth
                },
                success: function(response) {
                    status_absen_short = '';
                    console.log('response refreshSession code r-1', response);


                    db = response.data;

                    // const fields = [
                    //     'NRP',
                    //     'NAMA-KARYAWAN',
                    //     'JABATAN',
                    //     'PROJECT',
                    //     'DEPARTEMEN',
                    //     'DIVISI',
                    //     'PERUSAHAAN'
                    // ];

                    // const result = {};

                    // Object.values(db['database_tables']['KARYAWAN']['join_data'])
                    //     .forEach(data => {
                    //         let row = {};

                    //         fields.forEach(f => {
                    //             row[f] = data[f]?.text_data ?? null;
                    //         });

                    //         // 🔑 NRP jadi key utama
                    //         result[row.NRP] = row;
                    //     });

                    // console.log('result DB');
                    // console.log(result);


                    const fields = [
                        'NRP',
                        'NAMA-KARYAWAN',
                        'JABATAN',
                        'PROJECT',
                        'DEPARTEMEN',
                        'DIVISI',
                        'PERUSAHAAN',
                        'STATUS',
                    ];

                    let public_karyawan = {
                        ...db['database_tables']['KARYAWAN']
                    };

                    const result = {};

                    Object.values(db['database_tables']['KARYAWAN']['join_data'])
                        .forEach(data => {
                            let row = {};

                            fields.forEach(f => {
                                row[f] = data[f] ??
                                    null; // ⬅️ ambil OBJECT, bukan text_data
                            });

                            // jadikan NRP.code_data sebagai key utama
                            const nrpKey = row.NRP?.code_data;

                            if (!nrpKey) return;

                            result[nrpKey] = row;
                        });

                    db['database_tables']['KARYAWAN']['public_data'] = result;


                    const result_deep_merge = {};

                    Object.keys(result).forEach(nrp => {
                        result_deep_merge[nrp] = deepMerge(
                            result[nrp],
                            db['database_tables']['STATUS-KERJA-KARYAWAN']['data'][
                                nrp
                            ] ?? {} // ⬅️ override jika ada
                        );
                    });

                    console.log('result_deep_merge');
                    console.log(result_deep_merge);

                    let db_jabatan = db['database_tables']['JABATAN']['data'];
                    let new_table_karyawan_public = {
                        "code_table": "PUBLIC-KARYAWAN",
                        "parent_table": null,
                        "primary_table": "NRP",
                        "menu_table": "DATABASE",
                        "description_table": "Filter Karyawan",
                    }

                    new_table_karyawan_public['data'] = result_deep_merge;



                    db['FILTER_APP']['DEFAULT_FILTER']['date_end'] = date_end;
                    db['FILTER_APP']['DEFAULT_FILTER']['date_start'] = date_start;
                    db['FILTER_APP']['DEFAULT_FILTER']['day'] = day;
                    db['FILTER_APP']['DEFAULT_FILTER']['month'] = month;
                    db['FILTER_APP']['DEFAULT_FILTER']['year'] = year;


                    if (!localStorage.getItem('FILTER_APP')) {
                        db['FILTER_APP']['ON_FILTER'] = db['FILTER_APP']['DEFAULT_FILTER'];
                    } else {
                        let def_filter = JSON.parse(localStorage.getItem(key));

                        db['FILTER_APP']['DEFAULT_FILTER'] = def_filter['FILTER_APP'][
                            'DEFAULT_FILTER'
                        ];
                    }
                    let fields_obj = {};
                    const fields_ONE = [{
                            'table': 'KARYAWAN',
                            'field': 'NRP'
                        },
                        {
                            'table': 'IDENTITAS-KARYAWAN',
                            'field': 'NAMA-KARYAWAN'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'JABATAN'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'PROJECT'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'DEPARTEMEN'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'DIVISI'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'PERUSAHAAN'
                        },
                        {
                            'table': 'STATUS-KERJA-KARYAWAN',
                            'field': 'STATUS'
                        },
                    ];
                    fields_ONE.forEach(field => {
                        fields_obj[field.field] = db['database_tables'][field.table][
                            'fields'
                        ][
                            field.field
                        ];

                        // db['database_tables']?.['STATUS-KERJA-KARYAWAN']['fields'][
                        //     field] ?? db['database_tables']['KARYAWAN']['join_fields'][
                        //     field
                        // ]
                    });
                    new_table_karyawan_public['fields'] = fields_obj;
                    db['database_tables']['PUBLIC-KARYAWAN'] = new_table_karyawan_public;
                    let data_filter_karyawan = {};
                    Object.entries(db['database_tables']['KARYAWAN']['join_data']).forEach(([
                        key, value
                    ]) => {
                        const p = value['PERUSAHAAN']['value_data'];
                        const pr = value['PROJECT']['value_data'];
                        const dpt = value['DEPARTEMEN']['value_data'];
                        const dvs = value['DIVISI']['value_data'];
                        const sh = value['DIVISI']['value_data']; // kalau ada shift

                        if (!data_filter_karyawan[p]) data_filter_karyawan[p] = {};
                        if (!data_filter_karyawan[p][pr]) data_filter_karyawan[p][
                            pr
                        ] = {};
                        if (!data_filter_karyawan[p][pr][dpt]) data_filter_karyawan[p][
                            pr
                        ][dpt] = {};
                        if (!data_filter_karyawan[p][pr][dpt][dvs])
                            data_filter_karyawan[p][pr][dpt][dvs] = {};
                        if (!data_filter_karyawan[p][pr][dpt][dvs][sh])
                            data_filter_karyawan[p][pr][dpt][dvs][sh] = [];

                        // simpan karyawan (pakai key atau value)
                        data_filter_karyawan[p][pr][dpt][dvs][sh].push(
                            value['NRP']['code_data']
                        );
                    });


                    Object.entries(db['database_tables']['KARYAWAN']['join_data']).forEach(([key,
                        value
                    ]) => {
                        const jbt = value['JABATAN']['value_data'];
                        const g = db_jabatan[jbt]?.['GRADE']['value_data'] ||
                            '1'; // grade karyawan
                        const p = value['PERUSAHAAN']['value_data'];
                        const pr = value['PROJECT']['value_data'];
                        const dpt = value['DEPARTEMEN']['value_data'];
                        const dvs = value['DIVISI']['value_data'];

                        const sh = value['SHIFT'] ?
                            value['SHIFT']['value_data'] :
                            value['DIVISI']['value_data']; // fallback kalau belum ada shift

                        if (!data_filter_karyawan[g]) data_filter_karyawan[g] = {};
                        if (!data_filter_karyawan[g][p]) data_filter_karyawan[g][p] = {};
                        if (!data_filter_karyawan[g][p][pr]) data_filter_karyawan[g][p][
                            pr
                        ] = {};
                        if (!data_filter_karyawan[g][p][pr][dpt]) data_filter_karyawan[g][p]
                            [pr][dpt] = {};
                        if (!data_filter_karyawan[g][p][pr][dpt][dvs]) data_filter_karyawan[
                            g][p][pr][dpt][dvs] = {};
                        if (!data_filter_karyawan[g][p][pr][dpt][dvs][sh])
                            data_filter_karyawan[g][p][pr][dpt][dvs][sh] = [];

                        data_filter_karyawan[g][p][pr][dpt][dvs][sh].push(
                            value['NRP']['code_data']
                        );
                    });
                    console.log('data_filter_karyawan', data_filter_karyawan);
                    let data_karyawan = {
                        role: db['FILTER_APP']['USER']['role'],
                        PERUSAHAAN: db['FILTER_APP']['PROFILE']['PERUSAHAAN']['value_data'],
                        PROJECT: db['FILTER_APP']['PROFILE']['PROJECT']['value_data'],
                        DEPARTEMEN: db['FILTER_APP']['PROFILE']['DEPARTEMEN']['value_data'],
                        DIVISI: db['FILTER_APP']['PROFILE']['DIVISI']['value_data'],
                        SHIFT: db['FILTER_APP']['PROFILE']['DIVISI']['value_data']
                    };
                    console.log('data_karyawan', data_karyawan);
                    console.log('filtered', getAksesKaryawan(data_filter_karyawan, data_karyawan));

                    localStorage.setItem('FILTER-APP', JSON.stringify(db['FILTER_APP'][
                        'ON_FILTER'
                    ]));

                    setDatabase('DATABASE', db);
                    $('.refresh-data').hide();
                    // return false;
                    resolve(response); // 👈 tambahkan ini
                },
                error: function(response) {
                    console.log('error', response);
                    $('.refresh-data').hide();
                    stopLoading();
                    reject(response); // 👈 tambahkan ini
                }
            });
        });
    }


    function getAksesKaryawan(data, user) {
        let {
            role,
            PERUSAHAAN: p,
            PROJECT: pr,
            DEPARTEMEN: dpt,
            DIVISI: dvs,
            SHIFT: sh
        } = user;
        role = 2;
        role = parseInt(role);

        console.log('getAksesKaryawan role:', role);

        // role 2 → shift sendiri
        if (role === 2) {
            return data?.[role]?.[p]?.[pr]?.[dpt]?.[dvs]?.[sh] || [];
        }

        // role 3 → semua shift di divisi
        if (role === 3) {
            return Object.values(data?.[role]?.[p]?.[pr]?.[dpt]?.[dvs] || {})
                .flat();
        }

        // role 4-5 → semua divisi di departemen
        if ([4, 5].includes(role)) {
            return Object.values(data?.[role]?.[p]?.[pr]?.[dpt] || {})
                .map(obj => Object.values(obj).flat())
                .flat();
        }

        // role 6-7 → semua departemen di project
        if ([6, 7].includes(role)) {
            return Object.values(data?.[role]?.[p]?.[pr] || {})
                .map(dep => Object.values(dep)
                    .map(div => Object.values(div).flat())
                    .flat()
                ).flat();
        }

        // role 8-9 → semua project di perusahaan
        if ([8, 9].includes(role)) {
            return Object.values(data?.[role]?.[p] || {})
                .map(proj => Object.values(proj)
                    .map(dep => Object.values(dep)
                        .map(div => Object.values(div).flat())
                        .flat()
                    ).flat()
                ).flat();
        }

        // role 10-11 → semua perusahaan
        if ([10, 11].includes(role)) {
            return Object.values(data)
                .map(comp => Object.values(comp)
                    .map(proj => Object.values(proj)
                        .map(dep => Object.values(dep)
                            .map(div => Object.values(div).flat())
                            .flat()
                        ).flat()
                    ).flat()
                ).flat();
        }

        return [];
    }


    function initDatabase() {
        console.log('[INIT] Memulai initDatabase()');

        return new Promise((resolve, reject) => {
            console.log('[INIT] Membuka IndexedDB...');
            randNumber = localStorage.getItem('randNumber');
            const request = indexedDB.open("MyAppDB-" + randNumber, 1);
            console.log('randNumber di initDatabase: ' + randNumber);

            request.onupgradeneeded = function(event) {
                console.log('[UPGRADE] onupgradeneeded terjadi');

                try {
                    indexedDBInstance = event.target.result;
                    console.log('[UPGRADE] DB Version:', indexedDBInstance.version);

                    if (!indexedDBInstance.objectStoreNames.contains("storage")) {
                        console.log('[UPGRADE] Membuat objectStore: storage');
                        indexedDBInstance.createObjectStore("storage");
                    } else {
                        console.log('[UPGRADE] objectStore storage sudah ada');
                    }
                } catch (e) {
                    console.error('[ERROR-UPGRADE]', e);
                }
            };

            request.onsuccess = function(event) {
                console.log('[SUCCESS] IndexedDB berhasil dibuka');

                indexedDBInstance = event.target.result;

                indexedDBInstance.onerror = function(e) {
                    console.error('[DB-RUNTIME-ERROR] Terjadi error ketika DB sedang digunakan:', e);
                };

                resolve(indexedDBInstance);
            };

            request.onerror = function(event) {
                console.error('[ERROR] IndexedDB gagal dibuka');
                console.error('[ERROR MESSAGE]:', event.target.error);
                console.error('[ERROR CODE]:', event.target.errorCode);
                reject(event.target.error);
            };

            request.onblocked = function() {
                console.warn(
                    '[BLOCKED] IndexedDB diblokir. Tutup tab browser lain yang buka DB yang sama.');
            };
        });
    }


    function initUI() {
        NRP_USER = db['FILTER_APP']['PROFILE']['NRP']['value_data'];
        console.log('USER di initUI: ', db);
        $('.user-nrp').text(db['FILTER_APP']['PROFILE']['NRP']['value_data']);
        $('.user-name').text(db['FILTER_APP']['PROFILE']['NAMA-KARYAWAN']['text_data']);
        $('.user-jabatan').text(db['FILTER_APP']['PROFILE']['JABATAN']['text_data']);
        $('.user-divisi').text(db['FILTER_APP']['PROFILE']['DIVISI']['text_data']);
        $('.user-departemen').text(db['FILTER_APP']['PROFILE']['DEPARTEMEN']['text_data']);
        $('.user-project').text(db['FILTER_APP']['PROFILE']['PROJECT']['text_data']);
        $('.user-perusahaan').text(db['FILTER_APP']['PROFILE']['PERUSAHAAN']['text_data']);
    }

    function setDatabase(key, value) {
        console.log('new db' + key, value);
        return new Promise((resolve, reject) => {
            if (!indexedDBInstance) {
                reject('IndexedDB not initialized');
                return;
            }
            // Only wrap value if it's not already an object or is null
            let storeValue = value;
            if (typeof value !== 'object' || value === null || Array.isArray(value)) {
                storeValue = null;
            }
            const tx = indexedDBInstance.transaction("storage", "readwrite");
            const store = tx.objectStore("storage");
            const request = store.put(storeValue, key);

            request.onsuccess = () => resolve(true);
            request.onerror = (e) => reject(e);
        });
    }

    async function updateTableData(code_table, code_data, newRow) {
        // Update in-memory
        db['database_tables'][code_table]['data'][code_data] = newRow;

        // Save entire database_tables back to IndexedDB
        await setDatabase('database_tables', db['database_tables']);

        return true;
    }

    async function getReadyDatabase() {
        console.log('function ======== getReadyDatabase');
        // try {
        // const dbInstance = await initDatabase();
        // let result = getLocalStorage('FILTER_APP');
        let result = {
            FILTER_APP: getLocalStorage('FILTER_APP')
        };
        db = result;
        console.log('in getReadyDatabase, db:', result);
        // if (!result && result == null) {
        //     // await refreshSession();
        //     result = {
        //         FILTER_APP: getLocalStorage('FILTER_APP')
        //     };
        //     // result = await getDatabase('DATABASE');
        //     db = result;
        //     console.log('in if, db:', db);
        //     if (!db && db == null) {
        //         console.log('db tetap null')
        //     }
        // }
        return db;
    }

    async function clearAllStorage() {
        // Hapus semua IndexedDB
        const dbs = await indexedDB.databases();
        dbs.forEach(db => indexedDB.deleteDatabase(db.name));

        // Hapus cache
        const cacheKeys = await caches.keys();
        await Promise.all(cacheKeys.map(key => caches.delete(key)));

        // Hapus local & session storage
        localStorage.clear();
        sessionStorage.clear();

        console.log('Semua storage telah dihapus.');

    }

    async function refreshFullSession() {
        // Hapus semua IndexedDB
        startLoading();

        const dbs = await indexedDB.databases();
        dbs.forEach(db => indexedDB.deleteDatabase(db.name));

        // Hapus cache
        const cacheKeys = await caches.keys();
        await Promise.all(cacheKeys.map(key => caches.delete(key)));

        refreshSession();
        stopLoading();

    }

    // Get data dari IndexedDB
    async function getDatabase(key) {
        console.log('getDatabase : ' + key);

        return new Promise((resolve, reject) => {

            if (!indexedDBInstance) {
                reject(new Error('IndexedDB not initialized'));
                return;
            }
            try {
                const tx = indexedDBInstance.transaction('storage', 'readonly');
                const store = tx.objectStore('storage');
                const request = store.get(key);

                request.onsuccess = () => {
                    const result = request.result;
                    resolve(result === undefined ? null : result);
                };

                request.onerror = (event) => {
                    console.error('Error reading IndexedDB:', event);
                    reject(event.target.error || new Error('Unknown IndexedDB error'));
                };

                tx.oncomplete = () => {
                    // opsional: bisa tambahkan log di sini kalau mau tahu transaksi selesai
                    // console.log('Transaction complete');
                };

                tx.onerror = (event) => {
                    console.error('Transaction error:', event);
                    reject(event.target.error || new Error('IndexedDB transaction failed'));
                };
                // console.log('getDatabase selesai', indexedDBInstance);
            } catch (err) {
                reject(err);
            }
        });
    }

    function closeDatabase() {
        if (indexedDBInstance) {
            indexedDBInstance.close();
            indexedDBInstance = null;
            dbPromise = null;
            console.log('IndexedDB ditutup manual');
        }
    }


    // ========== Preview Helper ==========
    window.docxShow = {
        /**
         * Tampilkan PDF di modal.
         * @param {string} url  - URL file PDF (harus bisa diakses browser)
         * @param {Object} opts - { title?: string }
         */
        pdf(url, opts = {}) {
            const title = opts.title || 'Preview PDF';
            const modalId = 'modal-pdf-' + Date.now();
            const html = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0" style="height:85vh;">
                            <iframe src="${url}" width="100%" height="100%" style="border:none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            const el = document.getElementById(modalId);
            const modal = new bootstrap.Modal(el);
            modal.show();
            el.addEventListener('hidden.bs.modal', () => el.remove());
        },

        /**
         * Tampilkan gambar di modal.
         * @param {string} url  - URL gambar
         * @param {Object} opts - { title?: string, alt?: string }
         */
        img(url, opts = {}) {
            const title = opts.title || 'Preview Gambar';
            const alt = opts.alt || 'gambar';
            const modalId = 'modal-img-' + Date.now();
            const html = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="${url}" alt="${alt}" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            const el = document.getElementById(modalId);
            const modal = new bootstrap.Modal(el);
            modal.show();
            el.addEventListener('hidden.bs.modal', () => el.remove());
        },

        /**
         * Otomatis pilih pdf/img berdasarkan ekstensi.
         * @param {string} url
         * @param {Object} opts - { title?, alt? }
         */
        auto(url, opts = {}) {
            const ext = url.split('.').pop().toLowerCase();
            if (ext === 'pdf') return this.pdf(url, opts);
            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) return this.img(url, opts);
            alert('Format file tidak didukung untuk preview langsung.');
        }
    };


    // MasterData.js ini untuk table
    class MasterData {
        constructor(baseUrl = '/api/master') {
            this.baseUrl = baseUrl;
        }

        async getDatadataTable(code_table, field_data = null, code_data = null) {
            let _url = '/source/database/menu/getdatadatatable';
            console.log('FUNCTION getDatadataTable', code_table, field_data, code_data);
            console.log('FUNCTION getDatadataTable _url', _url);
            const response = await fetch(_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    table_name: code_table,
                    field: field_data,
                    value_field: code_data
                })
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            // Render tombol tahun

            conLog('response getdatadatatable', result);
            return result.data;
        }

        async getCache(key, code_table = null, code_data = null) {


            console.log('FUNCTION getCache', key, code_table, code_data);
            const response = await fetch('/source/database/cache/get-cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    key: key,
                    code_table: code_table,
                    code_data: code_data
                })
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            // Render tombol tahun

            conLog('response getcache', result);
            return result.data;
        }























        // Ambil data (seluruh tabel atau satu record)
        async get(codeTable, codeData = null) {
            const params = new URLSearchParams({
                code_table: codeTable
            });
            if (codeData) params.append('code_data', codeData);
            const res = await fetch(`${this.baseUrl}?${params}`);
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }

        // Hapus & isi ulang cache
        async refresh(codeTable) {
            const res = await fetch(`${this.baseUrl}/refresh`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    code_table: codeTable
                }),
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }

        // (Opsional) Kirim data untuk di-cache
        async set(codeTable, data) {
            const res = await fetch(`${this.baseUrl}/set`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    code_table: codeTable,
                    data
                }),
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }

        helloWorld() {
            console.log('Hello, World!');
        }
    }

    // ini untuk mengelola cache di sisi server (Laravel)
    const CacheManager = {
        baseUrl: '/source/database/cache',
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },
        async getCache(key, code_table = null, code_data = null) {
            const res = await fetch(`${this.baseUrl}/get-cache`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                },
                body: JSON.stringify({
                    key,
                    code_table,
                    code_data
                }),
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        },
        async deleteCache(key) {
            const res = await fetch(`${this.baseUrl}/delete-cache`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'auth_token': '{{ session('auth_token') }}',
                },
                body: JSON.stringify({
                    key
                }),
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }
    };
</script>
