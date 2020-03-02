<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FlightRequest;
use App\Http\Requests\Api\FlightUpdateRequest;
use App\Http\Resources\FlightResource;
use App\Models\Flight;

class FlightsController extends Controller
{
    public function index()
    {
        return FlightResource::collection(Flight::paginate());
    }

    public function create(FlightRequest $request)
    {
        $entity = new Flight([
            'departure_datetime' => $request->get('departure_datetime'),
            'arrival_datetime' => $request->get('arrival_datetime'),
            'number' => $request->get('number'),
            'board' => $request->get('board'),
            'aircraft_code' => $request->has('aircraft_code') ? $request->get('aircraft_code') : null,
            'departure_airport' => $request->get('departure_airport'),
            'arrival_airport' => $request->get('arrival_airport'),
            'captain' => $request->get('captain'),
            'extra_captain' => $request->has('extra_captain') ? $request->get('extra_captain') : null,
        ]);
        $entity->save();

        return new FlightResource($entity);
    }

    public function load(FlightRequest $request)
    {
        $flights = $request->get('flights');

        foreach ($flights as $flight) {
            $entity = new Flight([
                'departure_datetime' => $flight['departure_datetime'],
                'arrival_datetime' => $flight['arrival_datetime'],
                'number' => $flight['number'],
                'board' => $flight['board'],
                'aircraft_code' => array_key_exists('aircraft_code', $flight) ? $flight['aircraft_code'] : null,
                'departure_airport' => $flight['departure_airport'],
                'arrival_airport' => $flight['arrival_airport'],
                'captain' => $flight['captain'],
                'extra_captain' => array_key_exists('extra_captain', $flight) ? $flight['extra_captain'] : null,
            ]);
            $entity->save();
        }

        return [
            'data' => ['created_count' => count($flights)]
        ];
    }

    public function view($id)
    {
        return new FlightResource(Flight::findOrFail($id));
    }

    public function edit(FlightUpdateRequest $request, $id)
    {
        /** @var Flight $entity */
        $entity = Flight::findOrFail($id);
        $entity->fill($request->all());
        $entity->save();

        return new FlightResource($entity);
    }

    public function delete($id)
    {
        /** @var Flight $entity */
        $entity = Flight::findOrFail($id);
        $entity->delete();

        return [
            'data' => "Flight #$id successfully removed."
        ];
    }
}
