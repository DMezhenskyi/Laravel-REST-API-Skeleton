<?php

namespace German\Http\Controllers\API\v1\Auth;

use Illuminate\Http\Request;
use German\Models\User;
use Illuminate\Support\Facades\Lang;
use Validator;
use German\Http\Controllers\API\v1\BaseApiController as ApiController;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Contracts\Validation;
use German\Exceptions\ModelsExceptions\DBException;
use \Exception;
use JWTAuth;


class AuthController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['postLogout','postRegister','postLogin']]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function postRegister (Request $request) {

        $data = $request->all();
        $validate = $this->validator($data);

        if ($validate->fails()) {
            return response()->json(self::prepareResponse(false, $validate->messages()), 403);
        }
        try {
            $user = $this->create($data);
        } catch (Exception $e) {
            throw new DBException('Registration db error.');
        }
        return response()->json(self::prepareResponse(true, $user), 200);

    }

    /**
     * User signin attempts.
     *
     * @param  array  $data
     * @return json
     */
    public function postLogin (Request $request) {

        $credentials = $request->only('email', 'password');
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($this->hasTooManyLoginAttempts($request) === true)
            return response()->json(self::prepareResponse(false, $this->getLockoutErrorMessage($this->lockoutTime())), 401);

        try {

            if ( ! $token = JWTAuth::attempt($credentials)) {

                $this->incrementLoginAttempts($request);
                $msg[] = Lang::has('auth.failed') ? Lang::get('auth.failed') : 'These credentials do not match our records.';
                return response()->json(self::prepareResponse(false, $msg), 401);
            }

            $this->handleUserWasAuthenticated($request, $throttles);
            $user = User::where('email','=', $request['email'])->firstOrFail();

        } catch (JWTException $e) {
            $msg[] = Lang::has('auth.failed_token') ? Lang::get('auth.failed_token') : 'Authenticate attempt failed.';
            return response()->json(self::prepareResponse(false, $msg), 401);
        }

        return response()->json(self::prepareResponse(true, ['user' => $user, 'token' => $token]), 200);
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
            'password' => 'required|confirmed|min:6',
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
}