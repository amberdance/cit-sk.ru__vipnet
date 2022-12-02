<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray($request)
    {
        $routeName = $request->route()->getName();

        return array_merge(parent::toArray($request),
            [
                'organization' => $this->organization,

                $this->mergeWhen($routeName == 'me' || $routeName == 'login', [
                    'roles'       => $this->roles->pluck('name'),
                    'permissions' => $this->getAllPermissions()->pluck('name'),
                ]),

                $this->mergeWhen($routeName !== 'me', [
                    'roles'       => $this->whenLoaded('roles', $this->getRoleNames()),
                    'permissions' => $this->whenLoaded('permissions', $this->getAllPermissions()),
                ]),
            ]
        );
    }
}
