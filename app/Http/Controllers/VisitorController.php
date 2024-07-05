<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisitorController extends Controller
{
    public function visitor(Request $request)
    {
        $visitorName = $request->input('visitor_name', 'Anonymous');

        $Ip = $request->ip();
    
        $ipResponse = Http::get("http://ip-api.com/json");
        
        if ($ipResponse->successful()) {
            $locData = $ipResponse->json();
            $loc = $locData['city'] . ', ' . $locData['country'];
        } else {
            $loc = 'Unknown';
            $ipErrorMessage = 'Error fetching location data: ' . $ipResponse->status() . ' - ' . $ipResponse->body();
            Log::error($ipErrorMessage);

        
        }

        $weathResponse = Http::get("http://api.weatherapi.com/v1/current.json", [
            'key' => env('WEATHER_API_KEY'),
            'q' => $loc,
            'aqi' => 'no',
        ]);

        if ($weathResponse->successful()) {
            $weathData = $weathResponse->json();
            $temp = $weathData['current']['temp_c'];
        } else {
            $temp = 'N/A';
            $weathErrorMessage = 'Error fetching weather data: ' . $weathResponse->status() . ' - ' . $weathResponse->body();
            Log::error($weathErrorMessage);
        }

        $response = [
            'client_ip' => $Ip,
            'location' => $loc,
            'greeting' => "Hello, $visitorName!, the temperature is $temp degrees Celsius in $loc",
        ];

       // dd($response);
        return response()->json($response);
    }
}
