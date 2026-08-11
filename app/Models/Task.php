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
        'is_completed'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function assignedTo(){
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
