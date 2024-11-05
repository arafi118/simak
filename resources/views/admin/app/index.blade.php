@extends('admin.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="/db/app/register" class="btn btn-primary btn-sm">Register App</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="usaha">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Usaha</th>
                            <th>Alamat</th>
                            <th>Domain</th>
                            <th>Masa Aktif</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table = $('#usaha').DataTable({
            language: {
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            processing: true,
            serverSide: true,
            ajax: '/db/app',
            columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'nama_usaha',
                name: 'nama_usaha'
            }, {
                data: 'alamat',
                name: 'alamat'
            }, {
                data: 'domain',
                name: 'domain'
            }, {
                data: 'masa_aktif',
                name: 'masa_aktif',
                render: function(data, type, row) {
                    return moment(new Date(data).toString()).format('DD/MM/YYYY');
                }
            }],
            order: [
                [0, 'asc']
            ]
        })

        $('#usaha').on('click', 'tbody tr', function(e) {
            var data = table.row(this).data();

            window.location.href = '/db/app/' + data.id
        })
    </script>
@endsection
