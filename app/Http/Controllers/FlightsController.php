<?php

namespace App\Http\Controllers;

use App\Models\Flight;

class FlightsController extends Controller
{
    public function index()
    {
        $flights = Flight::orderBy('id', 'desc')->paginate(10);
        return view('flights.index', [
            'flights' => $flights
        ]);
    }
}
