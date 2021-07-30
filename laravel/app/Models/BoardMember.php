<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'board_id', 'user_id'];
    protected $appends = ['id', 'first_name', 'last_name', 'initial'];

    public function getIdAttribute() {
        return $this->user_id;
    }

    public function getFirstNameAttribute() {
        return User::where('id', $this->user_id)->first()->first_name;
    }

    public function getLastNameAttribute() {
        return User::where('id', $this->user_id)->first()->last_name;
    }

    public function getInitialAttribute() {
        return $this->getFirstNameAttribute()[0] . $this->getLastNameAttribute()[0];
    }
}