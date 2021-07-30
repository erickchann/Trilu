<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    function lists() {
        return $this->hasMany(BoardList::class);
    }

    function members() {
        return $this->hasMany(BoardMember::class);
    }
}