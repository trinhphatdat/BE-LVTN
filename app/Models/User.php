<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'fullname',
        'phone_number',
        'email',
        'password',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'remember_token',
        'status',
    ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    //Mối quan hệ với các table khác
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'user_id');
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
