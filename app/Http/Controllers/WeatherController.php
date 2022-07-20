<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use DateTime;
use App\Http\Controllers\HttpClientController;


class WeatherController extends Controller {

    private $geo_url = 'http://api.openweathermap.org/geo/1.0/direct';
    private $forecats_url = 'https://api.openweathermap.org/data/2.5/onecall';

    public function getDataByCityName(String $city) {

        //https://api.nbp.pl/api/exchangerates/rates/a/eur/2012-01-01/2012-01-31/?format=json 

        $app_id = config('weatherapi.WEATHER_API_CODE');
       
        $url = $this->geo_url . "?q=${city},pl&limit=1&appid=${app_id}";

        Log::info('Not from cache');
        Log::info($url);

        $http = new HttpClientController();

        $cityObj = $http->get_json_data($url);

        // only one city
        if (!empty($cityObj[0])) {
            $lat = $cityObj[0]->lat;
            $lon = $cityObj[0]->lon;
       
            $forecastObj = $http->get_json_data(
                $this->forecats_url .
                    "?lat=${lat}&lon=${lon}&appid=${app_id}&units=metric&exclude=minutely,hourly,alerts"
            );

            $daily = [];
           
            foreach ($forecastObj->daily as $o) {
                $w = new DateTime("@$o->dt");
                dump($w->getTimestamp(),$w->format('Y-m-d'));
                array_push($daily, $o->temp->day);
            }              

            return $daily;
        }
    }
}
