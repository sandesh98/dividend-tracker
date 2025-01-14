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
                    <a class="nav-link {{ request()->routeIs('transaction.index') ? 'active' : '' }}"
                        href="{{ route('transaction.index') }}">Transacties</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transaction.index') ? 'active' : '' }}"
                        href="{{ route('transaction.index') }}">Kosten</a>
                </li>
            </ul>

            <div class="dropdown">
                <button class="btn border px-2 py-1 rounded" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    € 13240,10
                </button>

                <div class="dropdown-menu  dropdown-menu-end p-0 m-0 mt-1 rounded-4 shadow border-0" aria-labelledby="dropdownMenuButton1">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between">
                                <span>Portfolio waarde</span>
                                <span class="fw-semibold">€ 13345,42</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Rendement</span>
                                <span class="fw-semibold text-success">€ 1334,44</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Transactiekosten</span>
                                <span class="fw-semibold text-danger">€ 25,00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Dividend</span>
                                <span class="fw-semibold text-success">€ 435,53</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
