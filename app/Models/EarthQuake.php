<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarthQuake extends Model
{
    use HasFactory;

    protected $table = 'earthquakes';
    protected $guarded = [];

    public function getLat()
    {
        return $this->lat;
    }

    public function getLng()
    {
        return $this->lng;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function getScale_MD()
    {
        return $this->scale_MD;
    }

    public function getScale_ML()
    {
        return $this->scale_ML;
    }

    public function getScale_Mw()
    {
        return $this->scale_Mw;
    }

    public function getLocation()
    {
        return $this->location;
    }
}
