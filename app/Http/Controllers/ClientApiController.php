<?php

namespace App\Http\Controllers;

use App\Contracts\ClientRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Inertia;
use Illuminate\Support\Facades\Log;

class ClientApiController extends Controller
{
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    private const CLIENT_LISTING = 'client_listing';


    /**
     * Display a listing of the resource.
     *
     * @return Inertia\Response
     */
    public function index(): JsonResponse
    {
        try {
            $data = [
                'clients' => $this->clientRepository->all(),
                'message' => SELF::CLIENT_LISTING
            ];

            return response()->json($data, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Inertia\Response
     */
    public function syncrhonize(): JsonResponse
    {
        try {
            $this->clientRepository->all(true);
            $data = [
                'message' => 'Synchronize success'
            ];

            return response()->json($data, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json($e->getMessage(), 500);
        }
    }

    
}
