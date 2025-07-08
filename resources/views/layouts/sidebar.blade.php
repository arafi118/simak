<div class="main-menu menu-fixed menu-dark menu-bg-default rounded menu-accordion menu-shadow">
    <div class="main-menu-content">
        <a class="navigation-brand d-none d-md-flex d-lg-flex d-xl-flex justify-content-center" href="/dashboard">
            <div class="avatar avatar-online avatar-md">
                <img class="brand-logo" alt="Logo" src="{{ asset('storage/logo/' . Session::get('logo')) }}" />
            </div>
        </a>
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @foreach (Session::get('menu') as $menu)
                @php
                    $active = active('', $menu->link);
                    if (count($menu->child) > 0) {
                        foreach ($menu->child as $child) {
                            $child_link = explode('/', $child->link);
                            $active = active('', '/' . end($child_link));

                            if ($active == 'active') {
                                break;
                            }
                        }
                    }
                @endphp
                <li class="{{ $active }} nav-item">
                    <a href="{{ $menu->link }}">
                        <i class="{{ $menu->ikon }}"></i>
                        <span class="menu-title">{{ $menu->title }}</span>
                    </a>

                    @if (count($menu->child) > 0)
                        <ul class="menu-content">
                            @foreach ($menu->child as $child)
                                <li>
                                    <a class="menu-item" href="{{ $child->link }}">{{ $child->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
