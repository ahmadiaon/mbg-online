@extends('app.layout.main')

@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="faq-wrap">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-block" data-toggle="collapse" data-target="#faq1">
                                Informasi akun
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
                                    <p class="text-center text-muted font-16 identitas-user-NAMA-KARYAWAN">
                                        NAMA LENGKAP
                                    </p>
                                    <h5 class="text-center mb-20 h5 text-blue identitas-user-JABATAN">JABATAN KARYAWAN</h5>
                                    <div id="form-user" class="profile-info">
                                        <h5 class="mb-20 h5 text-blue">Informasi User</h5>
                                        <ul>
                                            <li>
                                                <span>Nomor Registrasi Karyawan (NRP)</span>
                                                <div class="row">
                                                    <div class="col-10">
                                                        <input disabled class="form-control identitas-user-NRP pr-20"
                                                            value="000000" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <span>Email</span>
                                                <div class="row">
                                                    <div class="col-10">
                                                        <input class="form-control identitas-user-EMAIL"
                                                            value="contoh@mitrabarito.com" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <span>No HP</span>
                                                <div class="row">
                                                    <div class="col-10">
                                                        <input class="form-control identitas-user-NO-HP"
                                                            value="628-1255-8970-44" />
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <span>PIN</span>
                                                <div class="row">
                                                    <div class="col-md-8 col-10 pin-display mb-10">
                                                        <input disabled class="form-control identitas-user-PIN"
                                                            value="** ** **" />


                                                    </div>
                                                    <div class="col-md-2 pin-display">
                                                        <button type="button" id="btnToggle"
                                                            class="btn btn-primary">GANTI</button>

                                                    </div>
                                                    <div hidden class="col-md-10 col-10 pin-edit">
                                                        <div class="form-group justify-center row updatePinForm">
                                                            <div class="col-sm-12 col-md-12 btn-group">
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="text-right">

                                                <div class="row">
                                                    <div class="col-10">

                                                        <button hidden disabled type="button" id="btn-simpan-user"
                                                            onclick="simpanUser()" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </div>
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
@endsection()

@section('js_code')
    <script>
        readyDB(function(db) {
            $('.identitas-user-NRP').val(db?.FILTER_APP?.PROFILE?.NRP?.text_data ?? null);
            $('.identitas-user-EMAIL').val(db?.FILTER_APP?.PROFILE?.EMAIL?.text_data ?? null);
            $('.identitas-user-NO-HP').val(db?.FILTER_APP?.PROFILE?.["NO-HP"]?.text_data ?? null);
            $('.identitas-user-JABATAN').text(db?.FILTER_APP?.PROFILE?.JABATAN?.text_data ?? "-");
            $('.identitas-user-NAMA-KARYAWAN').text(db?.FILTER_APP?.PROFILE?.["NAMA-KARYAWAN"]?.text_data ?? "-");
        });

        $("#btnToggle").click(function() {
            $('.pin-edit').prop('hidden', false);
            $('.pin-display').prop('hidden', true);
            $('#btn-simpan-user').prop('hidden', false);
            $('#btn-simpan-user').prop('disabled', false);

        });

        function getPinInsert() {
            return `${getInputValue('pinNumber-1')}${getInputValue('pinNumber-2')}${getInputValue('pinNumber-3')}${getInputValue('pinNumber-4')}${getInputValue('pinNumber-5')}${getInputValue('pinNumber-6')}`;
        }

        function simpanUser() {
            startLoading();
            var pinInsert = getPinInsert();
            var objPinInsert = {
                name: "pin",
                value: null
            }


            if (pinInsert.length > 1) {
                conLog('formDataArray', 'formDataArray')
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
                    id_email:db?.FILTER_APP?.PROFILE?.EMAIL?.id ?? null,
                    id_no_hp:db?.FILTER_APP?.PROFILE?.['NO-HP']?.id ?? null,
                },
                success: function(response) {

                    if (objPinInsert.value) {
                        window.location.href = "/auth/login";
                    }
                    conLog('response', response);
                    showModalSuccess();
                    stopLoading();
                },
                error: function(response) {
                    conLog('error', response);
                    stopLoading();
                    //alertModal()
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // Menggunakan event input untuk mengawasi perubahan pada input
            $('#form-user').on('input', '.pinNumber', function() {
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
@endsection()
