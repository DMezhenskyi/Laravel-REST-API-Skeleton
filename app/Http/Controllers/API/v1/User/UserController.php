<?php

namespace App\Http\Controllers\API\v1\User;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use  App\Http\Controllers\API\v1\BaseApiController as ApiController;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Exceptions\ModelsExceptions\DBException;
use Illuminate\Support\Facades\Lang;

class UserController extends ApiController
{
    /**
     * Create a new user controller instance.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => 'getList']);
    }

    /**
     * Getting current authenticated user.
     *
     * @return json
     *
     * @throws \Tymon\JWTAuth\Exceptions\TokenInvalidException | \Tymon\JWTAuth\Exceptions\TokenExpiredException
     * \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function index () {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate())
                return response()->json(self::prepareResponse(true, $user), 404);

        } catch (TokenExpiredException $e) {
            throw new $e(
                Lang::has('auth.expired_token') ?
                    Lang::get('auth.expired_token') :
                    'Token is expired. Can\'t get user.'
            );
        } catch (TokenInvalidException $e) {
            throw new $e(
                Lang::has('auth.invalid_token') ?
                    Lang::get('auth.invalid_token') :
                    'Token is invalid. Can\'t get user.'
                );
        } catch (JWTException $e) {
            throw new $e(
                Lang::has('auth.not_found_token') ?
                    Lang::get('auth.not_found_token') :
                    'Token not found. Can\'t get user.'
            );
        }

        return response()->json(self::prepareResponse(true, $user), 200);
    }
    /**
     * Getting user list.
     *
     * @param  integer  $start Specifies user id for starting
     * @return string
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getList ($start = 0, $amount = 30) {
        try {
        $user = User::where( 'id', '>', $start )->take( $amount )->get();
        } catch (Exception $e) {
            throw new DBException('Users db error.');
        }
        return response()->json(self::prepareResponse(true, $user), 200);
    }

    /**
     * Gitting user.
     *
     * @param  integer  $id  Select user with passed id
     * @return json
     *
     * @throws \App\Exceptions\ModelsExceptions\DBException
     */
    public function show ($id) {
        try {
            $user = User::findOrFail($id);
        } catch (Exception $e) {
            throw new $e();
        }
        return response()->json(self::prepareResponse(true, $user), 200);
    }

    /**
     * Update user info.
     *
     * @param  integer  $id  Select user with passed id
     * @param  \Illuminate\Http\Request $request
     * @return json
     *
     * @throws \App\Exceptions\ModelsExceptions\DBException
     */
    public function update (Request $request) {

        $id = JWTAuth::parseToken()->getPayload()->get('sub');
        $data = $request->only('name','email');
        $validate = $this->validator($data, $id);

        if ($validate->fails()) {
            return response()->json(self::prepareResponse(false, $validate->messages()), 403);
        }
        try {
            $user = User::where('id','=',$id)->update($data);
        } catch (Exception $e) {
            throw new DBException('User update db error.');
        }
        return response()->json(self::prepareResponse(true, $user), 200);
    }

    /**
     * Get a validator for an incoming updating user request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator (array $data, $id) {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => '|required|email|max:255|unique:users,email,'.$id.'',
        ]);
    }
}