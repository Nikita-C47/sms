<?php

use App\Models\Flight;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class FlightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        // TODO: Добавить задание по генерации рейсов на каждый день
        // Заводим генератор
        $faker = Factory::create(config('app.faker_locale'));
        // Массив с рейсами. Сделаем их расписанием
        $flights = [
            ['departure' => '10:00:00', 'arrival' => '12:00:00', 'from' => 'Москва (Домодедово)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '11:00:00', 'arrival' => '12:30:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Москва (Домодедово)'],
            ['departure' => '12:00:00', 'arrival' => '13:00:00', 'from' => 'Москва (Шереметьево)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '13:00:00', 'arrival' => '15:00:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Москва (Шереметьево)'],
            ['departure' => '14:00:00', 'arrival' => '16:45:00', 'from' => 'Москва (Внуково)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '15:00:00', 'arrival' => '17:20:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Москва (Внуково)'],
            ['departure' => '16:00:00', 'arrival' => '17:00:00', 'from' => 'Санкт-Петербург (Пулково)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '17:00:00', 'arrival' => '18:55:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Санкт-Петербург (Пулково)'],
            ['departure' => '18:00:00', 'arrival' => '19:25:00', 'from' => 'Екатеринбург (Кольцово)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '19:00:00', 'arrival' => '20:30:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Екатеринбург (Кольцово)'],
            ['departure' => '20:00:00', 'arrival' => '21:40:00', 'from' => 'Новосибирск (Толмачёво)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '21:00:00', 'arrival' => '22:00:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Новосибирск (Толмачёво)'],
            ['departure' => '22:00:00', 'arrival' => '23:50:00', 'from' => 'Нижний Новгород (Стригино)', 'to' => 'Оренбург (Центральный)'],
            ['departure' => '23:00:00', 'arrival' => '23:55:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Нижний Новгород (Стригино)'],
            ['departure' => '10:00:00', 'arrival' => '12:00:00', 'from' => 'Оренбург (Центральный)', 'to' => 'Москва (Жуковский)']
        ];
        // Массив с бортами
        $boards = [
            ['number' => 'VP-BNU', 'aircraft' => '738'],
            ['number' => 'VP-BVV', 'aircraft' => '772'],
            ['number' => 'VP-BNU', 'aircraft' => '738'],
            ['number' => 'VP-BIZ', 'aircraft' => '734'],
        ];
        // КВС
        $captains = [
            "Иванов Иван Иванович",
            "Петров Петр Петрович",
            "Сидоров Сидр Сидорович"
        ];
        // Конечная дата будет сегодняшний день
        $finishDate = now()->startOfDay();
        // Начальная - за 2 недели до текущей
        $startDate = new Carbon('-2 weeks');
        $startDate->startOfDay();
        // Заводим цикл
        while($startDate->lessThanOrEqualTo($finishDate)) {
            // Номер рейса
            $i = 1;
            // Перебираем рейсы из расписания
            foreach ($flights as $flight) {
                // Выбираем борт
                $board = $faker->randomElement($boards);
                // Выбираем КВС
                $captain = $faker->randomElement($captains);
                // Заводим массив с данными
                $data = [
                    // Дата и время вылета
                    'departure_datetime' => $startDate->toDateString() . " " . $flight['departure'],
                    // Дата и время прилета
                    'arrival_datetime' => $startDate->toDateString() . " " . $flight['arrival'],
                    // Номер рейса (ARLN - вымышленный код АК)
                    'number' => 'ARLN ' . $i,
                    // Бортовой номер ВС
                    'board' => $board['number'],
                    // Код ВС
                    'aircraft_code' => $board['aircraft'],
                    // Аэропорт вылета
                    'departure_airport' => $flight['from'],
                    // Аэропорт прилета
                    'arrival_airport' => $flight['to'],
                    // КВС
                    'captain' => $captain
                ];
                // Генерируем случайную переменную
                if($faker->boolean) {
                    // Собираем массив капитанов в коллекцию
                    $newCaptains = collect($captains);
                    // Ищем капитана
                    $captainIndex = $newCaptains->search($captain);
                    // Собираем новый массив без первого КВС
                    $newCaptains = $newCaptains->except($captainIndex)->toArray();
                    // Добавляем второго КВС
                    $data['extra_captain'] = $faker->randomElement($newCaptains);
                }
                // Создаем рейс
                $newFlight = new Flight($data);
                $newFlight->save();
                // Плюсуем индекс
                $i++;
            }
            // Двигаем дату
            $startDate->addDay();
        }
    }
}
