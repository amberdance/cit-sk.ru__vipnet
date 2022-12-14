<?php

namespace App\Http\Controllers;

use App\Interfaces\CrudRepositoryInterface;
use App\Repositories\SignatureRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    /**
     * @var CrudRepositoryInterface
     */
    private $signatureRepository;

    public function __construct()
    {
        $this->signatureRepository = new SignatureRepository();
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(empty($request->all()) ? $this->signatureRepository->findAll() : $this->signatureRepository->findByFilter($request->all()));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        //
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->jsonSuccess($this->signatureRepository->delete($id));
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

        return $this->jsonSuccess($this->signatureRepository->massDelete($request->ids));
    }

}
