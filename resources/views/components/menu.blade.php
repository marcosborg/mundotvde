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
                <li class="nav-item dropdown">
                    <a class="nav-link {{ request()->is('tvde/aluguer-de-viaturas') || request()->is('tvde/consultadoria') || request()->is('tvde/formacao') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        TVDE <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/tvde/aluguer-de-viaturas">Aluguer de viaturas</a></li>
                        <li><a class="dropdown-item" href="/tvde/formacao">Formação</a></li>
                        <li><a class="dropdown-item" href="/tvde/consultadoria">Consultadoria</a></li>
                    </ul>
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
                        @foreach (\App\Models\Courier::all() as $item)
                            <li><a class="dropdown-item" href="/tvde/estafetas/{{ $item->id }}">{{ $item->title }}</a></li>
                        @endforeach
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/transfers-tours') ? 'active' : '' }}" href="/tvde/transfers-tours">Transfers e Tours</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/stand') || request()->is('tvde/stand/*') ? 'active' : '' }}" href="/tvde/stand">Stand</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tvde/trabalhar-com-viatura-propria') ? 'active' : '' }}" href="/tvde/trabalhar-com-viatura-propria">
                        Viatura própria
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link {{ request()->is('pagina/*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        A empresa <i class="bi bi-chevron-down ms-1"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (App\Models\Page::get() as $page)
                            <li>
                                <a class="dropdown-item" href="/pagina/{{ $page->id }}/{{ Illuminate\Support\Str::slug($page->title, '-') }}">
                                    {{ $page->title }}
                                </a>
                            </li>
                        @endforeach
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

