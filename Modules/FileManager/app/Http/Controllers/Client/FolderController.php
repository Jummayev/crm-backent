<?php

namespace Modules\FileManager\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FileManager\Http\Requests\Folder\StoreFolderRequest;
use Modules\FileManager\Http\Requests\Folder\UpdateFolderRequest;
use Modules\FileManager\Models\Folder;

class FolderController extends Controller
{
    protected mixed $modelClass = Folder::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->getQuery($request);
        $data = $query->paginate($request->get('per_page'));

        return okWithPaginateResponse($data);
    }

    public function client(Request $request): JsonResponse
    {
        $query = $this->getQuery($request);
        $query->where('status', Folder::STATUS_ACTIVE);
        $data = $query->paginate($request->get('per_page'));

        return okWithPaginateResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $model = $this->modelClass::create($data);

        return okResponse($model);
    }

    /**
     * Show the specified resource.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $query = $this->getQuery($request);
        $model = $query->firstOrFail($id);

        return okResponse($model);
    }

    public function clientShow(Request $request, int $id): JsonResponse
    {
        $query = $this->getQuery($request);
        $query->where('status', Folder::STATUS_ACTIVE);
        $model = $query->firstOrFail($id);

        return okResponse($model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFolderRequest $request, Folder $folder): JsonResponse
    {
        $data = $request->validated();
        $folder->update($data);

        return okResponse($folder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        $folder->delete();

        return okResponse($folder);
    }
}
