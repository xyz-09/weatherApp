<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use Log;
use DateTime;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;

class ImageChartController extends Controller {
    //   "https://image-charts.com/chart?chco=3072F3,ff0000,00aaaa&chd=t:${data}&chdl=Forecast-${city}&chdlp=t&chls=2,4,1&chm=s,000000,0,-1,5|s,000000,1,-1,5&chs=700x200&cht=lc&chan=1200,easeOutBack"

    private $colors = ['3072F3'];
    private $image_size = '700x200';

    private $minutes = 10000;
    private $cache_key_prefix = 'currency';

    public function getMultipleCurrencies(Request $request) {

        $request->validate([
            'currencies' => 'required|string',
        ]);

        $currencies = explode(',', $request->query('currencies'));
        if (empty($currencies)) return;


        $cachedData = [];
        foreach ($currencies as $currency) {
            $this->colors[]= str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
            array_push($cachedData, $this->chcekOrSetCache($this->cache_key_prefix . $currency, $currency));
        }

        $dataKeys = collect($cachedData)->map($closure = function ($item) use (&$closure) {
            return  array_keys($item);
        })->flatten()->unique()->all();

        $dataValues = collect($cachedData)->map($closure = function ($item) use (&$closure) {
            return  implode(',', array_values($item));
        })->flatten()->all();

        return  response($this->get_image(
            [implode('|', $dataValues)],
            $currencies,
            $dataKeys,
            []
        ))->header('Content-type', 'image/png')->header('charset', 'utf-8');
    }

    public function chcekOrSetCache($cacheKey, $currency) {

        return Cache::remember($cacheKey, $this->minutes, function () use ($currency) {

            $currencyController = new CurrencyController();
            return  $currencyController->getDataByCurrencyName($currency);
        });
    }

    public function getCurrencyImage($currency) {

        $cachedData =  $this->chcekOrSetCache($this->cache_key_prefix . $currency, $currency);

        $dataKeys = array_keys($cachedData);
        $dataValues = array_values($cachedData);

        return  response($this->get_image(
            [implode(',', $dataValues)],
            [$currency],
            $dataKeys,
            $this->getY_Axis($dataValues)
        ))->header('Content-type', 'image/png')->header('charset', 'utf-8');
    }

    private function getY_Axis($data) {
       
        if (empty($data)) return [];

        $divisible = 0.01;
        $numbers = [];
        $base = ceil(min($data) / $divisible) * $divisible;

        for ($i = $base; $i <= max($data); $i += $divisible) {
            $numbers[] = $i;
        }

        return $numbers;
    }

    public function get_image($data, $labels,  $x_labels, $y_labels) {

        $colors = implode(',', $this->colors);
        $sizes = $this->image_size;

        $l = implode('| ', $labels);
        $y_l = implode('||', $y_labels);
        $x_l = implode('|', $x_labels);
        $d = implode('||', $data);

        $imageContent = file_get_contents(
            "http://image-charts.com/chart?chco=${colors}&chd=t:${d}&chdl=${l}&chdlp=t&chs=${sizes}&chxl=0:|${x_l}|1:||${y_l}&cht=lc&chxt=x,y"
        );

        return $imageContent;
    }
}
