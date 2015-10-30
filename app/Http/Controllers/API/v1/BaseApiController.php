<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller as Controller;

abstract class BaseApiController extends Controller
{
    /**
     * API respond common method.
     *
     * @param  boolean   Transaction status
     * @return array     Data for client
     */

    public static function prepareResponse ($success, $data) {
        if( $data === false )
            $data = [];
        if ($success === true) {
            return ['success' => $success, 'data' => $data];
        } else {
            return ['success' => $success, 'msg' => $data];
        }
    }
}