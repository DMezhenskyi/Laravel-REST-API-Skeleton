<?php

namespace German\Models;

use German\Exceptions\ModelsExceptions\PermitException;
use Illuminate\Database\Eloquent\Model;
use \Cache;

class Collection extends Model
{
    protected $table = 'collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];


    public function toUsers()
    {
        return $this->belongsToMany('German\Models\User', 'users_collections');
    }

    public function getList () {
//        Todo: Create caching for pagination
//        $collections = Cache::remember('collections', 60, function()
//        {
            return Collection::paginate(9)->toArray();
//        });
//        return $collections;
    }

    public function saveCollection ($data) {
//        Cache::forget('collections');
        return self::create(['title' => $data['title']]);
    }

    public function updateCollection ($collectionId,$userId, $data) {
        if ($this->checkPermits($collectionId,$userId)) {
//            Cache::forget('collections');
            return self::where('id','=',$collectionId)->update([
                'title' => $data['title']
            ]);
        }
    }

    public function deleteCollection ($collectionId,$userId) {
        if ($this->checkPermits($collectionId,$userId)) {
            Cache::forget('collections');
            return self::where('id','=',$collectionId)->delete();
        }
    }



    /**
     * ------------------------------------------------------
     * Notice: Leave your protected methods here please.
     *         Keep code clean and readable.
     * ------------------------------------------------------
     */
    protected function checkPermits ($collectionId, $userId) {
        $check = self::where('id','=',$collectionId)->where('user_id','=',$userId)->count();
        if ($check == 1)
            return true;
        throw new PermitException('Bad permit.');
    }

}
