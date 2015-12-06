<?php

namespace German\Http\Controllers\API\v1;

use German\Http\Controllers\Controller as Controller;

abstract class BaseApiController extends Controller
{
    /**
     * API respond common method.
     *
     * @param  boolean   Transaction status
     * @return array     Data for client
     */

    public static function prepareResponse ($success, $data) {
        if( $data === false || $data === null )
            $data = [];
        if ($success === true) {
            return ['success' => $success, 'data' => $data];
        } else {
            return ['success' => $success, 'msg' => $data];
        }
    }
}