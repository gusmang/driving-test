<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    public function __construct(protected UserRepository $repo) {}

    public function getAllStudents()
    {
        return $this->repo->listStudentsAll();
    }

    public function getStudentsPaginated(array $params)
    {
        return $this->repo->paginateStudents($params);
    }

    public function enableDisable(int $id, bool $enable)
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $this->repo->update($user, ['is_active' => $enable]);
    }

    public function softDelete(int $id)
    {
        $user = $this->repo->findById($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $this->repo->softDelete($user);
    }
}
