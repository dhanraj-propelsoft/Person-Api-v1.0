<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Member extends Authenticatable
{
    use HasFactory, HasApiTokens;

    public function personDetails()
    {
        return $this->hasOne(PersonDetails::class, 'uid', 'uid');
    }
}
