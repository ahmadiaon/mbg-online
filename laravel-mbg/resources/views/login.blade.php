<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Mitra Barito Group</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f7fb;
        }

        .login-card {
            max-width: 420px;
            width: 100%;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .brand {
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .pin,
        #nik_ktp_group,
        .loadingLogin,
        .not-found {
            display: none;
        }
    </style>
</head>

<body>
    <main class="login-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <img src="/assets/vendors/images/mbg-logo.png" alt="Mitra Barito Group Logo" width="48"
                        height="48" class="mb-2">
                    <div class="brand">Mitra Barito Group</div>
                    <small class="text-muted">Login ke APP Mitrabarito</small>
                </div>

                <form id="form-login">
                    @csrf
                    <div class="mb-3">
                        <label for="nrp" class="form-label">Nomor Induk Karyawan</label>
                        <input type="text" name="nrp" class="form-control" id="nrp" required>
                    </div>

                    <div class="mb-3" id="nik_ktp_group" style="display:none;">
                        <label for="nik_ktp" class="form-label">NIK KTP</label>
                        <input type="text" name="nik_ktp" class="form-control" id="nik_ktp">
                    </div>

                    <input type="hidden" name="pin" id="pin">

                    <div class="input-group custom pin" style="display:none;">
                        <label for="nik_ktp" class="form-label">PIN</label>
                        <div class="form-group row pd-20">
                            <div class="col-12 d-flex justify-content-center gap-2">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                                <input maxlength="1" class="pinNumber form-control text-center" type="number">
                            </div>
                        </div>
                    </div>
                    <div class="d-none alert alert-danger mt-3" id="errorBox" role="alert">
                        This is a success alert—check it out!
                    </div>
                    <div class="loadingLogin text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="d-grid mt-3 mb-3">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </div>
                </form>

                <div class="text-center mt-3 small text-muted">Untuk bantuan login hubungi
                    <a href="https://wa.me/6281255897044">
                        @ahma.id</a>
                </div>

            </div>
        </div>
        <div class="text-center mt-3 small text-muted">© <span id="year"></span> Mitra Barito Group</div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Tahun otomatis
        document.getElementById('year').textContent = new Date().getFullYear();
        $('#nrp').on('input', function() {
            $('#nik_ktp').val('');
            $('.pinNumber').val('');
            $('#pin').val('');
            $('#nik_ktp_group').hide();
            $('.pin').hide();
            $('#errorBox').addClass('d-none').text('');
        });
        async function clearAllStorage() {
            // Hapus semua IndexedDB
            const dbs = await indexedDB.databases();
            dbs.forEach(db => indexedDB.deleteDatabase(db.name));


            // Hapus local & session storage
            localStorage.clear();
            sessionStorage.clear();

            console.log('Semua storage telah dihapus.');
        }

        clearAllStorage();
        $(document).ready(function() {
            localStorage.clear();
            // Auto move PIN input
            $(document).on('input', '.pinNumber', function() {
                var val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val);
                if (val.length === 1) {
                    $(this).next('.pinNumber').focus();
                }
            });

            $('#form-login').on('submit', function(e) {
                $('.loadingLogin').show();
                e.preventDefault();

                // gabungkan pin
                var pinVal = '';
                $('.pinNumber').each(function() {
                    pinVal += $(this).val();
                });
                $('#pin').val(pinVal);

                $.ajax({
                    url: "{{ route('login.ajax') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $(this).serialize(),
                    success: function(res) {
                        $('.loadingLogin').hide();
                        console.log('login res :', res);
                        if (res.status === 'success') {
                            // login selesai
                            // simpan token ke localStorage
                            localStorage.setItem('auth_token', JSON.stringify(res.data
                                .auth_login));

                            // redirect kalau perlu
                            window.location.href = "/authentication";
                        }

                        // kalau user belum set PIN → minta NIK
                        if (res.data && res.data.isPin === false) {
                            $('#nik_ktp_group').show();
                            $('.pin').hide();

                        }

                        // kalau user sudah punya PIN → minta PIN
                        if (res.data && res.data.isPin === true) {
                            $('.pin').show();
                            $('#nik_ktp_group').hide();

                        }
                        if (res.data.notFound === true) {
                            $('#errorBox')
                                .removeClass('d-none')
                                .text(res.message);
                        }
                        if (res.data.errorType === 'pin' || res.data.errorType === 'nik') {
                            $('#errorBox')
                                .removeClass('d-none')
                                .addClass('alert-danger')
                                .text('PIN/KTP tidak sesuai.');
                        }

                        console.log(res);
                    },
                    error: function(xhr) {
                        $('.loadingLogin').hide();
                        let res = xhr.responseJSON;

                        // ini baru error beneran → tampil merah
                        $('#errorBox')
                            .removeClass('d-none alert-success')
                            .addClass('alert-danger')
                            .text(res.message);
                    }


                });
            });
        });
    </script>
</body>

</html>
