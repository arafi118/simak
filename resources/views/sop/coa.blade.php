@extends('layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="akun"></div>
        </div>
    </div>

    <form action="" method="post" id="formCoa">
        @csrf

        @method('POST')
        <input type="hidden" name="id_akun" id="id_akun">
        <input type="hidden" name="nama_akun" id="nama_akun">
    </form>
@endsection

@section('script')
    <script>
        $('#akun').jstree({
            'core': {
                'check_callback': true,
                'data': {
                    'url': '/pengaturan/coa',
                }
            },
            'plugins': ['contextmenu', 'dnd', 'crrm'],
            'contextmenu': {
                'items': function($node) {
                    var tree = $('#akun').jstree(true);

                    var kode_akun = tree.get_node($node).id.split('.')
                    var lev1 = parseInt(kode_akun[0]);
                    var lev2 = parseInt(kode_akun[1]);
                    var lev3 = parseInt(kode_akun[2]);
                    var lev4 = parseInt(kode_akun[3]);

                    var items = {};
                    if (lev1 > 0 && lev2 > 0 && lev3 > 0 && lev4 == 0) {
                        var children = tree.get_node($node).children
                        var child_kode_akun = children[children.length - 1].split('.')
                        var child_lev1 = parseInt(child_kode_akun[0]);
                        var child_lev2 = parseInt(child_kode_akun[1]);
                        var child_lev3 = parseInt(child_kode_akun[2]);
                        var child_lev4 = parseInt(child_kode_akun[3]) + 1;
                        child_lev4 = child_lev4.padStart(2, '0');

                        items.Create = {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Tambah",
                            "action": function(obj) {
                                var id = `${child_lev1}.${child_lev2}.0${child_lev3}.${child_lev4}`
                                $node = tree.create_node($node, {
                                    "id": id,
                                    "text": id + ". Akun Baru",
                                });
                                tree.edit($node);
                            }
                        }
                    }

                    if ((lev1 > 0 && lev2 > 0 && lev3 > 0 && lev4 > 0) || tree.get_node($node).children
                        .length === 0) {
                        items.Rename = {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Edit",
                            "action": function(obj) {
                                tree.edit($node);
                            }
                        };
                        items.Remove = {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Hapus",
                            "action": function(obj) {
                                Swal.fire({
                                    title: 'Peringatan',
                                    text: 'Hapus akun ' + tree.get_node($node).text,
                                    showCancelButton: true,
                                    confirmButtonText: 'Hapus Kode Akun',
                                    cancelButtonText: 'Batal',
                                    icon: 'warning'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        tree.delete_node($node);
                                    }
                                })
                            }
                        };
                    }
                    return items;
                }
            }
        }).on('create_node.jstree', function(e, data) {
            var id = data.node.id
            var text = data.node.text

            $('#id_akun').val(id)
            $('#nama_akun').val(text)
            $('#formCoa input[name=_method]').val('POST')

            $('#formCoa').attr('action', '/pengaturan/coa')
            formSubmit('create', data)
        }).on('rename_node.jstree', function(e, data) {
            var id = data.node.id
            var text = data.node.text
            var old_text = data.old

            if (text != old_text) {
                $('#id_akun').val(id)
                $('#nama_akun').val(text)
                $('#formCoa input[name=_method]').val('PUT')

                $('#formCoa').attr('action', '/pengaturan/coa/' + id)
                formSubmit('update', data)
            }
        }).on('delete_node.jstree', function(e, data) {
            var id = data.node.id
            var text = data.node.text

            $('#id_akun').val(id)
            $('#nama_akun').val(text)
            $('#formCoa input[name=_method]').val('DELETE')

            $('#formCoa').attr('action', '/pengaturan/coa/' + id)
            formSubmit('delete', data)
        });

        function formSubmit(action, data = null) {
            var form = $('#formCoa')
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        if (action == 'create') {
                            data.instance.set_id(data.node, result.id);
                        }

                        if (action != 'delete') {
                            data.instance.set_text(data.node, result.nama_akun);
                        }

                        Toastr('success', result.msg)
                    } else {
                        if (action == 'create') {
                            data.instance.delete_node(data.node);
                        }

                        if (action == 'update') {
                            data.instance.set_text(data.node, data.node.old);
                        }

                        Toastr('warning', result.msg)
                    }
                }
            })
        }
    </script>
@endsection
