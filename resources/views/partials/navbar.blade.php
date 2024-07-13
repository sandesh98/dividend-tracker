<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('portfolio.index') ? 'active' : '' }}" aria-current="page"
                        href="{{ route('portfolio.index') }}">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dividend.index') ? 'active' : '' }}"
                        href="{{ route('dividend.index') }}">Dividend</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Transacties</a>
                </li>
            </ul>
            <div class="border px-2 py-1 rounded">
                € 13240,10
            </div>
        </div>
    </div>
</nav>
