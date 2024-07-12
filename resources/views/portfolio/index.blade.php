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
                    <tr>
                        <td class="text-nowrap">
                            APPLE INC. - COMMON ST
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
                                <div class="ml-auto">1645,20</div>
                            </div>
                        </td>
                        <td class="text-nowrap text-end">
                            10
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">114,43</div>
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
                                <span class="mr-auto"><i
                                        class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                                <div class="ml-auto">€ 838,34</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto"><i
                                        class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                                <div class="ml-auto">€ 838,34</div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-nowrap">
                            COCA COLA COMPANY
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">1950,90</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">1790,98</div>
                            </div>
                        </td>
                        <td class="text-nowrap text-end">
                            34
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">56,22</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto">€</span>
                                <div class="ml-auto">80,47</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto"><i
                                        class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                                <div class="ml-auto">€ 160,12</div>
                            </div>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex justify-content-between w-100 text-nowrap">
                                <span class="mr-auto"><i
                                        class="bi bi-arrow-up-right-circle-fill text-success"></i></span>
                                <div class="ml-auto">€ 235,53</div>
                            </div>
                        </td>
                    </tr>

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
    </div>
</body>

</html>
