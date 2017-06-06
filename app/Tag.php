<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $fillable = [
        'name',
        'team_id'
    ];

    public function team()
    {
        return $this->hasOne('App\Team');
    }

    public function solutions()
    {
        return $this->belongsToMany('App\Solution');
    }
}
