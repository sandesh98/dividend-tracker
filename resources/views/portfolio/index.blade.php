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

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th scope="col">Aandeel</th>
                    <th scope="col">Totale waarde</th>
                    <th scope="col">Tot geïnvesteerd</th>
                    <th scope="col">Aantal</th>
                    <th scope="col">GAK</th>
                    <th scope="col">Dividenden</th>
                    <th scope="col">Winst / Verlies</th>
                    <th scope="col">Winst / Verlies (ong.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocksData as $stock)
                <tr>
                    <td class="text-nowrap">
                        <a href="{{ route('portfolio.show', $stock['product']) }}" class="link-dark link-underline-opacity-0">{{ $stock['product'] }}</a>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between w-100 text-nowrap">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">2533,34</div>
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
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between w-100 text-nowrap">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">{{ $stock['averageStockPrice'] }}</div>
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
                            <span class="mr-auto"><i class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                            <div class="ml-auto">€ 838,34</div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex justify-content-between w-100 text-nowrap">
                            <span class="mr-auto"><i class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                            <div class="ml-auto">€ 838,34</div>
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
@endsection
