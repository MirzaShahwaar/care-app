<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    // Specify the table associated with the model
    protected $table = 'password_resets';

    // Specify the attributes that are mass assignable
    protected $fillable = ['email', 'otp', 'expires_at'];

    // Disable timestamps if not needed
    public $timestamps = false;
}
