@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => 'Transacties'])

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Aandeel</th>
                    <th>Actie</th>
                    <th>Bedrag</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>10-03-2020</td>
                    <td>APPLE COMMON ST.</td>
                    <td>3 aandelen gekocht</td>
                    <td>
                        <div class="flex justify-between">
                            <span class="text-success">↗</span>
                            <span>€ 838,34</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
