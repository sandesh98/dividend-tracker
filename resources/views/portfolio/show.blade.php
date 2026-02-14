@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => $stock->display_name, 'subheader' => $stock->isin])

    <div class="two-col" x-data="{ tab: 'transactions' }">
        <section>
            <div class="tabs-list mb-3">
                <a href="#" class="tabs-trigger" :class="tab === 'transactions' ? 'is-active' : ''" x-on:click.prevent="tab = 'transactions'">Transacties</a>
                <a href="#" class="tabs-trigger" :class="tab === 'dividends' ? 'is-active' : ''" x-on:click.prevent="tab = 'dividends'">Dividenden</a>
            </div>

            <div x-show="tab === 'transactions'" x-cloak class="grid gap-3">
                <span class="badge badge-dark" style="width:max-content;">Januari 2024</span>
                @for ($i = 0; $i < 10; $i++)
                    @include('portfolio.components.transaction-card-eur')
                @endfor
            </div>

            <div x-show="tab === 'dividends'" x-cloak class="grid gap-3">
                <span class="badge badge-dark" style="width:max-content;">Januari 2024</span>
                @for ($i = 0; $i < 10; $i++)
                    @include('portfolio.components.dividend-card-eur')
                @endfor
            </div>
        </section>

        <aside>
            <div class="card p-4">
                <p style="font-family:'AvertaStd-Bold';" class="mb-3">Informatie</p>
                <div class="flex justify-between"><span class="text-muted">Totale waarde</span><span class="text-primary">€ 2533,34</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">Aantal aandelen</span><span>{{ $quantity }}</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">GAK</span><span>€ {{ $averageStockPrice }}</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">Totaal geïnvesteerd</span><span>€ 1645,20</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">Eerste transactie</span><span>{{ $date->date }}</span></div>

                <p style="font-family:'AvertaStd-Bold';" class="my-4">Dividenden</p>
                <div class="flex justify-between"><span class="text-muted">Dividend ontvangen bruto</span><span>€ 19,90</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">Dividendbelasting</span><span>- € 1,71</span></div>
                <hr>
                <div class="flex justify-between"><span class="text-muted">Dividend ontvangen netto</span><span>€ 12,19</span></div>
            </div>
        </aside>
    </div>
@endsection
