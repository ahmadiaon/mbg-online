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
                    <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
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
        
        getReadyDatabase().then(function(db) {
            console.log('Database ready:', db);
            // return refreshSession().then(function() {
            //     filterApp = @json(session('FILTER_APP'));
            //     console.log('FILTER_APP', filterApp);
            // });
        }).then(function() {
            // Baru redirect setelah refreshSession selesai
            console.log('here we go')
            window.location.href = "/app";
        }).catch(function(err) {
            console.error('Error:', err);
        });
        
    </script>
</body>

</html>
