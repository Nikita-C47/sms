<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryForDepartmentFormRequest;
use App\Http\Requests\EventCategoryFormRequest;
use App\Models\Department;
use App\Models\Events\EventCategory;

class EventCategoriesController extends Controller
{
    public function index()
    {
        $entities = EventCategory::with('department')->orderBy('id', 'desc')->paginate(10);
        return view('event-categories.index', [
            'entities' => $entities
        ]);
    }

    public function create()
    {
        $departments = Department::all();
        return view('event-categories.create', [
            'departments' => $departments
        ]);
    }

    public function store(EventCategoryFormRequest $request)
    {
        $entity = new EventCategory([
            'department_id' => $request->get('department_id'),
            'code' => $request->get('code'),
            'name' => $request->get('name')
        ]);
        $entity->save();

        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Новая категория событий успешно добавлена'
        ]);
    }

    public function edit($id)
    {
        $entity = EventCategory::findOrFail($id);
        $departments = Department::all();

        return view('event-categories.edit', [
            'entity' => $entity,
            'departments' => $departments
        ]);
    }

    public function update(EventCategoryFormRequest $request, $id)
    {
        /** @var EventCategory $entity */
        $entity = EventCategory::findOrFail($id);

        $entity->code = $request->get('code');
        $entity->department_id = $request->get('department_id');
        $entity->name = $request->get('name');
        $entity->save();

        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Категория событий №'.$entity->id.' успешно обновлена'
        ]);
    }

    public function destroy($id)
    {
        /** @var EventCategory $entity */
        $entity = EventCategory::findOrFail($id);
        $entity->delete();

        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Категория событий "'.$entity->name.'" успешно удалена'
        ]);
    }
}
