@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => $stock->display_name, 'subheader' => $stock->isin])

    <div class="row" x-data="{ tab: 'transactions' }">
        <div class="col-lg-8">

            <nav class="nav nav-pills" role="tablist">
                <a class="nav-link rounded-pill" :class="tab === 'transactions' ? 'active' : ''" href="#" x-on:click.prevent="tab = 'transactions'">Transacties</a>
                <a class="nav-link rounded-pill" :class="tab === 'dividends' ? 'active' : ''" href="#" x-on:click.prevent="tab = 'dividends'">Dividenden</a>
            </nav>

            <hr class="border opacity-80">

            <div class="tab-content">
                <div x-show="tab === 'transactions'" x-cloak class="tab-pane fade show active">
                    <div class="d-grid gap-3">
                        <div class="col-auto">
                            <span class="bg-dark text-white px-2 py-2 rounded date-badge">Januari 2024</span>
                        </div>
                        @for ($i = 0; $i < 10; $i++)
                            @include('portfolio.components.transaction-card-eur')
                        @endfor
                    </div>
                </div>

                <div x-show="tab === 'dividends'" x-cloak class="tab-pane fade show">
                    <div class="d-grid gap-3">
                        <div class="col-auto">
                            <span class="bg-dark text-white px-2 py-2 rounded date-badge">Januari 2024</span>
                        </div>
                        @for ($i = 0; $i < 10; $i++)
                            @include('portfolio.components.dividend-card-eur')
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card sticky-top px-3 py-4">
                <span class="fw-bold pb-3">Informatie</span>
                <div class="d-flex justify-content-between">
                    <span>Totale waarde</span>
                    <span class="text-purple fw-semibold">€ 2533,34</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Aantal aandelen</span>
                    <span class="fw-semibold">{{ $quantity }}</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>GAK</span>
                    <span class="fw-semibold">€ {{ $averageStockPrice }}</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Totaal geïnvesteerd</span>
                    <span class="fw-semibold">€ 1645,20</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Eerste transactie</span>
                    <span class="fw-semibold">{{ $date->date }}</span>
                </div>

                <span class="fw-bold pb-3 pt-4">Dividenden</span>
                <div class="d-flex justify-content-between">
                    <span>Dividend ontvangen bruto</span>
                    <span class="fw-bold">€ 19,90</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Dividendbelasting</span>
                    <span class="fw-bold">- € 1,71</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Dividend ontvangen netto</span>
                    <span class="fw-bold">€ 12,19</span>
                </div>
            </div>
        </div>
    </div>
@endsection
