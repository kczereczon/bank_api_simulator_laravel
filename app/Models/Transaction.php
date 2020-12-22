<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        "nrb_ben",
        "name_ben",
        "address_ben",
        "amount",
        "title",
        "nrb_prin",
        "name_prin",
        "direction",
        "realisation_date"
    ];

    public function bankingAccounts()
    {
        return $this->belongsTo(BankingAccount::class);
    }
    
    public function users()
    {
        return $this->belongsTo(User::class);
    }

}
