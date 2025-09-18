<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\InactiveEmail;
use App\Models\Setting;
use App\Repositories\UserRepository;
use App\Services\SettingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyInactiveUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:inactive-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi email nhắc nhở user không học trong nhiều ngày';

    /**
     * Execute the console command.
     */
    public function handle(SettingService $settingService, UserRepository $userRepository)
    {
        $other = $settingService->findByKey(Setting::OTHER_KEY);
        // $other = json_decode($other->value, true);
        Log::debug($other);
        $numdaysInactive = isset($other) && isset($other['number_days_reminder_inactive']) 
            ? $other['number_days_reminder_inactive'] : Setting::NUMBER_DAYS_INACTIVE_REMINDER;
        $numberDaysAgo = Carbon::now()->subDays($numdaysInactive)->startOfDay();
        $inactiveUserIds = $userRepository->getInactiveUserIds($numberDaysAgo);
        Log::debug($inactiveUserIds);
        $users =  $userRepository->findWhereIn('id', $inactiveUserIds->toArray());

        foreach ($users as $user) {
            Log::debug(json_encode($user));
            Mail::to($user['email'])->send(
                new InactiveEmail(
                    $user['full_name'],
                    $numdaysInactive,
                    config('app.url')
                )
            );
        }

        $this->info('Đã gửi email cho các user in-active!');
    }
}
