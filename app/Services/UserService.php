<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $data = $this->normalizeOptionalFields($data);
        $data['role'] = $data['role'] ?? UserRole::GENERAL;

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $data = $this->normalizeOptionalFields($data);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::update($id, $data);
    }

    protected function normalizeOptionalFields(array $data): array
    {
        foreach (['department_id', 'designation_id', 'reporting_manager_id'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
