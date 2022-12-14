<?php

namespace App\Http\Controllers;

use App\Interfaces\CrudRepositoryInterface;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * @var CrudRepositoryInterface
     */
    private $organizationRepository;

    public function __construct()
    {
        $this->organizationRepository = new OrganizationRepository();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(empty($request->all()) ? $this->organizationRepository->findAll() : $this->organizationRepository->findByFilter($request->all()));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "label"         => "string|required",
            "tax_id"        => "string|required",
            "government_id" => "string|nullable",
            "note"          => "string|nullable",
            "city"          => "string|nullable",
            "district"      => "string|nullable",
        ]);

        return $this->jsonSuccess($this->organizationRepository->create($request->all()));
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            "label"         => "string|nullable",
            "tax_id"        => "string|nullable",
            "government_id" => "string|nullable",
            "note"          => "string|nullable",
            "city"          => "string|nullable",
            "district"      => "string|nullable",
        ]);

        return $this->jsonSuccess($this->organizationRepository->update($id, $request->all()));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->jsonSuccess($this->organizationRepository->delete($id));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function massDelete(Request $request): JsonResponse
    {

        $request->validate([
            "ids" => "array|required",
        ]);

        return $this->jsonSuccess($this->organizationRepository->massDelete($request->ids));
    }

}
