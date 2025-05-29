@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => '€ 3045,10', 'subheader' => 'Portfolio'])

    <div class="row gy-3">
        @include('portfolio.components.header-card', [
            'title' => 'Rendement (fictief)',
            'value' => '244,39',
        ])
        @include('portfolio.components.header-card', [
            'title' => 'Ontvangen dividend',
            'value' => $dividend,
        ])
        @include('portfolio.components.header-card', [
            'title' => 'Besteedbare ruimte (onjuist)',
            'value' => $availableCash,
        ])
        @include('portfolio.components.header-card', [
            'title' => 'Transactiekosten',
            'value' => $transactionCosts,
        ])
    </div>

    <div class="card p-4 my-5">
        <div class="fw-bolder fs-5">Effecten</div>
        <div class="d-flex my-3">
            <div class="bar rounded" style="width: 50%"></div>
            <div class="bar rounded mx-2" style="width: 35%"></div>
            <div class="bar rounded" style="width: 15%"></div>
        </div>
        <ul class="list-inline mb-0">
            <li class="list-inline-item">
                <div class="d-flex align-items-center">
                    <div class="bar-legend"></div>
                    Aandelen
                </div>
            </li>
            <li class="list-inline-item">
                <div class="d-flex align-items-center">
                    <div class="bar-legend"></div>
                    ETF's
                </div>
            </li>
            <li class="list-inline-item">
                <div class="d-flex align-items-center">
                    <div class="bar-legend"></div>
                    Cash
                </div>
            </li>
        </ul>
    </div>

    <div class="table-responsive mb-4">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Naam van het aandeel">Aandeel</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Huidige prijs * koers">Totale waarde</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Aankoopprijs * aantal">Tot geïnvesteerd</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Het aantal aandelingen in bezetting">Aantal</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Gemiddelde aankoopkoers">GAK</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Totaal ontvangen dividend">Dividenden</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Totale winst of verlies van een aandeel inclusief transactiekosten en dividenden">Totale
                            winst / verlies</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="Gerealiseerde waarde zonder transactiekosten en dividenden">Winst /
                            verlies</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($active as $stock)
                    <tr>
                        <td class="text-nowrap">
                            <img
                                src="{{ $stock['type'] === 'S' ? asset('build/icons/stock.svg') : asset('build/icons/etf.svg') }}">
                            <a href="{{ route('portfolio.show', $stock) }}"
                                class="link-dark link-underline-opacity-0">{{ $stock['product'] }}</a>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">{{ $stock['totalValue'] }}</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">{{ $stock['totalAmountInvested'] }}</div>
                            </div>
                        </td>
                        <td class="text-nowrap text-end">
                            {{ $stock['quantity'] }}
                        </td>
                        <td class="text-nowrap" style="width: 150px;">
                            <div class="d-flex justify-content-between w-100 text-nowrap gap-4">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">{{ $stock['averageStockPrice'] }}</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap gap-3">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">{{ $stock['dividend'] }}</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="d-flex gap-3">
                                    {{-- Make this conditional--}}
                                    <img src="{{ asset('build/icons/graph-down.svg') }}"
                                        alt="graph down icon">
                                    <span class="ml-2">€</span>
                                </span>
                                <div class="ml-auto">{{ $stock['profitLoss'] }}</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="d-flex gap-3">
                                    <img src="{{ $stock['rializedProfitLoss'] > 0 ? asset('build/icons/graph-up.svg') : asset('build/icons/graph-down.svg') }}"
                                        alt="graph up icon">
                                    <span class="ml-2">€</span>
                                </span>
                                <div class="ml-auto">{{ $stock['rializedProfitLoss'] }}</div>
                            </div>
                        </td>
                    </tr>
                @endforeach

            </tbody>

            <tfoot>
                <tr>
                    <td class="text-muted fs-6">Aantal (2)</td>
                    <td class="text-muted text-end">4483,90</td>
                    <td class="text-muted text-end">3435,98</td>
                    <td class="text-muted text-end">45</td>
                    <td class="text-muted text-end">170,65</td>
                    <td class="text-muted text-end">93,03</td>
                    <td class="text-muted text-end">994,46</td>
                    <td class="text-muted text-end">1069,87</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div x-data="{ open: true }">
        <div :class="open ? 'card border-0 bg-active mb-3 custom-pointer' : 'card border-0 bg-light mb-3 custom-pointer'"
            x-on:click="open = !open; $nextTick(() => document.getElementById('myTable').scrollIntoView({ behavior: 'smooth' }))">
            <div class="card-body py-2">
                <div class="d-flex align-items-center gap-2">
                    <i :class="open ? 'bi bi-plus fs-4' : 'bi bi-dash fs-4'"></i>
                    <span>Alle gesloten aandelen</span>
                </div>
            </div>
        </div>

        <div id="myTable">
            <div x-show="open" class="table-responsive" x-transition.delay.50ms>
                <table class="table">
                    <thead>
                        <tr class="text-nowrap">
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Naam van het aandeel">Gesloten aandeel</span></th>
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Laatste koersprijs">Laatste prijs</span></th>
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Gemiddelde verkoopprijs">GVP</span></th>
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Totaal ontvangen dividend">Dividenden</span></th>
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Totale winst of verlies van een aandeel inclusief transactiekosten en dividenden">Totale
                                    winst / verlies</span></th>
                            <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="Prestatie van een aandeel na de laatste verkoop transactie">Prestatie na
                                    gesloten</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($closed as $stock)
                            <tr>
                                <td class="text-nowrap">
                                    <img
                                        src="{{ $stock['type'] === 'S' ? asset('build/icons/stock.svg') : asset('build/icons/etf.svg') }}">
                                    <a href="{{ route('portfolio.show', $stock) }}"
                                        class="link-dark link-underline-opacity-0">{{ $stock['product'] }}</a>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-between w-100 text-nowrap">
                                        <span class="mr-auto">€</span>
                                        <div class="ml-auto">{{ $stock['lastPrice'] }}</div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-between w-100 text-nowrap">
                                        <span class="mr-auto">€</span>
                                        <div class="ml-auto">{{ $stock['averageStockSellPrice'] }}</div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-between w-100 text-nowrap">
                                        <span class="mr-auto">€</span>
                                        <div class="ml-auto">{{ $stock['dividend'] }}</div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-between w-100 text-nowrap">
                                        <span class="d-flex gap-2">
                                            <img src="{{ asset('build/icons/graph-down.svg') }}"
                                                alt="graph down icon">
                                            <span class="ml-2">€</span>
                                        </span>
                                        <div class="ml-auto">{{ $stock['profitLoss'] }}</div>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex justify-content-between w-100 text-nowrap">
                                        <span class="d-flex gap-2">
                                            <img src="{{ asset('build/icons/graph-up.svg') }}" alt="graph up icon">
                                            <span class="ml-2">€</span>
                                        </span>
                                        <div class="ml-auto">838,34</div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <td class="text-muted fs-6">Aantal (2)</td>
                            <td class="text-muted text-end">4483,90</td>
                            <td class="text-muted text-end">3435,98</td>
                            <td class="text-muted text-end">170,65</td>
                            <td class="text-muted text-end">994,46</td>
                            <td class="text-muted text-end">1069,87</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
