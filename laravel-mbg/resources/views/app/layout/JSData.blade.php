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
    window.dbReady = initDatabase();

    function hideLoadingContent() {
        $('.loading-content').hide();
    }

    async function refreshSession() {
        console.log('SESSION START');
        console.log('======================================================================================');
        $('.refresh-data').show();

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
                    setDatabase('DATABASE', response.data);

                    db = response.data;
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


    function initDatabase() {
        console.log('[INIT] Memulai initDatabase()');

        return new Promise((resolve, reject) => {
            console.log('[INIT] Membuka IndexedDB...');

            const request = indexedDB.open("MyAppDB", 1);

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
        $('.user-name').text(db['FILTER_APP']['PROFILE']['NAMA-KARYAWAN']['text_data']);
        $('.user-jabatan').text(db['FILTER_APP']['PROFILE']['JABATAN']['text_data']);
        $('.user-divisi').text(db['FILTER_APP']['PROFILE']['DIVISI']['text_data']);
        $('.user-departemen').text(db['FILTER_APP']['PROFILE']['DEPARTEMEN']['text_data']);
        $('.user-project').text(db['FILTER_APP']['PROFILE']['PROJECT']['text_data']);
        $('.user-perusahaan').text(db['FILTER_APP']['PROFILE']['PERUSAHAAN']['text_data']);
    }

    function setDatabase(key, value) {
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
        const dbInstance = await initDatabase();
        let result = await getDatabase('DATABASE');
        db = result;
        console.log('in getReadyDatabase, db:', result);
        if (!result && result == null) {
            await refreshSession();
            result = await getDatabase('DATABASE');
            db = result;
            console.log('in if, db:', db);
            if (!db && db == null) {
                console.log('db tetap null')
            }
        }
        return db;
        // } catch (error) {
        //     console.log('db tetap null')
        //     db = {};
        //     return db;
        // }
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
    function getDatabase(key) {
        console.log('getDatabase : ' + key)
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
            } catch (err) {
                reject(err);
            }
        });
    }
</script>
