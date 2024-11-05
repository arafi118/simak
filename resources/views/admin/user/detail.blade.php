@extends('admin.layouts.base')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade active show" id="akses-menu" role="tabpanel" aria-labelledby="akses-menu-tab">
                    @include('admin.user.partial.akses_menu')
                </div>
                <div class="tab-pane fade" id="akses-tombol" role="tabpanel" aria-labelledby="akses-tombol-tab">
                    <div id="LayoutAksesTombol"></div>
                </div>
            </div>
        </div>
        <div class="col-12 d-none">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active show" id="akses-menu-tab" data-toggle="pill" href="#akses-menu" role="tab"
                    aria-controls="akses-menu" aria-selected="true">Akses Menu</a>
                <a class="nav-link" id="akses-tombol-tab" data-toggle="pill" href="#akses-tombol" role="tab"
                    aria-controls="akses-tombol" aria-selected="false">Akses Tombol</a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function switchChecked(checkbox, checked) {
            if ($(checkbox).prop('checked') !== checked) {
                $(checkbox).prop('checked', checked);

                if (checkbox.switchery) {
                    checkbox.switchery.setPosition();
                }
            }
        }

        $(document).on('change', 'input[type=checkbox]', function() {
            let id = $(this).attr('id');
            let dataParent = $(this).attr('data-parent');

            if (dataParent) {
                let parentCheckbox = $('#' + dataParent)[0];
                switchChecked(parentCheckbox, true);
            }

            let isChecked = $(this).prop('checked');
            $('input[data-parent=' + id + ']').each(function() {
                switchChecked(this, isChecked);
            });

            if (dataParent && $('input[data-parent=' + dataParent + ']:checked').length === 0) {
                let parentCheckbox = $('#' + dataParent)[0];
                switchChecked(parentCheckbox, false);
            }
        });

        $(document).on('click', '#Next', function(e) {
            e.preventDefault();

            var form = $('#AksesMenu')
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        $('#LayoutAksesTombol').html(result.view)
                        $('#akses-tombol-tab').trigger('click')

                        SetSwitchery()
                    }
                }
            })
        })

        $(document).on('click', '#Back', function(e) {
            e.preventDefault();

            $('#akses-menu-tab').trigger('click')
        })

        $(document).on('click', '#Simpan', function(e) {
            e.preventDefault();

            var form = $('#AksesTombol')
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire('Berhasil', result.msg, 'success').then(() => {
                            window.close()
                        })
                    }
                }
            })
        })
    </script>
@endsection
