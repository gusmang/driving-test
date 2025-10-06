<?php


namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Str;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', strtolower($email))->where('is_verified', true)->first();
    }

    public function findById(int $id): ?User
    {
        return User::where('id', $id)->whereNull('deleted_at')->first();
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function softDelete(User $user): User
    {
        $user->delete();
        return $user;
    }

    public function listStudentsAll(): \Illuminate\Support\Collection
    {
        return User::where('role', 'student')->whereNull('deleted_at')->get();
    }

    public function paginateStudents(array $params)
    {
        $query = User::where('role', 'student')->whereNull('deleted_at');

        // Filter keyword
        if (!empty($params['keyword'])) {
            $keyword = $params['keyword'];
            $query->where('email', 'like', "%{$keyword}%");
        }

        // Sorting
        $sortId = $params['sortId'] ?? 'email';
        $sortOrder = $params['sortOrder'] ?? 'asc';
        $query->orderBy($sortId, $sortOrder);

        // Pagination
        $pageSize = intval($params['pageSize'] ?? 10);
        $page = intval($params['pageIndex'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page);
    }
}
