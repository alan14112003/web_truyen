<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserGenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'level_id',
        'avatar',
        'password',
    ];

    protected $appends = [
        'gender_name',
        'avatar_url'
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
        'email_verified_at' => 'datetime',
        'gender' => 'integer',
        'level_id' => 'integer',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function story()
    {
        return $this->hasMany(Story::class);
    }
    public function getGenderNameAttribute()
    {
       return UserGenderEnum::getNameByValue($this->gender) === false ?
           'Không' : UserGenderEnum::getNameByValue($this->gender);
    }

    public function getAvatarUrlAttribute()
    {
        if (is_null($this->avatar)) {
            return asset("img/no_face.png");
        }
        return file_exists("storage/avatars/$this->id") ?
            asset("storage/$this->avatar") : $this->avatar;
    }
}
