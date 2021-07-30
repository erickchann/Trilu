<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardList extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'board_id'];

    public function cards() {
        return $this->hasMany(Card::class, 'list_id');
    }

    public function board() {
        return $this->belongsTo(Board::class);
    }
}
