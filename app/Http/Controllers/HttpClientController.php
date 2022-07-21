<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HttpClientController extends Controller
{
    /**
     * get_json_data function by url
     *
     * @param String $url
     * @return Array
     */
    public function get_json_data(String $url)
    {
        $response = Http::withoutVerifying()->acceptJson()->get($url);
        return json_decode($response->getBody());
    }
}
