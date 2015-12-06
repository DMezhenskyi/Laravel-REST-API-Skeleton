<?php

namespace German\Models\Meta;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = "meta_de";

    /**
     * Relationship one to one. One word belongs to one part of speech.
     */

    public function partOfSpeech()
    {
        return $this->belongsTo('German\Models\SpeechPart', 'speech_part');
    }
}
