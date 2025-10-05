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
        return User::where('email', $email)->first();
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
        $q = User::where('role', 'student')->whereNull('deleted_at');
        if (!empty($params['keyword'])) {
            $k = $params['keyword'];
            $q->where('email', 'like', "%{$k}%");
        }
        $sortId = $params['sortId'] ?? 'email';
        $sortOrder = $params['sortOrder'] ?? 'asc';
        $pageSize = intval($params['pageSize'] ?? 10);
        return $q->orderBy($sortId, $sortOrder)->paginate($pageSize);
    }
}
