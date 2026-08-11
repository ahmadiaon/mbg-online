<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>authentication — Mitra Barito Group</title>
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
                    <small class="text-muted">Sedang menghubungkan ke APP</small>
                </div>
                <div class="text-center my-4">


                    <form id="editpinForm">
                        <div hidden class="col-md-12 col-12 pin-edit">
                            <div class="form-group justify-center row updatePinForm">
                                <div class="col-12 d-flex justify-content-center gap-2 mb-20">
                                    <input name="pinNumber-1" maxlength="1" id="pinNumber-1"
                                        class="pinNumber form-control mr-1" type="number">
                                    <input name="pinNumber-2" maxlength="1" id="pinNumber-2"
                                        class="pinNumber  form-control mr-1" type="number">
                                    <input name="pinNumber-3" maxlength="1" id="pinNumber-3"
                                        class="pinNumber  form-control mr-1" type="number">
                                    <input name="pinNumber-4" maxlength="1" id="pinNumber-4"
                                        class="pinNumber  form-control mr-1" type="number">
                                    <input name="pinNumber-5" maxlength="1" id="pinNumber-5"
                                        class="pinNumber  form-control mr-1" type="number">
                                    <input name="pinNumber-6" maxlength="1" id="pinNumber-6"
                                        class="pinNumber  form-control" type="number">
                                </div>
                                <div class="col-12">
                                    <div class="errNotif text-danger">PIN harus terdiri dari 6 digit.</div>
                                </div>
                                <div class="col-12 p-20">
                                    <button type="button" id="btn-simpan-user" onclick="simpanUser()"
                                        class="mt-20 btn btn-primary">Simpan PIN</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div style="display: none;" class="successNotif text-success">PIN berhasil diperbarui.</div>
                    <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div style="display: none;" class="errNotifToken col-12">
                        <div class="errNotif text-danger">Token tidak valid.</div>
                    </div>
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
    @include('app.layout.JSData')
    <script>
        function getInputValue(idElement) {
            return $(`#${idElement}`).val();
        }

        function getPinInsert() {
            return `${getInputValue('pinNumber-1')}${getInputValue('pinNumber-2')}${getInputValue('pinNumber-3')}${getInputValue('pinNumber-4')}${getInputValue('pinNumber-5')}${getInputValue('pinNumber-6')}`;
        }

        function simpanUser() {
            $('#loadingSpinner').show();
            var pinInsert = getPinInsert();
            var objPinInsert = {
                name: "pin",
                value: null
            }


            if (pinInsert.length > 1) {
                console.log('formDataArray', 'formDataArray')
                if (pinInsert.length < 6) {
                    $(`.errNotif`).show();
                    return false;
                } else if (pinInsert.length == 6) {
                    objPinInsert = {};
                    objPinInsert = {
                        name: "pin",
                        value: pinInsert
                    }
                }
            }
            // return false;
            $.ajax({
                url: '/api/user/update',
                type: "POST",
                headers: {
                    'x-auth-login': localStorage.getItem('auth_token'),
                    'token': localStorage.getItem('auth_token'),
                    'X-Auth-Login': localStorage.getItem('auth_token'),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    email: $('.identitas-user-EMAIL').val(),
                    no_hp: $('.identitas-user-NO-HP').val(),
                    uuid_data: db?.FILTER_APP?.PROFILE?.NRP?.uuid_data ?? null,
                    pin: objPinInsert.value,
                    id_email: db?.FILTER_APP?.PROFILE?.EMAIL?.id ?? null,
                    id_no_hp: db?.FILTER_APP?.PROFILE?.['NO-HP']?.id ?? null,
                },
                success: function(response) {

                    if (objPinInsert.value) {
                        window.location.href = "/auth/login";
                    }
                    console.log('response', response);
                    $('#loadingSpinner').hide();
                    $('.successNotif').show();
                },
                error: function(response) {
                    console.log('error', response);
                    $('#loadingSpinner').hide();
                    //alertModal()
                }
            });
        }

        $(document).ready(function() {
            // Menggunakan event input untuk mengawasi perubahan pada input
            $('#editpinForm').on('input', '.pinNumber', function() {
                // Mendapatkan nilai input
                var inputValue = $(this).val();

                // Pindah ke input berikutnya jika digit telah dimasukkan
                var sanitizedValue = $(this).val().replace(/[^0-9]/g, '');

                // Menetapkan nilai bersih kembali ke input
                $(this).val(sanitizedValue);
                inputValue = $(this).val();
                if (inputValue.length === 1) {
                    $(this).next('.pinNumber').focus();
                }
            });
        });
    </script>
    <script>
        // Ambil token dari URL (segment terakhir)
        const token = window.location.pathname.split('/').pop();
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        console.log('Token:', token);

        async function verify() {
            try {
                const res = await fetch(`/authentication/${token}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({}) // body kosong, token ada di URL
                });

                const data = await res.json();
                console.log('Response:', data);

                if (data.status == 'success') {
                    localStorage.setItem('auth_token', JSON.stringify(data.auth_token));
                    localStorage.setItem('FILTER_APP', JSON.stringify(data.FILTER_APP));
                    console.log('Redirecting to /app');
                    $('#loadingSpinner').hide();
                    $('.pin-edit').prop('hidden', false);
                    // window.location.href = '/app';
                } else {
                    throw new Error(data.message || 'Token tidak valid.');
                }
            } catch (err) {
                console.error('Error:', err);
                $('#loadingSpinner').hide();
                $('.errNotifToken').show();

            }
        }

        verify();
    </script>
</body>

</html>
