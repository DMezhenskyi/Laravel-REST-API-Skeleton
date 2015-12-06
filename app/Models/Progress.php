<?php

namespace German\Models\Progress;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = "progress";

    public function word()
    {
        return $this->hasOne('German\Models\Word');
    }
}
