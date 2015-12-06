<?php

namespace German\Http\Controllers\API\v1\Collection;

use Validator;
use German\Models\Collection;
use Illuminate\Http\Request;
use German\Http\Controllers\API\v1\BaseApiController as ApiController;
use JWTAuth;

class CollectionController extends ApiController
{
    protected $collection;
    /**
     * Create a new user collection instance.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->middleware('jwt.auth', ['except' => 'getList']);
    }

    /**
     * Getting a list of collections.
     *
     * @return json
     *
     */
    public function index () {

        $collections = $this->collection->getList();
        return response()->json(self::prepareResponse(true, $collections), 200);

    }

    /**
     * Save new collection item.
     *
     * @param Illuminate\Http\Request
     * @return json
     *
     */
    public function store (Request $request) {
        $data = $request->only('title');
        $validate = $this->validator($data);

        if ($validate->fails()) {
            return response()->json(self::prepareResponse(false, $validate->messages()), 403);
        }

        $newCollection = $this->collection->saveCollection($data);

        return response()->json(self::prepareResponse(true, $newCollection), 200);
    }

    /**
     * Get item equal id.
     *
     * @param $id
     * @return json
     *
     */
    public function show ($id) {
        $user = Collection::findOrFail($id);
        return response()->json(self::prepareResponse(true, $user), 200);
    }

    /**
     * Get item equal id.
     *
     * @param $collectionId
     * @param \Illuminate\Http\Request
     *
     * @return json
     *
     */
    public function update ($collectionId, Request $request) {

        $data = $request->only('title');
        $validate = $this->validator($data);

        $userId = JWTAuth::parseToken()->getPayload()->get('sub');

        if ($validate->fails()) {
            return response()->json(self::prepareResponse(false, $validate->messages()), 403);
        }

        $updateCollection = $this->collection->updateCollection($collectionId, $userId, $data);

        return response()->json(self::prepareResponse(true, $updateCollection), 200);

    }

    /**
     * Delete item equal id.
     *
     * @param $collectionId
     * @return json
     *
     */
    public function destroy ($collectionId) {
        $userId = JWTAuth::parseToken()->getPayload()->get('sub');
        $this->collection->deleteCollection($collectionId, $userId);

        return response()->json(self::prepareResponse(true, []), 200);
    }



    /**
     * ------------------------------------------------------
     * Notice: Leave your protected methods here please.
     *         Keep code clean and readable.
     * ------------------------------------------------------
     */


    /**
     * If params valid checking.
     *
     * @param $collectionId
     * @return json
     *
     */
    protected function validator (array $data) {
        return Validator::make($data, [
            'title' => 'required|max:255',
        ]);
    }


}