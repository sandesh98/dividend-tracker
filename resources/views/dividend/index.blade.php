@extends('layout.master')

@section('main')
    @include('partials.header', ['header' => 'Dividend'])

    <div class="card p-4 my-6">
        <canvas id="dividendChart"></canvas>
    </div>

    <div x-data="{ tab: '{{ $years[0] ?? '' }}' }">
        <div class="tabs-list">
            @foreach ($years as $year)
                <a href="#" class="tabs-trigger" :class="tab === '{{ $year }}' ? 'is-active' : ''" x-on:click.prevent="tab = '{{ $year }}'">{{ $year }}</a>
            @endforeach
        </div>

        <div class="my-4">
            @foreach ($years as $year)
                <div x-show="tab === '{{ $year }}'" x-cloak class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Aandeel</th>
                                <th>Waarde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dividendByYear[$year] as $dividend)
                                <tr>
                                    <td>{{ $dividend['name'] }}</td>
                                    <td>{{ $dividend['amount'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
@endsection
