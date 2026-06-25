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
        $roles = $this->extractRoles($data) ?? [UserRole::GENERAL];

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = parent::create($data);
        $user->syncRoles($roles);

        return $user->load('userRoles');
    }

    public function update(int $id, array $data): Model
    {
        $data = $this->normalizeOptionalFields($data);
        $roles = $this->extractRoles($data);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user = parent::update($id, $data);

        if ($roles !== null) {
            $user->syncRoles($roles);
        }

        return $user->load('userRoles');
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

    /**
     * @return array<int, int>|null
     */
    private function extractRoles(array &$data): ?array
    {
        if (array_key_exists('roles', $data)) {
            $roles = array_map('intval', $data['roles'] ?? []);
            unset($data['roles']);

            return $roles;
        }

        if (array_key_exists('role', $data)) {
            $role = (int) $data['role'];
            unset($data['role']);

            return [$role];
        }

        return null;
    }
}
