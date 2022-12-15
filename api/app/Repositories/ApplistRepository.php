<?php

namespace App\Repositories;

use App\Helpers\ValidationHelper;
use App\Http\Resources\BaseCollection;
use App\Interfaces\CrudRepositoryInterface;
use App\Interfaces\IResourceCollection;
use App\Models\Application;
use Illuminate\Validation\ValidationException;

class ApplistRepository implements CrudRepositoryInterface
{

    public function findAll(): IResourceCollection
    {
        return new BaseCollection(Application::with(["organization", "signature"])->get());
    }

    public function findByFilter(array $filter): IResourceCollection
    {
        $query = Application::with(["organization", "signature"]);

        if (isset($filter["reception_date"])) {
            $query->whereBetween('reception_date', $filter['reception_date']);
        }

        if (isset($filter["search"])) {
            $query->where("id", $filter["search"]);
            $query->orWhere("note", "like", "%" . $filter["search"] . "%");
            $query->orWhereRelation("organization", "label", "like", "%" . $filter["search"] . "%");
            $query->orWhereRelation("organization", "tax_id", "like", "%" . $filter["search"] . "%");
            $query->orWhereRelation("signature", "label", "like", "%" . $filter["search"] . "%");
        }

        $query->orderByDesc("id");

        return new BaseCollection(isset($filter['per_page']) ? $query->paginate($filter['per_page']) : $query->get());
    }

    public function findById(int $id): Application
    {
        return Application::with(["organization", "signature"])->where("id", $id)->firstOrFail();
    }

    public function update(int $id, array $fields): Application
    {

        ValidationHelper::sanitizeArray($fields);

        $application = $this->findById($id);
        $application->update($fields);

        return $this->findById($application->id);
    }

    public function create(array $fields): Application
    {
        ValidationHelper::sanitizeArray($fields);

        if ($this->isReceptionDateExists($fields["reception_date"])) {
            throw ValidationException::withMessages([
                'reception_date' => "Application with date " . $fields["reception_date"] . " already exists",
            ]);
        }

        $application                  = new Application();
        $application->reception_date  = $fields["reception_date"];
        $application->organization_id = $fields["organization_id"];
        $application->signature_id    = $fields["signature_id"];
        $application->person_count    = $fields["person_count"];

        if (isset($fields["note"])) {
            $application->note = $fields["note"];
        }

        $application->save();

        return $this->findById($application->id);
    }

    public function delete($id): int
    {
        return Application::destroy($id);
    }

    public function massDelete(array $ids): int
    {
        return Application::whereIn("id", $ids)->delete();
    }

    public static function isReceptionDateExists(string $receptionDate): bool
    {
        return boolval(Application::select("id")->where("reception_date", $receptionDate)->count());
    }
}
