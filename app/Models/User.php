<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'active'
    ];

    public function bankingAccounts()
    {
        return $this->hasMany(BankingAccount::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
