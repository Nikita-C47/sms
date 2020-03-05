<?php

namespace App\Http\Controllers\Dictionaries;

use App\Http\Controllers\Controller;
use App\Http\Requests\DictionaryFormRequest;
use App\Models\Events\EventType;

/**
 * Класс, предатвляющий контроллер типов событий.
 * @package App\Http\Controllers\Dictionaries Контроллеры для моделей-справочников.
 */
class EventTypesController extends Controller
{
    /** @var array $entityData русифицированные названия элементов справочников. */
    protected $entityData = [
        'entityName' => 'тип событий',
        'entitiesName' => 'типов событий',
        'entityType' => 'event-type',
        'entitiesType' => 'event-types'
    ];

    /**
     * Отображает список ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Получаем ресурсы, разбитые по 10 штук
        $entities = EventType::orderBy('id', 'desc')->paginate(10);
        // Возвращаем представление
        return view('dictionaries.index', [
            'entities' => $entities,
            'entityType' => $this->entityData['entityType'],
            'entitiesName' => $this->entityData['entitiesName']
        ]);
    }

    /**
     * Отображает форму добавления ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dictionaries.create', [
            'entityName' => $this->entityData['entityName'],
            'entitiesType' => $this->entityData['entitiesType']
        ]);
    }

    /**
     * Сохраняет ресурс в БД.
     *
     * @param DictionaryFormRequest $request запрос на добавление ресурса.
     * @return \Illuminate\Http\Response
     */
    public function store(DictionaryFormRequest $request)
    {
        // Создаем ресурс
        $entity = new EventType([
            'name' => $request->get('name')
        ]);
        $entity->save();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Новый тип событий успешно добавлен'
        ]);
    }

    /**
     * Отображает форму редактирования ресурса.
     *
     * @param int $id ID мероприятия в БД.
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Ищем мероприятие по его ID
        $entity = EventType::findOrFail($id);
        // Возвращаем форму редактирования
        return view('dictionaries.edit', [
            'entity' => $entity,
            'entityName' => $this->entityData['entityName'],
            'entitiesType' => $this->entityData['entitiesType']
        ]);
    }

    /**
     * Обновляет указанный ресурс в БД.
     *
     * @param DictionaryFormRequest $request запрос на обновление ресурса.
     * @param int $id ID ресурса.
     * @return \Illuminate\Http\Response
     */
    public function update(DictionaryFormRequest $request, $id)
    {
        // Ищем ресурс по ID.
        /** @var EventType $entity */
        $entity = EventType::findOrFail($id);
        // Обновляем его
        $entity->name = $request->get('name');
        $entity->save();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Тип событий №'.$entity->id.' успешно обновлён'
        ]);
    }

    /**
     * Удаляет указанный ресурс из БД.
     *
     * @param int $id ID ресурса.
     * @return \Illuminate\Http\Response
     * @throws \Exception исключение в случае неудачного удаления.
     */
    public function destroy($id)
    {
        // Ищем ресурс
        /** @var EventType $entity */
        $entity = EventType::findOrFail($id);
        // Удаляем его
        $entity->delete();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Тип событий "'.$entity->name.'" успешно удалён'
        ]);
    }
}
