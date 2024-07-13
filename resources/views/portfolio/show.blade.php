@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => 'APPLE INC. - COMMON ST', 'subheader' => 'US0378331005'])

    <div class="row">
        <div class="col-lg-8">

            <nav class="nav nav-pills" id="nav-tab" role="tablist">
                <a class="nav-link active rounded-pill" id="transactions" data-bs-toggle="tab"
                    data-bs-target="#nav-transactions" type="button" role="tab" aria-controls="transactions"
                    aria-selected="true">Transacties</a>
                <a class="nav-link rounded-pill" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Dividenden</a>
            </nav>

            <hr class="border opacity-80">

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-transactions" role="tabpanel" aria-labelledby="transactions"
                    tabindex="0">
                    
                    <div class="d-grid gap-3">
                        <div class="col-auto">
                            <span class="bg-dark text-white px-3 py-2 rounded date-badge">Januari 2024</span>
                        </div>
                        @for ($i = 0; $i < 10; $i++)
                            @include('portfolio.components.transaction-card-eur')   
                        @endfor
                    </div>

                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    De PP
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
                    <span class="fw-semibold">10</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>GAK</span>
                    <span class="fw-semibold">€ 104,54</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Totaal geïnvesteerd</span>
                    <span class="fw-semibold">€ 1645,20</span>
                </div>
                <hr class="border opacity-80 my-2">
                <div class="d-flex justify-content-between">
                    <span>Eerste transactie</span>
                    <span class="fw-semibold">10-03-2021</span>
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
