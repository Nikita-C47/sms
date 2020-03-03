<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Department;
use App\Notifications\UserCredentialsNotification;
use App\User;
use App\Components\Helpers\PasswordHelper;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        $user->notify(new UserCredentialsNotification($password));

        /** @var \App\User $authUser */
        $authUser = Auth::user();
        Log::channel('user_actions')
            ->info("User ".$user->name." was created by user ".$authUser->name);

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

        /** @var \App\User $authUser */
        $authUser = Auth::user();

        if($request->has('reset_password')) {
            $password = $this->generatePassword();
            $user->password = Hash::make($password);
            $user->save();
            $user->notify(new UserCredentialsNotification($password, true));
            $text .= ". Новый пароль выслан на указанный email";

            Log::channel('user_actions')
                ->info("User ".$authUser->name." resets user ".$user->name." password");
        }

        Log::channel('user_actions')->info("User ".$user->name." was updated by user ".$authUser->name);

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

        /** @var \App\User $authUser */
        $authUser = Auth::user();
        Log::channel('user_actions')
            ->info("User ".$user->name." was removed by user ".$authUser->name);


        return redirect()->route('users')->with('alert', [
            'type' => 'success',
            'text' => 'Пользователь "'.$user->name.'" успешно удалён!'
        ]);
    }

    public function auth($id)
    {
        /** @var Authenticatable|User $user */
        $user = User::real()->findOrFail($id);

        Auth::login($user);

        /** @var \App\User $authUser */
        $authUser = Auth::user();
        Log::channel('user_actions')
            ->info("User ".$authUser->name." was authorized as user ".$user->name);

        return redirect()->route('home');
    }
}
