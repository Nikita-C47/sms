<?php

namespace App\Http\Controllers\Dictionaries;

use App\Http\Controllers\Controller;
use App\Http\Requests\DictionaryFormRequest;
use App\Models\Department;

/**
 * Класс, предатвляющий контроллер подразделений.
 * @package App\Http\Controllers\Dictionaries Контроллеры для моделей-справочников.
 */
class DepartmentsController extends Controller
{
    /** @var array $entityData русифицированные названия элементов справочников. */
    protected $entityData = [
        'entityName' => 'подразделение',
        'entitiesName' => 'подразделений',
        'entityType' => 'department',
        'entitiesType' => 'departments'
    ];

    /**
     * Отображает список ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Получаем ресурсы, разбитые по 10 штук
        $entities = Department::orderBy('id', 'desc')->paginate(10);
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
        $entity = new Department([
            'name' => $request->get('name')
        ]);
        $entity->save();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route('departments')->with('alert', [
            'type' => 'success',
            'text' => 'Новое подразделение успешно добавлено'
        ]);
    }

    /**
     * Отображает форму редактирования подразделения.
     *
     * @param int $id ID подразделения в БД.
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Ищем ресурс по его ID
        $entity = Department::findOrFail($id);
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
        /** @var Department $entity */
        $entity = Department::findOrFail($id);
        // Обновляем его
        $entity->name = $request->get('name');
        $entity->save();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route('departments')->with('alert', [
            'type' => 'success',
            'text' => 'Подразделение №'.$entity->id.' успешно обновлено'
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
        /** @var Department $entity */
        $entity = Department::findOrFail($id);
        // Удаляем его
        $entity->delete();
        // Возвращаем редирект на список ресурсов с уведомлением
        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Подразделение "'.$entity->name.'" успешно удалено'
        ]);
    }
}
