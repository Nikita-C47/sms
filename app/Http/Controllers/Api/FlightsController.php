<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FlightRequest;
use App\Http\Requests\Api\FlightUpdateRequest;
use App\Http\Resources\FlightResource;
use App\Models\Flight;
use Illuminate\Support\Facades\Log;

/**
 * Класс, представляющий контроллер api для работы с рейсами.
 * @package App\Http\Controllers\Api Контроллеры для api приложения.
 */
class FlightsController extends Controller
{
    /**
     * Возвращает текущий список рейсов в приложении с постраничной разбивкой.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Возвращаем стандартный список рейсов с пагинацией (по 15 на странице)
        return FlightResource::collection(Flight::paginate());
    }

    /**
     * Добавляет рейс в БД.
     *
     * @param FlightRequest $request запрос на добавление рейса через api.
     * @return FlightResource созданный рейс.
     */
    public function create(FlightRequest $request)
    {
        // Заводим новый рейс
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
        // Пишем сообщение в лог о том, что был создан новый рейс через api
        Log::channel('user_actions')->info("Flight #".$entity->id." was imported by api");
        // Возвращаем созданный рейс в ответ
        return new FlightResource($entity);
    }

    /**
     * Загружает несколько рейсов в БД.
     *
     * @param FlightRequest $request запрос на добавление рейсов.
     * @return array данные о количестве созданных рейсов.
     */
    public function load(FlightRequest $request)
    {
        // Получаем рейсы из запроса
        $flights = $request->get('flights');
        // Перебираем их
        foreach ($flights as $flight) {
            // Создаем новый рейс
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
        // Пишем в лог сообщение с количеством добавленных рейсов
        Log::channel('user_actions')->info(count($flights)." flights was imported by api");
        // Возвращаем эту же информацию в ответ
        return [
            'data' => ['created_count' => count($flights)]
        ];
    }

    /**
     * Возвращает рейс по его ID.
     *
     * @param int $id ID рейса.
     * @return FlightResource информация о рейсе.
     */
    public function view($id)
    {
        return new FlightResource(Flight::findOrFail($id));
    }

    /**
     * Редактирует рейс с указанным ID.
     *
     * @param FlightUpdateRequest $request запрос на обновление рейса.
     * @param int $id ID рейса.
     * @return FlightResource информация об обновленном рейсе.
     */
    public function edit(FlightUpdateRequest $request, $id)
    {
        // Ищем рейс по переданному id
        /** @var Flight $entity */
        $entity = Flight::findOrFail($id);
        // Заполняем рейс новыми данными из запроса (чтобы можно было обновлять рейс частично)
        $entity->fill($request->all());
        $entity->save();
        // Пишем в лог информацию о том, что рейс был обновлен через api
        Log::channel('user_actions')->info("Flight #".$entity->id." was updated by api");
        // Возвращаем рейс
        return new FlightResource($entity);
    }

    /**
     * Удаляет рейс из БД.
     *
     * @param int $id ID рейса.
     * @return array информация об удалении рейса.
     * @throws \Exception искобчение в случае неудачного удаления рейса.
     */
    public function delete($id)
    {
        // Ищем рейс по ID
        /** @var Flight $entity */
        $entity = Flight::findOrFail($id);
        // Удаляем его
        $entity->delete();
        // Пишем в лог информацию о том, что рейс был удален
        Log::channel('user_actions')->info("Flight #$id was removed by api");
        // Возвращаем информацию об успешном удалении
        return [
            'data' => "Flight #$id successfully removed."
        ];
    }
}
