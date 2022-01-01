<?php

namespace App\Models;

use App\Mail\VerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\EmailVerificationNotification;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime:d-m-Y',
        'updated_at' => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y',
    ];
    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }
    public function products() {
        return $this->hasMany(Product::class);
    }
    public function carts() {
        return $this->hasMany(Cart::class);
    }
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->products()->delete();
            $model->carts()->delete();
        });
    }
}
