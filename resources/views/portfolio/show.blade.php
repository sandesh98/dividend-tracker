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

        @include('partials.header', ['header' => 'APPLE INC. - COMMON ST', 'subheader' => 'US0378331005'])

        <div class="row">
            <div class="col-8">

                {{-- <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">

                        <button class="nav-link active" id="transactions" data-bs-toggle="tab"
                            data-bs-target="#nav-transactions" type="button" role="tab"
                            aria-controls="transactions" aria-selected="true">Transacties</button>

                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile"
                            aria-selected="false">Profile</button>
                    </div>
                </nav> --}}

                <nav class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-link active rounded-pill" id="transactions" data-bs-toggle="tab"
                        data-bs-target="#nav-transactions" type="button" role="tab" aria-controls="transactions"
                        aria-selected="true">Trans</a>
                    <a class="nav-link rounded-pill" id="nav-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="false">Div</a>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-transactions" role="tabpanel"
                        aria-labelledby="transactions" tabindex="0">
                        Bubba.
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                        tabindex="0">
                        De PP
                    </div>
                </div>

            </div>
        </div>
</body>

</html>
