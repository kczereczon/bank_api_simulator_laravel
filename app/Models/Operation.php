<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nrb_ben',
        'name_ben',
        'address_ben',
        'amount',
        'posting_date',
        'nrb_prin',
        'name_prin',
        'address_prin'

    ];

    public function benAccount()
    {
        return $this->belongsTo(BankingAccount::class, "ben_banking_account_id");
    }

    public function prinAccount()
    {
        return $this->belongsTo(User::class, "prin_banking_account_id");
    }

    public function transaction()
    {
        return $this->belongsTo(BankingAccount::class);
    }
}
