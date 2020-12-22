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
    
    public function benAccount()
    {
        return $this->belongsTo(BankingAccount::class, "ben_banking_account_id");
    }

    public function prinAccount()
    {
        return $this->belongsTo(BankingAccount::class, "prin_banking_account_id");
    }

    public function status(){
        return $this->hasOne(Status::class, "status_id");
    }

    public function operation(){
        return $this->hasOne(Operation::class, "operation_id");
    }

}
