<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = "statuses";

    protected $fillable = ['name'];

    public function transation(){
        return $this->belongsTo(Transation::class, "status_id");
    }
}
