@extends('layout.master')

@section('main')

    @include('partials.header', ['header' => 'Dividend'])

    <div>
        <canvas id="dividendChart"></canvas>
    </div>

    <div x-data="{ tab: '{{ $years[0] ?? '' }}' }">
        <nav class="nav nav-pills" role="tablist">
            @foreach ($years as $year)
                <a class="nav-link rounded-pill"
                   :class="tab === '{{ $year }}' ? 'active' : ''"
                   x-on:click.prevent="tab = '{{ $year }}'"
                   href="#">
                    {{ $year }}
                </a>
            @endforeach
        </nav>

        <hr class="border opacity-80">

        <div class="tab-content">
            @foreach ($years as $year)
                <div x-show="tab === '{{ $year }}'" x-cloak class="tab-pane fade show">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Aandeel</th>
                                <th scope="col">Waarde</th>
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
