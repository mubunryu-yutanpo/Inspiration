<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //カラムに挿入するものを指定
    protected $fillable = ['user_id', 'idea_id'];

    //他のモデルとの関係
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function idea(){
        return $this->belongsTo('App\Idea');
    }
}
