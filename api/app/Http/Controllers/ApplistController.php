<?php

namespace App\Http\Controllers;

use App\Interfaces\CrudRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplistController extends Controller
{
    /**
     * @var CrudRepositoryInterface
     */
    private $applistRepository;

    public function __construct(CrudRepositoryInterface $applistRepository)
    {
        $this->applistRepository = $applistRepository;

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
        $request->validate([
            'note' => 'nullable|string|min:6',
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
        $request->validate([
            'status_id' => 'nullable|integer',
            'is_done'   => 'nullable|boolean',
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
        return $this->jsonSuccess($this->applistRepository->delete($id));
    }

}
