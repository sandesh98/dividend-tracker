<nav class="navbar bg-white border-bottom">
    <div class="container navbar-collapse">
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('portfolio.index') ? 'active' : '' }}" href="{{ route('portfolio.index') }}">Portfolio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dividend.index') ? 'active' : '' }}" href="{{ route('dividend.index') }}">Dividend</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transaction.index') ? 'active' : '' }}" href="{{ route('transaction.index') }}">Transacties</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('transaction.index') }}">Kosten</a>
            </li>
        </ul>

        <div x-data="{ open: false }" style="position: relative;">
            <button class="btn px-2 py-1 rounded" type="button" x-on:click="open = !open" x-on:click.outside="open = false">
                € 13240,10
            </button>

            <div x-show="open" x-cloak class="dropdown-menu p-0 m-0 mt-1 rounded-4 shadow border-0">
                <div class="card" style="width: 18rem;">
                    <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between"><span>Portfolio waarde</span><span class="fw-semibold">€ 13345,42</span></div>
                        <div class="d-flex justify-content-between"><span>Rendement</span><span class="fw-semibold text-success">€ 1334,44</span></div>
                        <div class="d-flex justify-content-between"><span>Transactiekosten</span><span class="fw-semibold text-danger">€ 25,00</span></div>
                        <div class="d-flex justify-content-between"><span>Dividend</span><span class="fw-semibold text-success">€ 435,53</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
