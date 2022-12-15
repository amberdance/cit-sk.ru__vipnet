<?php

namespace App\Repositories;

use App\Helpers\ValidationHelper;
use App\Http\Resources\BaseCollection;
use App\Interfaces\CrudRepositoryInterface;
use App\Interfaces\IResourceCollection;
use App\Models\Organization;

class OrganizationRepository implements CrudRepositoryInterface
{

    public function findAll(): IResourceCollection
    {
        return new BaseCollection(Organization::select(["id", "label", "tax_id", "government_id", "city", "district", "note"])->get());
    }

    public function findByFilter(array $filter): IResourceCollection
    {
        $query = Organization::select(["id", "label", "tax_id", "government_id", "city", "district", "note"]);

        if (isset($filter["search"])) {
            $query->where("id", $filter["search"]);
            $query->orWhere("label", "like", "%" . $filter["search"] . "%");
            $query->orWhere("tax_id", "like", "%" . $filter["search"] . "%");
        }

        $query->orderByDesc("id");

        return new BaseCollection(isset($filter['per_page']) ? $query->paginate($filter['per_page']) : $query->get());
    }

    public function findById(int $id): Organization
    {
        return Organization::findOrFail($id);
    }

    public function update(int $id, array $fields): Organization
    {
        $organization = $this->findById($id);
        $organization->update($fields);

        return $organization;
    }

    public function create(array $fields): Organization
    {

        ValidationHelper::sanitizeArray($fields);

        $organization = new Organization;

        $organization->label  = $fields["label"];
        $organization->tax_id = $fields["tax_id"];

        if (isset($fields["city"])) {
            $organization->city = $fields["city"];
        }

        if (isset($fields["district"])) {
            $organization->district = $fields["district"];
        }

        if (isset($fields["government_id"])) {
            $organization->government_id = $fields["government_id"];
        }

        $organization->save();

        return $this->findById($organization->id);
    }

    public function delete($id): int
    {
        return Organization::destroy($id);
    }

    public function massDelete(array $ids): int
    {
        return Organization::whereIn("id", $ids)->delete();
    }
}
