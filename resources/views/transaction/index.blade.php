@extends('layout.master')

@section('main')

    @include('partials.header', ['header' => 'Transacties'])

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="text-nowrap fw-bold">
                    <th scope="col">Datum</th>
                    <th scope="col">Aandeel</th>
                    <th scope="col">Actie</th>
                    <th scope="col">Bedrag</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-nowrap">
                        10-03-2020
                    </td>
                    <td class="text-nowrap">
                        APPLE COMMON ST.
                    </td>
                    <td class="text-nowrap">
                        3 aandelen gekocht
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex gap-2">
                            <span class="mr-auto"><i class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                            <div class="ml-auto">€ 838,34</div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
@endsection