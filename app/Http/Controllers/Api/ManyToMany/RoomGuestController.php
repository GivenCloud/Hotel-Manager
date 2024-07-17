<?php

namespace App\Http\Controllers\Api\ManyToMany;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\RoomGuest\StoreRoomRequest;
use App\Http\Requests\ManyToMany\RoomGuest\StoreGuestRequest;
use App\Models\Room;
use App\Models\ManyToMany\RoomGuest;
use App\Models\Guest;

/**
 * @OA\Tag(
 *   name="RoomGuest",
 *   description="API Endpoints of RoomGuest Controller"
 * )
 */

class RoomGuestController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/room-guest",
     *    summary="Get all room-guest",
     *    tags={"RoomGuest"},
     *    description="List of room-guest",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(RoomGuest::get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/room-guests/add-rooms",
     *     tags={"RoomGuest"},
     *     summary="Add rooms to a guest",
     *     description="Add rooms to a guest", 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="guest_id", type="integer", example=1),
     *             @OA\Property(property="room_ids", type="array",
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
    public function storeRooms(StoreRoomRequest $request)
    {
        $guest = Guest::findOrFail($request->guest_id);
        $guest->rooms()->sync($request->room_ids);
        return response()->json($guest->rooms()->get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/room-guests/add-guests",
     *     tags={"RoomGuest"},
     *     summary="Add guests to a room",
     *     description="Add guests to a room",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="room_id", type="integer", example=1),
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
        $room = Room::findOrFail($request->room_id);
        $room->guests()->sync($request->guest_ids);
        return response()->json($room->guests()->get(), 200);
    }

/**
 * Display the specified room-guest.
 *
 * @OA\Get(
 *    path="/api/room-guest/{id}",
 *    tags={"RoomGuest"},
 *    summary="Get a specific room-guest",
 *    description="Retrieve a specific roomguest by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the room-guest",
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
 *        description="Room-Guest not found"
 *    )
 * )
 */
    public function show($id)
    {
        $roomguest = RoomGuest::findOrFail($id);
        return response()->json($roomguest, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/room-guests/update-rooms",
     *     tags={"RoomGuest"},
     *     summary="Update rooms for a guest",
     *     description="Update rooms for a guest",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="guest_id", type="integer", example=1),
     *             @OA\Property(property="room_ids", type="array",
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
    public function updateRooms(StoreRoomRequest $request)
    {
        $guest = Guest::findOrFail($request->guest_id);
        $guest->rooms()->sync($request->room_ids);
        return response()->json($guest->rooms()->get(), 200);
    }

    /**
     * @OA\Put(
     *     path="/api/room-guests/update-guests",
     *     tags={"RoomGuest"},
     *     summary="Update guests for a room",
     *     description="Update guests for a room",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="room_id", type="integer", example=1),
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
        $room = Room::findOrFail($request->room_id);
        $room->guests()->sync($request->guest_ids);
        return response()->json($room->guests()->get(), 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/room-guest/{id}",
     *    summary="Delete a specific room-guest",
     *    tags={"RoomGuest"},
     *    description="Delete a specific room-guest by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room-guest",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Room-Guest deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Room-Guest not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $roomguest = RoomGuest::findOrFail($id);
        $roomguest->delete();
        return response()->json(null, 200);
    }
}
