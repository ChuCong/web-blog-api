<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

use function App\Helpers\paginate;

class UserRepository extends BaseRepository
{
    public function getModel()
    {
        return User::class;
    }

    protected function getQueryBuilder()
    {
        $query = $this->model->newQuery();
        return $this->query = $query;
    }

    public function getListUser($limit, $page, $filters = [], 
        $sortField = 'created_at', $sortOrder= 'desc')
    {
        $query = $this->getQueryBuilder()->orderBy($sortField, $sortOrder);

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('full_name', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['course_id'])) {
            $query->whereHas('courseManagers', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        // Đếm số lượng khóa học và khóa học hoàn thành
        $query->withCount([
            'courseManagers',
            'courseComplete as completed_courses_count'
        ]);

        // Lấy lần truy cập cuối cùng
        $query->withMax('userlog', 'created_at');

        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $users = $query->skip($offset)->take($limit)->get();

        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $users,
            'paginate' => $paginate
        ];
    }

    public function getAllActiveUserIds()
    {
        return $this->model->where('active', User::ACTIVE)->pluck('email', 'id')->toArray();
    }

    public function getUserIdsByIds(array $ids)
    {
        return $this->model->whereIn('id', $ids)->pluck('email', 'id')->toArray();
    }

    public function getExportUsers()
    {
        return $this->getQueryBuilder()
            ->withCount(['courseManagers', 'courseComplete as completed_courses_count'])
            ->withMax('userlog', 'created_at')
            ->with(['courses']) // Eager load các khóa học user đã tham gia
            ->get()
            ->map(function($user) {
                $courseTitles = $user->courses->pluck('title')->toArray();

                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'ethnicity' => $user->ethnicity,
                    'disability' => $user->disability,
                    'birth_day' => $user->birth_day,
                    'education' => $user->education,
                    'organization' => $user->organization,
                    'course_titles' => implode(', ', $courseTitles),
                    'total_courses' => $user->course_managers_count,
                    'completed_courses' => $user->completed_courses_count,
                    'last_learned_at' => $user->userlog_max_created_at,
                    'created_at' => $user->created_at,
                    'active' => $user->active ? 'Đang hoạt động' : 'Đã xóa'
                ];
            });
    }

    public function getInactiveUserIds($numdaysInactive)
    {
        return $this->model
            ->whereDoesntHave('userlog', function ($q) use ($numdaysInactive) {
                $q->where('created_at', '>=', $numdaysInactive);
            })
            ->pluck('id');
    }
    
    public function countTotalUsers($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        return $query->count();
    }

    public function countActiveAndCompletedUsers($startDate = null, $endDate = null)
    {
        $activeQuery = $this->model->where('active', User::ACTIVE);
        $completedQuery = $this->model->whereHas('courseComplete');

        if ($startDate) {
            $activeQuery->where('created_at', '>=', $startDate);
            $completedQuery->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $activeQuery->where('created_at', '<=', $endDate);
            $completedQuery->where('created_at', '<=', $endDate);
        }

        return [
            'active_users' => $activeQuery->count(),
            'completed_users' => $completedQuery->distinct('id')->count('id')
        ];
    }

    public function topStudents($limit = 5, $startDate = null, $endDate = null)
    {
        $query = $this->model->withCount(['courseComplete' => function($q) use ($startDate, $endDate) {
            if ($startDate) $q->where('created_at', '>=', $startDate);
            if ($endDate) $q->where('created_at', '<=', $endDate);
        }]);
        return $query->orderByDesc('course_complete_count')->take($limit)->get(['id', 'full_name', 'course_complete_count']);
    }
}
