<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dividend Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.navbar')

    <div class="container">

        @include('partials.header', ['header' => '€ 13045,10', 'subheader' => 'Portfolio'])

        <div class="row gy-3">
            <div class="col-lg-3 col-md-6">
                <div class="card bg-light border-0 p-4">
                    <div class="mb-0">Redement</div>
                    <div class="fw-bold fs-2 mb-0">€ 2024,39</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-light border-0 p-4">
                    <div class="mb-0">Ontvangen dividend</div>
                    <div class="fw-bold fs-2 mb-0">€ 249,04</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-light border-0 p-4">
                    <div class="mb-0">Besteedbare ruimte</div>
                    <div class="fw-bold fs-2 mb-0">€ 1643,41</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card bg-light border-0 p-4">
                    <div class="mb-0">Transactiekosten</div>
                    <div class="fw-bold fs-2 mb-0">€ 153,50<divp>
                    </div>
                </div>
            </div>
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

        <table class="table">
            <thead>
                <tr>
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
                <tr>
                    <th scope="row">APPLE INC. - COMMON ST</th>
                    {{-- <td class="d-flex justify-content-between">
                        <div>€</div>
                        <div>2533,34</div>
                    </td> --}}
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">2533,34</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">1645,20</div>
                        </div>
                    </td>
                    <td>
                        10
                    </td>
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">114,43</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">12,56</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">€ 838,34</div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex justify-content-between w-100">
                            <span class="mr-auto">€</span>
                            <div class="ml-auto">€ 838,34</div>
                        </div>
                    </td>

                </tr>
            </tbody>
        </table>
</body>

</html>
