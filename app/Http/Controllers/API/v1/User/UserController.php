<?php

namespace App\Http\Controllers\API\v1\User;

use App\Models\User;
use Illuminate\Http\Request;
use  App\Http\Controllers\API\v1\BaseApiController as ApiController;

class UserController extends ApiController
{

    /**
     * Gitting user list.
     *
     * @param  integer  $start Specifies user id for starting
     * @return json
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function getList ($start = 100, $amount = 30) {
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
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function show ($id) {
        try {
            $user = User::findOrFail($id);
        } catch (Exception $e) {
            throw new DBException('User db error.');
        }
        return response()->json(self::prepareResponse(true, $user), 200);
    }


    /**
     * Update user info.
     *
     * @param  integer  $id  Select user with passed id
     * @return json
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function update ($id, Request $request) {
        //
    }
}