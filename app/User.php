<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'email_verified_at', '2fa_setup_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'email', 'password', 'is_admin', 'remember_token', 
        'email_verified_at', '2fa_enabled', '2fa_secret', 
        '2fa_backup_codes', '2fa_setup_at', 'deleted_at',
        'updated_at'
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function url()
    {
        return url(config('app.url').'/'.$this->username);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function statuses()
    {
        return $this->hasManyThrough(
            Status::class,
            Profile::class
        );
    }

    public function filters()
    {
        return $this->hasMany(UserFilter::class);
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.User.'.$this->id;
    }
}
