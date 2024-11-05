<div class="card">
    <div class="card-body">
        <form action="/db/user/{{ $user->id }}/akses_tombol" method="post" id="AksesMenu">
            @csrf

            <ul class="list-group list-group-flush">
                @foreach ($menu as $m)
                    @php
                        $checked = 'checked';
                        if (in_array($m->id, explode('#', $user->akses_menu))) {
                            $checked = '';
                        }
                    @endphp
                    <li class="list-group-item pt-1 pb-0">
                        <label for="menu-{{ $m->id }}">
                            <input type="checkbox" name="menu[{{ $m->id }}]" id="menu-{{ $m->id }}"
                                class="switchery" data-size="sm" {{ $checked }} />
                            <label for="menu-{{ $m->id }}">{{ $m->title }}</label>
                        </label>

                        @if (count($m->child) > 0)
                            <div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($m->child as $c)
                                        @php
                                            $checked = 'checked';
                                            if (in_array($c->id, explode('#', $user->akses_menu))) {
                                                $checked = '';
                                            }
                                        @endphp
                                        <li class="list-group-item pt-1 pb-0">
                                            <label for="menu-{{ $c->id }}">
                                                <input type="checkbox" data-parent="menu-{{ $m->id }}"
                                                    name="menu[{{ $c->id }}]" id="menu-{{ $c->id }}"
                                                    class="switchery" data-size="sm" {{ $checked }} />
                                                <label for="menu-{{ $c->id }}">{{ $c->title }}</label>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </form>

        <div class="d-flex justify-content-end mt-3">
            <button type="button" id="Next" class="btn btn-sm btn-success">Lanjutkan</button>
        </div>
    </div>
</div>
