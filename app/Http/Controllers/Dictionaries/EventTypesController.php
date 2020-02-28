<?php

namespace App\Http\Controllers\Dictionaries;

use App\Http\Controllers\Controller;
use App\Http\Requests\DictionaryFormRequest;
use App\Models\Events\EventType;

class EventTypesController extends Controller
{
    protected $entityData = [
        'entityName' => 'тип событий',
        'entitiesName' => 'типов событий',
        'entityType' => 'event-type',
        'entitiesType' => 'event-types'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = EventType::orderBy('id', 'desc')->paginate(10);

        return view('dictionaries.index', [
            'entities' => $entities,
            'entityType' => $this->entityData['entityType'],
            'entitiesName' => $this->entityData['entitiesName']
        ]);
    }

    /**
     * Show the form for creating a new resource.
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DictionaryFormRequest $request)
    {
        $entity = new EventType([
            'name' => $request->get('name')
        ]);
        $entity->save();

        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Новый тип событий успешно добавлен'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $entity = EventType::findOrFail($id);
        return view('dictionaries.edit', [
            'entity' => $entity,
            'entityName' => $this->entityData['entityName'],
            'entitiesType' => $this->entityData['entitiesType']
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DictionaryFormRequest $request, $id)
    {
        /** @var EventType $entity */
        $entity = EventType::findOrFail($id);
        $entity->name = $request->get('name');
        $entity->save();

        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Тип событий №'.$entity->id.' успешно обновлён'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /** @var EventType $entity */
        $entity = EventType::findOrFail($id);
        $entity->delete();

        return redirect()->route($this->entityData['entitiesType'])->with('alert', [
            'type' => 'success',
            'text' => 'Тип событий "'.$entity->name.'" успешно удалён'
        ]);
    }
}
