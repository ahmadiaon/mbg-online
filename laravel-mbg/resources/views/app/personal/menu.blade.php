@extends('app.layout.main')

@section('content')
    <div class="row pd-20">
        <div class="col-md-4 mr-10 col-sm-12 card-box mb-30">
            <h5 class="pd-20 h5 mb-0">Personal</h5>
            <div class="latest-post">
                <ul>
                    <li hidden>
                        <div class="row">
                            <div class="col-md-2">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                            </div>
                            <div class="col-md-10">
                                <h4>
                                    <a href="#">Absensi</a>
                                </h4>
                                <span>Informasi mengenai kehadiran anda </span><span class="badge bg-primary"><i
                                        class="icon-copy bi bi-arrow-return-right"></i></span>
                            </div>
                        </div>

                    </li>
                    <li>
                        <div class="row">
                            <div class="col-md-2">
                                <img width="30px" src="assets/vendors/logo/slip-logo.png" alt="">
                            </div>
                            <div class="col-md-10">
                                <h4>
                                    <a href="/my-slip">SLIP</a>
                                </h4>
                                <a href="/my-slip">
                                <span>Informasi mengenai slip gaji anda </span>
                                <span class="badge bg-primary"><i
                                            class="icon-copy bi bi-arrow-return-right"></i></span></a>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="row">
                            <div class="col-md-2">
                                <img width="30px" src="assets/vendors/logo/user.png" alt="">
                            </div>
                            <div class="col-md-10">
                                <h4>
                                    <a href="/user">User</a>
                                </h4>
                                <a href="/user">
                                    <span>Ubah PIN dan User </span><span class="badge bg-primary"><i
                                            class="icon-copy bi bi-arrow-return-right"></i></span></a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div hidden class="col-md-4 mr-10 col-sm-12 card-box mb-30">
            <h5 class="pd-20 h5 mb-0">HSE</h5>
            <div class="latest-post">
                <ul>
                    <li>
                        <div class="row">
                            <div class="col-md-2">
                                <img src="assets/vendors/images/chrome.png" alt="">
                            </div>
                            <div class="col-md-10">
                                <h4>
                                    <a href="#">Hazard Report</a>
                                </h4>
                                <span>Laporkan kondisi berbahaya </span><span class="badge bg-primary"><i
                                        class="icon-copy bi bi-arrow-return-right"></i></span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    {{-- warning modal change pin --}}
    <div class="modal fade" id="warning-modal-change-pin" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content bg-warning">
                <div class="modal-body text-center">
                    <h3 class="mb-15">
                        <i class="fa fa-exclamation-triangle"></i> Warning
                    </h3>
                    <p>
                        harap ganti password login anda menggunakan PIN, untuk memudahkan login di kemudian hari.
                    </p>
                    <button type="button" class="btn btn-dark close-modal" data-dismiss="modal">
                        batal
                    </button>
                    <a href="/user">
                        <button type="button" class="btn btn-success">
                            Ubah
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection()

@section('js_code')
    <script>
        readyDB(function(db) {
            initUI();
            if (!db['FILTER_APP']['USER']['pin']) {
                $('#warning-modal-change-pin').modal('show');
            }
        });
    </script>
@endsection()
