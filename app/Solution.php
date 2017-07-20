<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solution extends Model
{
    use SoftDeletes;

    protected $table = 'solution';

    protected $fillable = [
        'issue',
        'cause',
        'solution',
        'active',
        'team_id',
        'tags'
    ];

    public function team()
    {
        return $this->hasOne('App\Team');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
}
