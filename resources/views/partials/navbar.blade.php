<nav class="nav-shell">
    <div class="container nav-inner">
        <div class="nav-links">
            <a class="nav-link {{ request()->routeIs('portfolio.index') ? 'is-active' : '' }}" href="{{ route('portfolio.index') }}">Portfolio</a>
            <a class="nav-link {{ request()->routeIs('dividend.index') ? 'is-active' : '' }}" href="{{ route('dividend.index') }}">Dividend</a>
            <a class="nav-link {{ request()->routeIs('transaction.index') ? 'is-active' : '' }}" href="{{ route('transaction.index') }}">Transacties</a>
            <a class="nav-link" href="{{ route('transaction.index') }}">Kosten</a>
        </div>

        <div x-data="{ open: false }" style="position: relative;">
            <button class="btn" x-on:click="open = !open" x-on:click.outside="open = false" type="button">€ 13240,10</button>
            <div class="popover" x-show="open" x-cloak>
                <div class="flex justify-between mb-2"><span class="text-muted">Portfolio waarde</span><span>€ 13345,42</span></div>
                <div class="flex justify-between mb-2"><span class="text-muted">Rendement</span><span class="text-success">€ 1334,44</span></div>
                <div class="flex justify-between mb-2"><span class="text-muted">Transactiekosten</span><span class="text-danger">€ 25,00</span></div>
                <div class="flex justify-between"><span class="text-muted">Dividend</span><span class="text-success">€ 435,53</span></div>
            </div>
        </div>
    </div>
</nav>
