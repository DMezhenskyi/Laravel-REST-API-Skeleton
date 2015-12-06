<?php

namespace German\Http\Controllers\API\v1\User;

use Validator;
use German\Models\User;
use Illuminate\Http\Request;
use German\Http\Controllers\API\v1\BaseApiController as ApiController;
use JWTAuth;
use Exception;
use German\Exceptions\ModelsExceptions\DBException;
use Illuminate\Support\Facades\Lang;

class WordController extends ApiController
{
    /**
     * Create a new word controller instance.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => 'getList']);
    }

    //TODO

}