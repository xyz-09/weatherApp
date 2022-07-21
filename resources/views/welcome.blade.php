<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
            height: auto;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
    <script>
        const
        singleMainEndpoint = '{{route('currency','')}}',
        multipleMainEndpoint = '{{route('currencies',[" currencies"=>''])}}',
        currencyListMainEndpoint = "https://api.nbp.pl/api/exchangerates/tables/a/?format=json"  ;

    </script>

</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md">
                Currencies graphs
            </div>
            <div id="app">
                <div>
                    <select v-model="singleSelected" @change="getCurrencyByCode('singleImage')">
                        <option v-for="currency in currencyList" :value="currency.code">
                            ${currency.currency}
                          
                        </option>
                    </select>
                    <p v-if="loadingSingle">Loading ...</p>
                    <p> Graf dla jednej waluty - 7 ostatnich dni</p>
                    <img id="singleImage" src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif?20151024034921" />

                </div>
                <div>
                    <select v-model="multiSelected" multiple @change="getMultiCurrencyImage('multiImage')">
                        <option v-for="currency in currencyList" :value="currency.code">
                            ${currency.currency}
                        </option>
                    </select>
                    <p>Wybrane: ${ multiSelected.join(`,`) }</p>
                    <p v-if="loadingMultiple">Loading ...</p>
                    <p>Graf dla kilku walut - 7 ostatnich dni</p>
                    <img id="multiImage" src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif?20151024034921"/>

                </div>
            </div>
        </div>
        <script src="https://unpkg.com/vue@3.2.33/dist/vue.global.prod.js"></script>
        <script src="./js/app.js"></script>
</body>

</html>