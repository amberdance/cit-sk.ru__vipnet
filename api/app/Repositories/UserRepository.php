<?php
namespace App\Repositories;

use App\Helpers\ValidationHelper;
use App\Http\Resources\User\UserResource;
use App\Interfaces\ResourceModel;
use App\Interfaces\UserRepositoryInterface;
use App\Lib\Constant;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Exceptions\UnauthorizedException;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return User::all();
    }

    /**
     * @param array $filter
     *
     * @return Collection
     */
    public function findByFilter(array $filter): Collection
    {
        $query = User::select()->orderByDesc('id');

        $query->where('id', $filter['search']);
        $query->orWhere('first_name', 'like', '%' . $filter['search'] . '%');
        $query->orWhere('last_name', 'like', '%' . $filter['search'] . '%');
        $query->orWhere('patronymic', 'like', '%' . $filter['search'] . '%');
        $query->orWhere('login', 'like', '%' . $filter['search'] . '%');
        $query->orWhere('email', 'like', '%' . $filter['search'] . '%');

        return $query->get();
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * @param array $params
     *
     * @return ResourceModel
     */
    public function create(array $params): ResourceModel
    {
        $user                  = new User;
        $user->first_name      = ValidationHelper::mbUcFirst($params['first_name']);
        $user->last_name       = ValidationHelper::mbUcFirst($params['last_name']);
        $user->organization_id = $params['organization_id'];
        $user->department      = ValidationHelper::mbUcFirst($params['department']);
        $user->post            = ValidationHelper::mbUcFirst($params['post']);
        $user->login           = $params['login'];
        $user->password        = Hash::make($params['password']);
        $user->is_blocked      = $params['is_blocked'] ?? false;
        $user->email           = $params['email'] ?? null;
        $user->patronymic      = $params['patronymic'] ?? null;
        $user->phone           = $params['phone'] ?? null;
        $user->assignRole(Constant::USER_ROLE);
        $user->save();
        $user->forgetSharedCache();

        return $user;
    }

    /**
     * @param int $id
     * @param array $params
     *
     * @return User
     */
    public function update(int $id, array $params): User
    {

        $user = User::findOrFail($id);

        if ($user->hasRole(Constant::ROOT_ROLE)) {
            throw new UnauthorizedException(403, 'Cannot update ' . Constant::ROOT_ROLE . ' role');
        }

        $user->fill($params);

        if (isset($params['organization_id'])) {
            $user->organization_id = $params['organization_id'];
        }

        if (isset($params['login'])) {
            $user->login = $params['login'];
        }

        if (isset($params['password'])) {
            $user->password = Hash::make($params['password']);
        }

        if (isset($params['is_blocked'])) {
            $user->is_blocked = $params['is_blocked'];
        }

        $user->save();
        $user->forgetSharedCache();

        return $user;
    }

    /**
     * @param mixed $id
     *
     * @return int
     */
    public function delete($id): int
    {

        $id       = is_array($id) ? $id : [$id];
        $trulyIds = [];

        foreach (User::whereIn('id', $id)->get() as $user) {
            if ($user->hasRole(Constant::ROOT_ROLE)) {
                continue;
            }

            $trulyIds[] = $user->id;
        }

        User::forgetSharedCache();

        return count($trulyIds) ? User::whereIn('id', $trulyIds)->delete() : 0;
    }

    /**
     * @param int $userId
     * @param array $roles
     *
     * @return UserResource
     */
    public function assignRoles(int $userId, array $roles): UserResource
    {
        User::forgetSharedCache();

        return new UserResource(User::findOrFail($userId)->syncRoles($roles));
    }

    /**
     * @param int $userId
     * @param array $permissions
     *
     * @return User
     */
    public function assignPermissions(int $userId, array $permissions): User
    {
        User::forgetSharedCache();

        return User::findOrFail($userId)->syncPermissions($permissions);
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return Role::handleSharedCache(fn() => Role::select(['id', 'name', 'description'])->where('name', '!=', Constant::ROOT_ROLE)->get());
    }

    /**
     * @return Collection
     */
    public function getPermissions(): Collection
    {
        return Permission::handleSharedCache(fn() => Permission::select(['id', 'name', 'description'])->get());
    }

    /**
     * @return UserResource
     */
    public static function getAuthenticatedUser(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
