<?php

namespace German\Models\Word;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    /**
     * Relationship one to one. One word has one translate.
     */
    public function metaInfo()
    {
        return $this->hasOne('German\Models\Meta');
    }

    /**
     * Relationship belongs to many word collections.
     */
    public function toCollections()
    {
        return $this->belongsToMany('German\Models\Collection', 'words_collections');
    }
}
