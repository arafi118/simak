@php
    use App\Utils\Tanggal;
@endphp

@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body" id="profile">
                <div class="row justify-content-start align-items-center">
                    <div class="col-sm-auto col-4">
                        <div class="avatar position-relative pointer" id="fileUpload" style="width: 74px; height: 74px;">
                            <img src="{{ asset('/storage/profil/' . $user->foto) }}" alt="bruce"
                                class="w-100 rounded-circle shadow" id="preview">
                        </div>

                        <form action="/profil/{{ $user->id }}" method="post" enctype="multipart/form-data"
                            id="formUpload">
                            @csrf
                            @method('PUT')

                            <input type="file" name="logo" id="logo" class="d-none">
                        </form>
                    </div>
                    <div class="col-sm-auto col-8 my-auto">
                        <div class="h-100">
                            <h5 class="font-weight-bold mb-0 nama_user">
                                {{ Session::get('nama') }}
                            </h5>
                            <p class="mb-0 font-weight-normal text-sm">
                                @if ($user->level == '1' && $user->jabatan == '1')
                                    {{ $usaha->kepala_lembaga }}
                                @elseif ($user->level == '1' && $user->jabatan == '2')
                                    {{ $usaha->kabag_administrasi }}
                                @elseif ($user->level == '1' && $user->jabatan == '3')
                                    {{ $usaha->kabag_keuangan }}
                                @else
                                    {{ $user->j ? $user->j->nama_jabatan : '' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Data Diri</h5>
                </div>
                <div class="card-body">
                    <form action="/profil/{{ $user->id }}" method="post" id="formDataDiri">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="type" id="_type" value="data_diri">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>NIK</label>
                                    <input type="text" class="form-control form-control-sm" name="nik" id="nik"
                                        placeholder="{{ str_replace('.', '', $user->u->kd_desa) }}"
                                        value="{{ $user->nik }}" maxlength="16">
                                    <small class="text-danger" id="msg_nik"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Depan</label>
                                    <input type="text" name="nama_depan" id="nama_depan"
                                        class="form-control form-control-sm" placeholder="Nama Depan"
                                        value="{{ $user->namadepan }}">
                                    <small class="text-danger" id="msg_nama_depan"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Belakang</label>
                                    <input type="text" name="nama_belakang" id="nama_belakang"
                                        class="form-control form-control-sm" placeholder="Nama Belakang"
                                        value="{{ $user->namabelakang }}">
                                    <small class="text-danger" id="msg_nama_belakang"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Inisial</label>
                                    <input type="text" name="inisial" id="inisial" class="form-control form-control-sm"
                                        placeholder="Ins" value="{{ $user->ins }}">
                                    <small class="text-danger" id="msg_inisial"></small>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" id="tempat_lahir"
                                        class="form-control form-control-sm"
                                        placeholder="{{ $user->u->d->kec->kabupaten->nama_kab }}"
                                        value="{{ $user->tempat_lahir }}">
                                    <small class="text-danger" id="msg_tempat_lahir"></small>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input autocomplete="off" type="text" name="tanggal_lahir" id="tanggal_lahir"
                                        class="form-control form-control-sm date"
                                        value="{{ Tanggal::tglIndo($user->tgl_lahir) }}">
                                    <small class="text-danger" id="msg_tanggal_lahir"></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea name="alamat" id="alamat" class="form-control form-control-sm" placeholder="Alamat">{{ $user->alamat }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Telpon</label>
                                    <input type="text" name="telpon" id="telpon"
                                        class="form-control form-control-sm" placeholder="628"
                                        value="{{ $user->hp }}" maxlength="13">
                                    <small class="text-danger" id="msg_telpon"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="pendidikan">Pendidikan</label>
                                    <select class="form-control select2" name="pendidikan" id="pendidikan">
                                        @foreach ($pendidikan as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $p->id == $user->pendidikan ? 'selected' : '' }}>
                                                {{ $p->deskripsi_p }} ({{ $p->tingkat }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-danger" id="msg_pendidikan"></small>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="menjabat_sejak">Menjabat Sejak</label>
                                    <input autocomplete="off" type="text" name="menjabat_sejak" id="menjabat_sejak"
                                        class="form-control form-control-sm date"
                                        value="{{ Tanggal::tglIndo($user->sejak) }}">
                                    <small class="text-danger" id="msg_menjabat_sejak"></small>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="d-flex justify-content-end">
                        <button type="button" data-toggle="modal" data-target="#EditUser"
                            class="btn btn-info btn-sm mb-0">
                            Edit User
                        </button>
                        <button type="submit" id="SimpanDataDiri" class="btn btn-warning btn-sm mb-0 ml-1">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit User --}}
        <div class="modal fade" id="EditUser" tabindex="-1" aria-labelledby="EditUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title mb-0" id="EditUserLabel">
                            Edit User Login
                        </h1>
                    </div>
                    <div class="modal-body">
                        <form action="/profil/{{ $user->id }}" method="post" id="FormEditUser">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="type" id="type" value="data_user">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" class="form-control form-control-sm" name="username"
                                            id="username" value="{{ $user->uname }}">
                                        <small class="text-danger" id="msg_username"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control form-control-sm" name="password"
                                            id="password" disabled value="{{ $pass }}">
                                        <small class="text-danger" id="msg_password"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Password Baru</label>
                                        <input type="password" class="form-control form-control-sm" name="password_baru"
                                            id="password_baru">
                                        <small class="text-danger" id="msg_password_baru"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Konfirmasi Password</label>
                                        <input type="password" class="form-control form-control-sm"
                                            name="konfirmasi_password" id="konfirmasi_password">
                                        <small class="text-danger" id="msg_konfirmasi_password"></small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm mb-0" data-dismiss="modal">Tutup</button>
                        <button type="button" id="SimpanEditUser"
                            class="btn bg-warning btn-sm float-end mb-0 text-white">Simpan
                            Perubahan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        $(document).on('click', '#SimpanDataDiri', function(e) {
            e.preventDefault()

            var form = $('#formDataDiri')
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire('Berhasil', result.msg,
                            'success')

                        $('.nama_user').html(result.user.namadepan + ' ' + result.user.namabelakang)
                    }
                },
                error: function(result) {
                    const respons = result.responseJSON;

                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error')
                    $.map(respons, function(res, key) {
                        $('#' + key).parent('.input-group.input-group-static').addClass(
                            'is-invalid')
                        $('#msg_' + key).html(res)
                    })
                }
            })
        })

        $(document).on('input', '#inisial', function() {
            var inisial = $(this).val()

            if (inisial.length >= 2) {
                $(this).val(inisial.substring(0, 2))
            }
        })

        $(document).on('click', '#SimpanEditUser', function(e) {
            e.preventDefault()

            var form = $('#FormEditUser')
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            title: 'Berhasil',
                            icon: 'success',
                            text: result.msg,
                            showCancelButton: false,
                            confirmButtonText: 'Login Ulang'
                        }).then((result) => {
                            $('#formLogout').submit()
                        })
                    } else {
                        Swal.fire('Error', result.msg, 'error')
                    }
                },
                error: function(result) {
                    const respons = result.responseJSON;

                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error')
                    $.map(respons, function(res, key) {
                        $('#' + key).parent('.input-group.input-group-static').addClass(
                            'is-invalid')
                        $('#msg_' + key).html(res)
                    })
                }
            })
        })

        $(document).on('click', '#fileUpload', function(e) {
            e.preventDefault()

            $('#logo').trigger('click')
        })

        $(document).on('change', '#logo', function(e) {
            e.preventDefault()

            var logo = $(this).get(0).files[0]
            if (logo) {


                var form = $('#formUpload')
                var formData = new FormData(document.querySelector('#formUpload'));
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        if (result.success) {
                            var reader = new FileReader();

                            reader.onload = function() {
                                $("#preview").attr("src", reader.result);
                                $("#profil_avatar").attr("src", reader.result);
                            }

                            reader.readAsDataURL(logo);
                        }
                    }
                })
            }
        })
    </script>
@endsection
