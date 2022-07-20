<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DateTime;
use App\Http\Controllers\HttpClientController;


class CurrencyController extends Controller {

    private $api_url = 'https://api.nbp.pl/api/exchangerates/rates/a/%s/%s/%s/?format=json';
    private $days = 8;

    public function getDataByCurrencyName(String $currency = "EUR") {

        $dt = new DateTime();
        $now = $dt->format('Y-m-d');

        $url = sprintf($this->api_url, $currency, $dt->modify('-' . $this->days . ' day')->format('Y-m-d'), $now);

        Log::info('Not from cache');
        Log::info($url);

        $http = new HttpClientController();

        $currencyObject = $http->get_json_data($url);

        $currencyData = [];

        if(empty($currencyObject) || !property_exists($currencyObject,'rates'))
        return  $currencyData;
       
        foreach ($currencyObject->rates as $o) {
            $currencyData[$o->effectiveDate] =  $o->mid;
        }

        return $currencyData;
    }
}
