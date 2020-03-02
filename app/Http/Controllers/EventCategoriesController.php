<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryForDepartmentFormRequest;
use App\Http\Requests\EventCategoryFormRequest;
use App\Models\Department;
use App\Models\Events\EventCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class EventCategoriesController extends Controller
{
    public function index()
    {
        // Начинаем конструировать запрос
        $entities = EventCategory::with('department');
        // Если это не администратор, значит список категорий запросил менеджер
        if(!Gate::allows('admin')) {
            /** @var \App\User $user */
            $user = Auth::user();
            // Добавляем фильтр по отделу
            $entities->where('department_id', $user->department_id);
        }
        // Добавляем сортировку и пагинатор
        $entities = $entities->orderBy('id', 'desc')->paginate(10);
        return view('event-categories.index', [
            'entities' => $entities
        ]);
    }

    public function create()
    {
        $departments = Gate::allows('admin') ? Department::all() : null;

        return view('event-categories.create', [
            'departments' => $departments
        ]);
    }

    public function store(EventCategoryFormRequest $request)
    {
        /** @var \App\User $user */
        $user = Auth::user();

        $entity = new EventCategory([
            'department_id' => Gate::allows('admin') ? $request->get('department_id') : $user->department_id,
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
        $departments = Gate::allows('admin') ? Department::all() : null;

        return view('event-categories.edit', [
            'entity' => $entity,
            'departments' => $departments
        ]);
    }

    public function update(EventCategoryFormRequest $request, $id)
    {
        /** @var EventCategory $entity */
        $entity = EventCategory::findOrFail($id);

        Gate::authorize('event-category', $entity);

        /** @var \App\User $user */
        $user = Auth::user();

        $entity->fill([
            'department_id' => Gate::allows('admin') ? $request->get('department_id') : $user->department_id,
            'code' => $request->get('code'),
            'name' => $request->get('name')
        ]);
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

        Gate::authorize('event-category', $entity);

        $entity->delete();

        return redirect()->route('event-categories')->with('alert', [
            'type' => 'success',
            'text' => 'Категория событий "'.$entity->name.'" успешно удалена'
        ]);
    }
}
