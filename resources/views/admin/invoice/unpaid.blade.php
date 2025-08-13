@extends('admin.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="invoice">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Usaha</th>
                            <th>Tgl Incoice</th>
                            <th>Jumlah</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table = $('#invoice').DataTable({
            language: {
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            processing: true,
            serverSide: true,
            ajax: '/db/unpaid',
            columns: [{
                data: 'nomor',
                name: 'nomor'
            }, {
                data: 'usaha.nama_usaha',
                name: 'usaha.nama_usaha'
            }, {
                data: 'tgl_invoice',
                name: 'tgl_invoice',
                render: function(data, type, row) {
                    return moment(new Date(data).toString()).format('DD/MM/YYYY');
                }
            }, {
                data: 'jumlah',
                name: 'jumlah'
            }, {
                data: 'saldo',
                name: 'saldo'
            }],
            order: [
                [0, 'desc']
            ]
        })

        $('#invoice').on('click', 'tbody tr', function(e) {
            var data = table.row(this).data();

            window.location.href = '/db/invoice/' + data.idv
        })
    </script>
@endsection
