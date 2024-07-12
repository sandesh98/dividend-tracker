@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => 'APPLE INC. - COMMON ST', 'subheader' => 'US0378331005'])

    <div class="row">
        <div class="col-8">

            <nav class="nav nav-pills" id="nav-tab" role="tablist">
                <a class="nav-link active rounded-pill" id="transactions" data-bs-toggle="tab"
                    data-bs-target="#nav-transactions" type="button" role="tab" aria-controls="transactions"
                    aria-selected="true">Transacties</a>
                <a class="nav-link rounded-pill" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Dividenden</a>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-transactions" role="tabpanel" aria-labelledby="transactions"
                    tabindex="0">

                    <div class="card bg-light border-0 px-4 py-3">
                        <div class="d-flex gap-3 flex-column">
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bolder fs-5">5 aandelen gekocht</span>
                                    <div class="d-flex gap-2">
                                        <span>10 januari 2020</span>
                                        <span>19:00</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="fw-semibold">€ 1045,23</span>
                                    <span>(inclusief € 2,00 transactiekosten)</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex gap-2">
                                    <span class="fw-semibold text-muted">Wisselkoers</span>
                                    <span class="fw-semibold text-muted">€ 1,0000 = $ 1,0875</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="fw-semibold text-muted">Kosten per aandeel</span>
                                    <span class="fw-semibold text-muted">$ 230 = € 250,12</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    De PP
                </div>
            </div>
        </div>
    </div>
@endsection
