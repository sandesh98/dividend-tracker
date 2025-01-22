@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => '€ 13045,10', 'subheader' => 'Portfolio'])

    <div class="row gy-3">
        @include('portfolio.components.header-card', ['title' => 'Rendement', 'value' => '2025,39'])
        @include('portfolio.components.header-card', ['title' => 'Ontvangen dividend', 'value' => '249,04'])
        @include('portfolio.components.header-card', ['title' => 'Besteedbare ruimte', 'value' => $availableCash])
        @include('portfolio.components.header-card', ['title' => 'Transactiekosten', 'value' => number_format(($transactionCosts / 100), 2, ',')])
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
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Naam van het aandeel">Aandeel</span></th>
                    <th scope="col"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Huidige prijs * koers">Totale waarde</span></th>
                    <th scope="col">Tot geïnvesteerd</th>
                    <th scope="col">Aantal</th>
                    <th scope="col">GAK</th>
                    <th scope="col">Dividenden</th>
                    <th scope="col">Winst / Verlies</th>
                    <th scope="col">Winst / Verlies (ong.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($active as $stock)
                <tr>
                    <td class="text-nowrap">
                        <a href="{{ route('portfolio.show', current($stock['isin'])) }}" class="link-dark link-underline-opacity-0">{{ $stock['product'] }}</a>
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
                            <div class="ml-auto">12,56</div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between w-100 text-nowrap">
                            <span class="d-flex gap-3">
                                <i class="bi {{ $stock['profitLoss'] < 0 ? 'bi-arrow-down-right-circle-fill text-danger' : 'bi-arrow-up-right-circle-fill text-success' }}"></i>
                                <span class="ml-2">€</span>
                            </span>
                            <div class="ml-auto">{{ $stock['profitLoss'] }}</div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between w-100 text-nowrap">
                            <span class="d-flex gap-3">
                                <i class="bi bi-arrow-up-right-circle-fill text-success"></i>
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
        <div :class="open ? 'card border-0 bg-active mb-3 custom-pointer' : 'card border-0 bg-light mb-3 custom-pointer'" x-on:click="open = !open; $nextTick(() => document.getElementById('myTable').scrollIntoView({ behavior: 'smooth' }))">
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
                            <th scope="col">Gesloten aandelen</th>
                            <th scope="col">Laatste prijs</th>
                            <th scope="col">GVP</th>
                            <th scope="col">Dividenden</th>
                            <th scope="col">Winst / Verlies</th>
                            <th scope="col">Prestatie na gesloten</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($closed as $stock)
        
                        <tr>
                            <td class="text-nowrap">
                                <a href="{{ route('portfolio.show', current($stock['isin'])) }}" class="link-dark link-underline-opacity-0">{{ $stock['product'] }}</a>
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
                                    <div class="ml-auto">{{ $stock['totalAmountInvested'] }}</div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-between w-100 text-nowrap">
                                    <span class="mr-auto">€</span>
                                    <div class="ml-auto">12,56</div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-between w-100 text-nowrap">
                                    <span class="d-flex gap-2">
                                        <i class="bi {{ $stock['profitLoss'] < 0 ? 'bi-arrow-down-right-circle-fill text-danger' : 'bi-arrow-up-right-circle-fill text-success' }}"></i>
                                        <span class="ml-2">€</span>
                                    </span>
                                    <div class="ml-auto">{{ $stock['profitLoss'] }}</div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex justify-content-between w-100 text-nowrap">
                                    <span class="d-flex gap-2">
                                        <i class="bi bi-arrow-up-right-circle-fill text-success"></i>
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
