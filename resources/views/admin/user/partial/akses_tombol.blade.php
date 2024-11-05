<form action="/db/user/{{ $user->id }}/hak_akses" method="post" id="AksesTombol">
    @csrf

    <input type="hidden" name="akses_menu" id="akses_menu" value="{{ implode('#', $menu) }}">
    <div id="accordion" class="collapse-icon accordion-icon-rotate left">
        @foreach ($MenuTombol as $mt)
            @php
                if (count($mt->tombol) < 1) {
                    continue;
                }
            @endphp
            <div class="card">
                <div class="card-header" id="menu{{ $mt->id }}">
                    <a class="card-title lead collapsed" data-toggle="collapse" data-target="#tombol{{ $mt->id }}"
                        aria-expanded="false" aria-controls="tombol{{ $mt->id }}" href="#">
                        {{ $mt->title }}
                    </a>
                </div>
                <div id="tombol{{ $mt->id }}" class="collapse" aria-labelledby="menu{{ $mt->id }}"
                    data-parent="#accordion">
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($mt->tombol as $tombol)
                                @php
                                    $checked = 'checked';
                                    if (in_array($tombol->id, explode('#', $user->akses_tombol))) {
                                        $checked = '';
                                    }
                                @endphp
                                <li class="list-group-item pt-1 pb-0">
                                    <label for="tombol-{{ $tombol->id }}">
                                        <input type="checkbox" name="tombol[{{ $tombol->id }}]"
                                            id="tombol-{{ $tombol->id }}" class="switchery" data-size="sm"
                                            {{ $checked }} />
                                        <label for="tombol-{{ $tombol->id }}">{{ $tombol->text }}</label>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</form>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <button type="button" id="Back" class="btn btn-sm btn-warning mr-2">Kembali</button>
            <button type="button" id="Simpan" class="btn btn-sm btn-success">Simpan</button>
        </div>
    </div>
</div>
