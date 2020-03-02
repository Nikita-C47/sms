<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Department;
use App\Notifications\UserCredentials;
use App\User;
use App\Components\Helpers\PasswordHelper;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    use PasswordHelper;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::real()->with('department')->orderBy('id', 'desc')->paginate(10);
        return view('users.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all();

        return view('users.create', [
            'departments' => $departments,
            'roles' => User::USER_ROLES
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserFormRequest $request)
    {
        $password = $this->generatePassword();

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'access_level' => $request->get('access_level'),
            'department_id' => $request->get('department_id'),
            'password' => Hash::make($password)
        ]);

        $user->save();
        $user->notify(new UserCredentials($password));

        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => 'Новый пользователь успешно добавлен. Пароль выслан на указанный email'
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
        $user = User::real()->with('department')->findOrFail($id);
        $departments = Department::all();

        return view('users.edit', [
            'user' => $user,
            'departments' => $departments,
            'roles' => User::USER_ROLES
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserFormRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserFormRequest $request, $id)
    {
        /** @var User $user */
        $user = User::real()->findOrFail($id);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->access_level = $request->get('access_level');
        $user->department_id = $request->get('department_id');
        $user->save();

        $text = "Пользователь успешно обновлен";

        if($request->has('reset_password')) {
            $password = $this->generatePassword();
            $user->password = Hash::make($password);
            $user->save();
            $user->notify(new UserCredentials($password, true));
            $text .= ". Новый пароль выслан на указанный email";
        }

        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => $text
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
        /** @var User $user */
        $user = User::real()->findOrFail($id);
        $user->delete();

        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => 'Пользователь "'.$user->name.'" успешно удалён!'
        ]);
    }

    public function auth($id)
    {
        /** @var Authenticatable $user */
        $user = User::real()->findOrFail($id);

        Auth::login($user);

        return redirect()->route('home');
    }
}
