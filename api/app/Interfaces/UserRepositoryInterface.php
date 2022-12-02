<?php
namespace App\Interfaces;

use App\Http\Resources\User\UserResource;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @param int $userId
     * @param array $roles
     *
     * @return UserResource
     */
    public function assignRoles(int $userId, array $roles): UserResource;

    /**
     * @param int $userId
     * @param array $permissions
     *
     * @return User
     */
    public function assignPermissions(int $userId, array $permissions): User;

    /**
     * @return Collection
     */
    public function getRoles(): Collection;

    /**
     * @return Collection
     */
    public function getPermissions(): Collection;

    /**
     * @return UserResource
     */
    public static function getAuthenticatedUser(): UserResource;

}
