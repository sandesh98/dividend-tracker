@extends('layout.master')

@section('main')

    @include('partials.header', ['header' => 'Dividend'])

    <div>
        <canvas id="dividendChart"></canvas>
    </div>

    <nav class="nav nav-pills" id="nav-tab" role="tablist">
        @foreach ($years as $year)
            <a class="nav-link {{ $loop->first ? 'active' : '' }} rounded-pill"
               id="nav-tab-{{ $year }}"
               data-bs-toggle="tab"
               data-bs-target="#nav-{{ $year }}"
               type="button"
               role="tab"
               aria-controls="nav-{{ $year }}"
               aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                {{ $year }}
            </a>
        @endforeach
    </nav>

    <hr class="border opacity-80">

    <div class="tab-content" id="nav-tabContent">
        @foreach ($years as $year)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                 id="nav-{{ $year }}"
                 role="tabpanel"
                 aria-labelledby="nav-tab-{{ $year }}"
                 tabindex="0">
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
@endsection
