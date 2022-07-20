<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HttpClientController extends Controller
{
    //
    public function get_json_data($url)
    {
        $response = Http::acceptJson()->get($url);
        return json_decode($response->getBody());
    }
}
