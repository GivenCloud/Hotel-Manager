<?php

namespace App\Http\Controllers\Api\ManyToMany;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\GuestService\StoreGuestRequest;
use App\Http\Requests\ManyToMany\GuestService\StoreServiceRequest;
use App\Models\Guest;
use App\Models\ManyToMany\GuestService;
use App\Models\Service;

/**
 * @OA\Tag(
 *   name="GuestService",
 *   description="API Endpoints of GuestService Controller"
 * )
 */

class GuestServiceController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/guest-service",
     *    summary="Get all guest-service",
     *    tags={"GuestService"},
     *    description="List of guest-service",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(GuestService::get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/guest-services/add-guests",
     *     tags={"GuestService"},
     *     summary="Add guests to a service",
     *     description="Add guests to a service", 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="guest_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeGuests(StoreGuestRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $service->guests()->sync($request->guest_ids);
        return response()->json($service->guests()->get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/guest-services/add-services",
     *     tags={"GuestService"},
     *     summary="Add services to a guest",
     *     description="Add services to a guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="guest_id", type="integer", example=1),
     *             @OA\Property(property="service_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function storeServices(StoreServiceRequest $request)
    {
        $guest = Guest::findOrFail($request->guest_id);
        $guest->services()->sync($request->service_ids);
        return response()->json($guest->services()->get(), 200);
    }

/**
 * Display the specified guest-service.
 *
 * @OA\Get(
 *    path="/api/guest-service/{id}",
 *    tags={"GuestService"},
 *    summary="Get a specific guest-service",
 *    description="Retrieve a specific guestservice by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the guest-service",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Successful operation",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Guest-Service not found"
 *    )
 * )
 */
    public function show($id)
    {
        $guestservice = GuestService::findOrFail($id);
        return response()->json($guestservice, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/guest-services/update-guests",
     *     tags={"GuestService"},
     *     summary="Update guests for a service",
     *     description="Update guests for a service",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="guest_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateGuests(StoreGuestRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $service->guests()->detach();
        $service->guests()->sync($request->guest_ids);
        return response()->json($service->guests()->get(), 200);
    }

    /**
     * @OA\Put(
     *     path="/api/guest-services/update-services",
     *     tags={"GuestService"},
     *     summary="Update services for a guest",
     *     description="Update services for a guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="guest_id", type="integer", example=1),
     *             @OA\Property(property="service_ids", type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function updateServices(StoreServiceRequest $request)
    {
        $guest = Guest::findOrFail($request->guest_id);
        $guest->services()->detach();
        $guest->services()->sync($request->service_ids);
        return response()->json($guest->services()->get(), 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/guest-service/{id}",
     *    summary="Delete a specific guest-service",
     *    tags={"GuestService"},
     *    description="Delete a specific guest-service by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the guest-service",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guest-Service deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Guest-Service not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $guestservice = GuestService::findOrFail($id);
        $guestservice->delete();
        return response()->json(null, 200);
    }
}
