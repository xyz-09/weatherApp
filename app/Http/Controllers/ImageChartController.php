<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache;
use Log;
use App\Http\Controllers\WeatherController;

class ImageChartController extends Controller
{
    //   "https://image-charts.com/chart?chco=3072F3,ff0000,00aaaa&chd=t:${data}&chdl=Forecast-${city}&chdlp=t&chls=2,4,1&chm=s,000000,0,-1,5|s,000000,1,-1,5&chs=700x200&cht=lc"
       
    private $colors = ['3072F3','ff0000'];
    private $labels = ["Forecast"];
    private $image_size = '700x200';
    private $data;
    private $minutes = 600;

    public function getWeatherImage(Request $request){
        $request->validate([
            'city' => 'required|string',
        ]);

        $city = $request->query('city');
        $cacheKey = $this->cache_key_prefix . '-' . $city;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        } else {
            Cache::remember('forecast', $this->minutes, function () use ($city) {
                $weatherController = new WeatherController();
                $weatherController->get($city);
            });
        }
    }




    public function get_image($city, $data, $labels, $y_labels){

        $colors = implode(',',$this->colors);
       
        $sizes = $this->image_size;
     
        $l = implode('|', $labels);
        $y_l = implode('|',$y_labels);
        $d = implode('||', $data);

        $imageContent = file_get_contents(
            "https://image-charts.com/chart?chco=${colors}&chd=t:${d}&chdl=${l}&chdlp=t&chs=${sizes}&chxl=0:|${y_l}|1:||${y_l}&cht=lc&chxt=x,y&chm=s,E4061C,0,-1,15.0|B,FCECF4,0,0,0"
        );

        return $imageContent;
    }
}
