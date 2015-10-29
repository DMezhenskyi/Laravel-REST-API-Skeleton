<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Respond common method.
     *
     * @param  boolean   Transaction status
     * @return array     Data for client
     */

    public static function prepareResponse ($success, array $data) {
        if ($success === true) {
            return ['success' => $success, 'data' => $data];
        } else {
            return ['success' => $success, 'msg' => $data];
        }
    }
}
