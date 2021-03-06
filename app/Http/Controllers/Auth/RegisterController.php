<?php

namespace App\Http\Controllers\Auth;


use Auth;
use App\Team;
use App\User;
use Illuminate\Auth\GuardHelpers;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        if($this->isTeamLimitReached() && !$request->session()->has('invite_token')) {
            return view('auth.full');
        }

        return view('auth.register');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        if($this->isTeamLimitReached() && !$request->session()->has('invite_token')) {
            return view('auth.full');
        }

        $user = $this->create($request->all());

        // If user was not invited, create and join a new team
        if (!$request->session()->has('invite_token')) {
            // Create event (team)
            $team = new Team();
            $team->owner_id = $user->id;
            $team->name = $user->name;
            $team->save();

            // Add user to team
            $user->teams()->attach($team->id);
            $user->switchTeam($team->id);
        }

        // Log the user in

        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }

    private function isTeamLimitReached() {
        $teamLimit = env('TEAM_LIMIT') ? env('TEAM_LIMIT') : 1;

        return (Team::count() >= $teamLimit);
    }

}
