@php
    use App\Models\Courier;
    use App\Models\Page;
    use Illuminate\Support\Str;

    $pages = Page::all();

    $findPage = function (array $titles) use ($pages) {
        foreach ($titles as $title) {
            $page = $pages->first(function ($item) use ($title) {
                return mb_strtolower(trim((string) $item->title)) === mb_strtolower(trim($title));
            });

            if ($page) {
                return $page;
            }
        }

        foreach ($titles as $title) {
            $page = $pages->first(function ($item) use ($title) {
                return Str::contains(
                    Str::lower((string) $item->title),
                    Str::lower($title)
                );
            });

            if ($page) {
                return $page;
            }
        }

        return null;
    };

    $rentACarPage = $pages->firstWhere('id', 8);

    $pageUrl = function (?Page $page) {
        if (!$page) {
            return '#';
        }

        return '/pagina/' . $page->id . '/' . Str::slug($page->title, '-');
    };

    $rentACarPage = $pages->firstWhere('id', 8);
    $aboutPage = $findPage(['Sobre nós', 'Sobre nos']);
    $partnersPage = $findPage(['Parceiros']);
    $contactsPage = $findPage(['Contactos', 'Contato', 'Contactos da empresa']);

    $couriers = Courier::all();
    $ownersCourier = $couriers->first(function ($item) {
        return Str::contains(Str::lower((string) $item->title), ['propriet', 'proprietar']);
    });
    $driversCourier = $couriers->first(function ($item) {
        return Str::contains(Str::lower((string) $item->title), ['motorist', 'condutor']);
    });

    $remainingCouriers = $couriers->filter(function ($item) use ($ownersCourier, $driversCourier) {
        return (!$ownersCourier || $item->id !== $ownersCourier->id)
            && (!$driversCourier || $item->id !== $driversCourier->id);
    })->values();

    if (!$ownersCourier && $remainingCouriers->count() > 0) {
        $ownersCourier = $remainingCouriers->shift();
    }

    if (!$driversCourier && $remainingCouriers->count() > 0) {
        $driversCourier = $remainingCouriers->shift();
    }

    $isAEmpresaActive =
        request()->is('pagina/*')
        || request()->is('tvde/formacao')
        || request()->is('tvde/consultadoria')
        || request()->is('tvde/transfers-tours');
@endphp

<nav class="navbar navbar-expand-lg fixed-top site-navbar">
    <div class="container py-1">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('assets/website/img/logo_white.png') }}" alt="Mundo TVDE">
        </a>

        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-lg-1 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/aluguer-de-viaturas') ? 'active' : '' }}" href="/tvde/aluguer-de-viaturas">
                        Aluguer de viaturas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/trabalhar-com-viatura-propria') ? 'active' : '' }}" href="/tvde/trabalhar-com-viatura-propria">
                        Slot
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pagina/7/seguros') ? 'active' : '' }}" href="/pagina/7/seguros">Seguros</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link {{ request()->is('tvde/estafetas') || request()->is('tvde/estafetas/*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Bolsa TVDE <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu">
                        @if ($ownersCourier)
                            <li>
                                <a class="dropdown-item" href="/tvde/estafetas/{{ $ownersCourier->id }}">
                                    Bolsa de proprietários TVDE
                                </a>
                            </li>
                        @endif
                        @if ($driversCourier)
                            <li>
                                <a class="dropdown-item" href="/tvde/estafetas/{{ $driversCourier->id }}">
                                    Bolsa de motoristas TVDE
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pagina/8/*') ? 'active' : '' }}" href="{{ $pageUrl($rentACarPage) }}">
                        Rent a Car
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/stand') || request()->is('tvde/stand/*') ? 'active' : '' }}" href="/tvde/stand">Stand</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link {{ $isAEmpresaActive ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        A empresa <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ $pageUrl($aboutPage) }}">
                                Sobre nós
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ $pageUrl($partnersPage) }}">
                                Parceiros
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ $pageUrl($contactsPage) }}">
                                Contactos
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/tvde/formacao">Formação</a></li>
                        <li><a class="dropdown-item" href="/tvde/consultadoria">Consultadoria</a></li>
                        <li><a class="dropdown-item" href="/tvde/transfers-tours">Transfers e Tours</a></li>
                    </ul>
                </li>

                @auth
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="btn btn-light btn-sm fw-semibold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Área reservada
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/admin">Painel</a></li>
                            <li>
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="btn btn-light btn-sm fw-semibold dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Entrar
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/login">Login</a></li>
                            <li><a class="dropdown-item" href="/register">Criar conta</a></li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
</form>


