<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.navbar')

    <div class="container">
        <header class="my-5">
            <div class="subtitle mb-0">Portfolio</div>
            <h1 class="title fw-bold">13045,10</p>
        </header>

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
                        <div class="bar-legend mr-2 pr-4"></div>
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
</body>

</html>
