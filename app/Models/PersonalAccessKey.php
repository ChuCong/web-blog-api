<?php

namespace App\Models;

use App\AppMain\Config\AppConst;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessKey extends Model
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'user_id',
        'last_used_at',
        'time_expired',
        'guard_name'
    ];

    // public function user(){
    //     return $this->belongsTo(Admin::class, 'user_id');
    // }

    public function user_frontend(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function findKeyAndUpdateLastUsed($key, $guard_name)
    {
        if (!$key) return null;

        $now = Carbon::now();
        $personalAccessKey = static::where('key', $key)->where('guard_name', $guard_name)->where(function($q) use($now){
            $q->where('time_expired', '>', $now)->orWhereNull('time_expired');
        })->first();

        if($personalAccessKey){
            $personalAccessKey->last_used_at = $now;
            $personalAccessKey->save();
        }

        return $personalAccessKey;
    }

    public static function findCompanyIdByKey($key)
    {
        if (!$key) return null;
        $company_id = null;
        $data = static::where('key', $key)->with('user')
        ->first();

        if (isset($data['user']) && isset($data['user']['company_id'])) {
            $company_id = $data['user']['company_id'];
        }
        return $company_id;
    }
}
