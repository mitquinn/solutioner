<?php

namespace App;

use Laravel\Spark\Team as SparkTeam;

class Team extends SparkTeam
{
    public function solutions()
    {
        return $this->hasMany('App\Solution');
    }

    public function tags()
    {
        return $this->hasMany('App\Tag');
    }
}
