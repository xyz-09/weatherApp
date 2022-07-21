<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use App\Http\Controllers\CurrencyController;

class ImageChartController extends Controller {

    private $colors = ['3072F3'];
    private $image_size = ["width" => 700, "height" => 300];

    private $minutes = 1000;
    private $cache_key_prefix = 'currency';

    /**
     * Get multiple currency graph from image-chart.com
     * 
     * @param Request $request
     * 
     * @return mixed void/image
     */

    public function getMultipleCurrenciesImage(Request $request) {

        $request->validate([
            'currencies' => 'required|string',
        ]);

        $currencies = explode(',', $request->query('currencies'));
        if (empty($currencies)) return;


        $cachedData = [];
        foreach ($currencies as $currency) {
            //set random color
            $this->colors[] = str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
            array_push($cachedData, $this->checkOrSetCache($this->cache_key_prefix . $currency, $currency));
        }

        $dataKeys = collect($cachedData)->map(function ($item) {
            return  array_keys($item);
        })->flatten()->unique()->all();

        $dataValues = collect($cachedData)->map(function ($item) {
            return  implode(',', array_values($item));
        })->flatten()->all();

        return  response(
            $this->getImage(
                [implode('|', $dataValues)],
                $currencies,
                $dataKeys,
                $this->getY_Axis($cachedData)
            )
        )->header('Content-type', 'image/png')->header('charset', 'utf-8');
    }

    /**
     * CheckOrSetCache 
     *
     * @param String $cacheKey
     * @param String $currency
     * @return Array
     */
    public function checkOrSetCache(String $cacheKey, String $currency) {

        return Cache::remember($cacheKey, $this->minutes, function () use ($currency) {

            $currencyController = new CurrencyController();
            return  $currencyController->getDataByCurrencyName($currency);
        });
    }

    /**
     * getCurrencyImage function
     *
     * @param String $currency
     * @return void
     */
    public function getCurrencyImage(String $currency) {

        $cachedData =  $this->checkOrSetCache($this->cache_key_prefix . $currency, $currency);

        $dataKeys = array_keys($cachedData);
        $dataValues = array_values($cachedData);

        return  response($this->getImage(
            [implode(',', $dataValues)],
            [$currency],
            $dataKeys,
            $this->getY_Axis([$dataValues])
        ))->header('Content-type', 'image/png')->header('charset', 'utf-8');
    }

    /**
     * getY_Axis function
     *
     * @param Array $data
     * @return String
     */
    private function getY_Axis(array $data) {

        if (empty($data)) return [];      

        $numbers = [];
        $divisables = [];

        foreach ($data as $key => $d) {
            $dd = array_values($d);
            $divisible = abs($dd[1]- $dd[0]);
            $base = ceil(min($dd) / $divisible) * $divisible - $divisible;

            for ($i = $base; $i <= max($dd)+$divisible; $i += $divisible) {
                $numbers[] = $i;
            }
           
        }
        $min = min($numbers);
        $max = max($numbers);
        
        return "1,${min},${max}";
    }

    /**
     * getImage function
     *
     * @param Array $data - data for graph
     * @param Array $labels - labels for data
     * @param Array $x_labels - labels for x axis
     * @param Array $y_labels - labels for y axis
     * @return Image
     */
    public function getImage($data, $labels,  $x_labels, $y_labels) {

        $colors = implode(',', $this->colors);



        $this->image_size["height"] =  min([$this->image_size["height"] * count($this->colors), 999]);
        $sizes = implode("x", $this->image_size);

        $l = implode('| ', $labels);
        $x_l = implode('|', $x_labels);
        $d = implode('||', $data);

        $imageContent = file_get_contents(
            "http://image-charts.com/chart?chco=${colors}&chd=t:${d}&chdl=${l}&chdlp=t&chs=${sizes}&cht=ls&chxr=${y_labels}&chxt=x,y&chxl=0:|${x_l}"
        );
        //&chxl=0:|${x_l}|1:||${y_l}

        return $imageContent;
    }
}
