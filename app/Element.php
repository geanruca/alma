<?php

namespace App;

use App\Element;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    public function parent()
    {
        return $this->belongsTo(Element::class,'parent','id');
    }
    
    public function sons()
    {
        return $this->hasMany(Element::class,'id','parent');
    }
}
