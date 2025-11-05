<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function getAll(array $filters = [])
    {
        $query = User::query();

        // Eğer company_id varsa, filtrele
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        return $query->get();
    }

}
