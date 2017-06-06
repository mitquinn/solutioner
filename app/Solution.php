<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
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
