<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- __ HEADER APP --}}
    <title id="title">MBG App</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/vendors/images/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/vendors/images/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/vendors/images/favicon-16x16.png" />

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link rel="stylesheet" type="text/css" href="/assets/src/fonts/google_font_css2.css" />
    <link rel="stylesheet" type="text/css" href="/assets/vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="/assets/vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/src/plugins/datatables/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/src/plugins/datatables/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="/assets/vendors/styles/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="/assets/src/plugins/sweetalert2/sweetalert2.css" />
    {{-- ___SRC Other CSS --}}
    @yield('src_css')
    {{-- ___SRC Other CSS --}}

    <style>
        a {
            text-decoration: none;
        }


        th.no-sort::after,
        th.no-sort::before {
            display: none !important;
            /* Hilangkan ikon sorting */
        }
    </style>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        .loading {
            display: flex;
            flex-direction: column;
            min-height: 70vh;
            /* tinggi penuh layar */
            font-family: Arial, sans-serif;
        }

        .container {
            height: 70%;
            display: flex;
            flex-direction: column;
        }

        .loading {

            height: 100%;
            flex: 1;
            /* Memastikan div loading mengisi ruang */
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }

        #filter-faq table.data-table,
        #filter-faq .data-table {
            width: 100% !important;
            min-width: 100% !important;
            table-layout: auto !important;
        }

        #filter-faq .col-sm-12,
        #filter-faq .card>.collapse,
        #filter-faq .card>.collapse>table {
            width: 100% !important;
        }
    </style>

    @include('app.layout.JSfunctions')
</head>

<body>
    {{-- HEADER --}}
    @include('app.layout.header')
    @include('app.layout.leftSideBar')

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="title pb-20">
                <h2 class="header-title h3 mb-0">Loading to APP</h2>
            </div>
            <div class="body-content" style="display: none;">
                @yield('content')
            </div>
            <div class="container">
                <div class="loading-content loading" style="flex: 1; width: 100%; height: 100%;">
                    <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>

            </div>


            <div class="mb-20">
            </div>

            <div class="footer-wrap pd-20 mb-20 card-box">
                MBG Aplication by
                <a href="ganti_dengan_git_ahmadiaon" target="_blank">ahma.id</a>
            </div>
        </div>
    </div>

    @yield('modal_code')
    @include('app.layout.modal')
</body>
{{-- JAVASCRIPT SRC --}}

<script src="/assets/vendors/scripts/core.js"></script>
<script src="/assets/vendors/scripts/script.min.js"></script>
<script src="/assets/vendors/scripts/process.js"></script>
<script src="/assets/vendors/scripts/layout-settings.js"></script>
<script src="/assets/src/plugins/cropperjs/dist/cropper.js"></script>
<script src="/assets/src/plugins/apexcharts/apexcharts.min.js"></script>
<script src="/assets/src/plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="/assets/src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script src="/assets/src/plugins/datatables/js/dataTables.responsive.min.js"></script>
<script src="/assets/src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<!-- add sweet alert js & css in footer -->
<script src="/assets/src/plugins/sweetalert2/sweetalert2.all.js"></script>
<script src="/assets/src/plugins/sweetalert2/sweet-alert.init.js"></script>

@include('app.layout.JSUIFunction')
@include('app.layout.JSfunctions')

{{-- __SRC Other JS --}}

<script>
    // setUpTitle
    let current_url = window.location.href;
    let header_active = 'profile';
    let myArray = current_url.split("/");
    header_active = myArray[4];
    let NRP_USER = '';

    header_active = myArray[5];
    header_active = myArray.at(-1);
    if (header_active.indexOf('#') !== -1) {
        header_active = header_active.replace('#', '');
    }
    console.log('header active :' + header_active);
    $(`.header-title`).text(capitalizeEachWord(header_active));
    $('#title').text(`${capitalizeEachWord(header_active)} | MBG`);
    $(`#${header_active}`).addClass('active');

    var monthRomawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    var months = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober",
        "November", "Desember"
    ];
    let COLOR_BOOTSTRAP = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
    var monthRomawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    var months = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober",
        "November", "Desember"
    ];
    var months_3_char = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt",
        "Nov", "Des"
    ];
</script>

@include('app.layout.JSData')
{{-- ___SRC Other JS --}}
<script>
    let filterApp = @json(session('FILTER_APP'));
    console.log('Database initializing...');

    function readyDB(callback) {
        if (window.DB) {
            callback(window.DB);
        } else {
            // jika belum ready (halaman load lebih cepat dari fetch)
            let interval = setInterval(() => {
                if (window.DB) {
                    clearInterval(interval);
                    callback(window.DB);
                }
            }, 50);
        }
    }

    getReadyDatabase().then(function(db) {
        window.DB = db; // Simpan global

        initUI();
        filterApp = @json(session('FILTER_APP'));

        $('.body-content').show();
        $('.loading').hide();
    });


    $(document).on('select2:open', () => {
        let all = document.querySelectorAll('.select2-container--open');
        all.forEach(el => el.style.zIndex = 9999);
    });
</script>


@include('app.layout.JSfunctionDatatable')
@yield('js_code')

</html>
