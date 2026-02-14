@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => '€ 3045,10', 'subheader' => 'Portfolio'])

    <div class="grid grid-cols-4 my-6">
        @include('portfolio.components.header-card', ['title' => 'Rendement (fictief)', 'value' => '244,39'])
        @include('portfolio.components.header-card', ['title' => 'Ontvangen dividend', 'value' => '200'])
        @include('portfolio.components.header-card', ['title' => 'Besteedbare ruimte (onjuist)', 'value' => $availableCash])
        @include('portfolio.components.header-card', ['title' => 'Gemaakte kosten', 'value' => $transactionCost])
    </div>

    <div class="card p-4 my-6">
        <p style="font-family:'AvertaStd-Bold';" class="mb-3">Effecten</p>
        <div class="flex gap-2 mb-3">
            <div class="asset-bar" style="width:50%"></div>
            <div class="asset-bar" style="width:35%"></div>
            <div class="asset-bar" style="width:15%"></div>
        </div>
        <div class="flex gap-3 text-muted">
            <span>• Aandelen</span><span>• ETF's</span><span>• Cash</span>
        </div>
    </div>

    <div class="table-wrap mb-4">
        <table class="table">
            <thead>
                <tr>
                    <th title="Naam van het aandeel">Aandeel</th>
                    <th title="Huidige prijs * koers">Marktwaarde</th>
                    <th title="Aankoopprijs * aantal">Tot geïnvesteerd</th>
                    <th title="Het aantal aandelingen in bezit">Aantal</th>
                    <th title="Gemiddelde aankoopkoers">GAK</th>
                    <th title="Totale winst of verlies">Totale winst / verlies</th>
                    <th title="Gerealiseerde waarde">Winst / verlies</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($active as $stock)
                    <tr>
                        <td>
                            <img src="{{ $stock['type'] === 'S' ? asset('build/icons/stock.svg') : asset('build/icons/etf.svg') }}">
                            <a href="{{ route('portfolio.show', $stock) }}">{{ $stock['product'] }}</a>
                        </td>
                        <td>€ {{ $stock['marketValue']->getAmount() }}</td>
                        <td>€ {{ $stock['totalAmountInvested']->getAmount() }}</td>
                        <td class="text-right">{{ $stock['quantity'] }}</td>
                        <td>€ {{ $stock['averageStockPrice'] }}</td>
                        <td>
                            <div class="flex justify-between"><span>€</span><span>{{ $stock['profitLoss'] }}</span></div>
                        </td>
                        <td>
                            <div class="flex justify-between"><span>€</span><span>{{ $stock['realizedProfitLoss'] }}</span></div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-data="{ open: true }" class="my-4">
        <button class="btn" x-on:click="open = !open" type="button" x-text="open ? 'Gesloten aandelen verbergen' : 'Alle gesloten aandelen'" style="margin-bottom:.75rem;"></button>

        <div x-show="open" x-cloak class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Gesloten aandeel</th>
                        <th>Laatste prijs</th>
                        <th>GVP</th>
                        <th>Totale winst / verlies</th>
                        <th>Prestatie na gesloten</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($closed as $stock)
                        <tr>
                            <td>
                                <img src="{{ $stock['type'] === 'S' ? asset('build/icons/stock.svg') : asset('build/icons/etf.svg') }}">
                                <a href="{{ route('portfolio.show', $stock) }}">{{ $stock['product'] }}</a>
                            </td>
                            <td>€ {{ $stock['lastPrice'] }}</td>
                            <td>€ {{ $stock['averageStockSellPrice'] }}</td>
                            <td>€ {{ $stock['profitLoss'] }}</td>
                            <td>€ 838,34</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
