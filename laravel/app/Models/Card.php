<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'list_id', 'id'];
    protected $appends = ['card_id'];

    public function getCardIdAttribute() {
        return $this->id;
    }

    public function list()
    {
        return $this->belongsTo(BoardList::class, 'list_id');
    }
}