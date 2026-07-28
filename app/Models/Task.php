<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'position',
        'assigned_to',
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
