<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Repositories\BaseRepository;

class PasswordResetRepository extends BaseRepository
{
    public function getModel()
    {
        return PasswordReset::class;
    }
    
}
