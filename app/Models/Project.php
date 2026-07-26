<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $hidden = ['pivot'];
    protected $fillable = [
        'user_id',
        'title',
        'description',
    ];
    public function users(){
        return $this->belongsToMany(User::class,'user_projects')->withPivot('role');
    }

    public function categories(){
        return $this->hasMany(Category::class);
    }

}
