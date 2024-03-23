<?php

namespace App\Http\Controllers;

use App\Contracts\ClientRepositoryInterface;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest; // Add a request for update validation
use App\Http\Resources\ClientResource;
use App\Services\GetOrderList;
use Exception;
use Illuminate\Http\JsonResponse;
use Inertia;
use Illuminate\Support\Facades\Lang;  // Include for translations

class ClientController extends Controller
{
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    // Message constants for translations (replace with your actual message content)
    private const CLIENT_CREATED_SUCCESS = 'client_created_success';
    private const CLIENT_CREATION_FAILED = 'client_create_failed';
    private const CLIENT_NOT_FOUND = 'client_not_found';
    private const CLIENT_UPDATED_SUCCESS = 'client_updated_success';
    private const CLIENT_UPDATE_FAILED = 'client_update_failed';
    private const CLIENT_DELETED_SUCCESS = 'client_deleted_success';
    private const CLIENT_DELETE_FAILED = 'client_delete_failed';


    /**
     * Display a listing of the resource.
     *
     * @return Inertia\Response
     */
    public function index(): Inertia\Response | JsonResponse
    {
        try{
            $clients = $this->clientRepository->all();
            return Inertia\Inertia::render('Clients/Index', [
                'clients' => ClientResource::collection($clients),
                'filters' => request('filters'),
            ]);

        }catch(Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }

       
    }

    /**
     * Display a create of the resource.
     *
     * @return Inertia\Response
     */
    public function create(): Inertia\Response
    {
        return Inertia\Inertia::render('Clients/Create', []);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->clientRepository->create($data);
            return response()->json(['message' => Lang::get(self::CLIENT_CREATED_SUCCESS)], 201);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => Lang::get(self::CLIENT_CREATION_FAILED)], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Inertia\Response | JsonResponse
     */
    public function show(int $id): Inertia\Response | JsonResponse
    {
        try {
            $client = $this->clientRepository->find($id);
            if (!$client) {
                return response()->json(['message' => Lang::get(self::CLIENT_NOT_FOUND)], 404);
            }

            return Inertia\Inertia::render('Clients/Show', [
                'client' => new ClientResource($client),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => Lang::get(self::CLIENT_UPDATE_FAILED)], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            if (!$this->clientRepository->update($id, $data)) {
                return response()->json(['message' => Lang::get(self::CLIENT_NOT_FOUND)], 404);
            }
            return response()->json(['message' => Lang::get(self::CLIENT_UPDATED_SUCCESS)]);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => Lang::get(self::CLIENT_UPDATE_FAILED)], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            if (!$this->clientRepository->delete($id)) {
                return response()->json(['message' => Lang::get(self::CLIENT_NOT_FOUND)], 404);
            }
            return response()->json(['message' => Lang::get(self::CLIENT_DELETED_SUCCESS)], 200);
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['message' => Lang::get(self::CLIENT_DELETE_FAILED)], 500);
        }
    }
}
