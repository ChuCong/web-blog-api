<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RedisService
{
    public function getRedis($key = null)
    {
        try {
            $data = Redis::get($key);
            return json_decode($data, true);
        } catch (Exception $e) {
            Log::error($e);
            return null;
        }
    }

    public function setRedis($key = null, $data = null)
    {
        try {
            if ($key && ($data || is_array($data))) {
                if (is_array($data)) $data = json_encode($data);
                Redis::set($key, $data);
                return 1;
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    public function setCacheRedisInSeconds($key = null, $data = null, $seconds = 14400)
    {
        try {
            if ($key && ($data || is_array($data))) {
                if (is_array($data)) $data = json_encode($data);
                Redis::set($key, $data, 'EX', $seconds);
                return 1;
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function delRedis($key = null)
    {
        try {
            Redis::del($key);
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function checkKey($key = null)
    {
        $data = Redis::get($key);
        if ($data) {
            return 1;
        }
        return 0;
    }
    public function delAllRedis()
    {
        try {
            Redis::flushdb();
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function delRedisByPattern($pattern)
    {
        try {
            $keys = Redis::keys($pattern);
            $prefix = config('database.redis.options.prefix', '');

            if ($keys) {
                $keysToDelete = $prefix ? array_map(fn($key) => Str::replaceFirst($prefix, '', $key), $keys) : $keys;
                Redis::del($keysToDelete);
            }
            return 1;
        } catch (Exception $e) {
            Log::error('Error deleting Redis keys: ' . $e->getMessage());
            return 0;
        }
    }

    public function keys($key = null)
    {
        try {
            return Redis::keys($key);
        } catch (Exception $e) {
            Log::error($e);
            return null;
        }
    }
}
