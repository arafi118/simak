@extends('admin.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label" for="lokasi">Lokasi</label>
                <select class="form-control select2" name="lokasi" id="lokasi">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($lokasi as $lok)
                        <option value="{{ $lok->id }}">
                            {{ $lok->nama_usaha }}
                        </option>
                    @endforeach
                </select>
                <small class="text-danger" id="msg_lokasi"></small>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped" id="DaftarUser">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Level</th>
                        <th>Jabatan</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table;

        $(document).on('change', '#lokasi', function(e) {
            e.preventDefault()

            if (table) {
                // table.clear().draw();
                table.destroy();
            }

            if ($(this).val().length > 0) {
                CreateTable($(this).val())
            }
        })

        $('.table').on('click', 'tbody tr', function(e) {
            var data = table.row(this).data();

            window.open('/db/user/' + data.id)
        })

        function CreateTable(lokasi) {
            console.log(lokasi)
            table = $('#DaftarUser').DataTable({
                language: {
                    paginate: {
                        previous: "&laquo;",
                        next: "&raquo;"
                    }
                },
                processing: true,
                serverSide: true,
                ajax: "/db/user/lokasi/" + lokasi,
                columns: [{
                        data: 'namadepan',
                        name: 'namadepan'
                    },
                    {
                        data: 'l.deskripsi_level',
                        name: 'l.deskripsi_level'
                    },
                    {
                        data: 'j.nama_jabatan',
                        name: 'j.nama_jabatan'
                    },
                    {
                        data: 'uname',
                        name: 'uname'
                    },
                    {
                        data: 'pass',
                        name: 'pass'
                    },
                ]
            })
        }
    </script>
@endsection
