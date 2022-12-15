<?php

namespace App\Http\Controllers;

use App\Interfaces\CrudRepositoryInterface;
use App\Repositories\ApplistRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplistController extends Controller
{
    /**
     * @var CrudRepositoryInterface
     */
    private $applistRepository;

    public function __construct()
    {
        $this->applistRepository = new ApplistRepository();

    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(empty($request->all()) ? $this->applistRepository->findAll() : $this->applistRepository->findByFilter($request->all()));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $this->authorize("application.create");

        $request->validate([
            'organization_id' => "integer|required",
            "signature_id"    => "integer|required",
            "person_count"    => "integer|required",
            "reception_date"  => "date|required",
            'note'            => 'nullable|string',
        ]);

        return $this->jsonSuccess($this->applistRepository->create($request->all()));
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {

        $this->authorize("application.update");

        $request->validate([
            'reception_date'  => 'nullable|date',
            'organization_id' => 'nullable|integer',
            'signature_id'    => 'nullable|integer',
            'person_count'    => 'nullable|integer',
            'note'            => 'nullable|string',
        ]);

        return $this->jsonSuccess($this->applistRepository->update($id, $request->all()));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {

        $this->authorize("application.delete");

        return $this->jsonSuccess($this->applistRepository->delete($id));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function massDelete(Request $request): JsonResponse
    {
        $this->authorize("application.delete");

        $request->validate([
            "ids" => "array|required",
        ]);

        return $this->jsonSuccess($this->applistRepository->massDelete($request->ids));
    }

}
