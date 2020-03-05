<?php

namespace App\Http\Controllers;

use App\Models\Flight;

/**
 * Класс, представляющий контроллер рейсов.
 * @package App\Http\Controllers Контроллеры приложения.
 */
class FlightsController extends Controller
{
    /**
     * Отображает список рейсов в приложении.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Отображаем список рейсов приложения по 10 штук
        $flights = Flight::orderBy('id', 'desc')->paginate(10);
        // Возвращаем представление
        return view('flights.index', [
            'flights' => $flights
        ]);
    }
}
