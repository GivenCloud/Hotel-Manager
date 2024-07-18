<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManyToMany\RoomGuest\StoreGuestRequest;
use App\Http\Requests\Room\StoreRequest;
use App\Models\Room;

/**
 * @OA\Tag(
 *   name="Room",
 *   description="API Endpoints of Room Controller"
 * )
 */

class RoomController extends Controller
{
    /**
     * @OA\Get(
     *    path="/api/room",
     *    summary="Get all rooms",
     *    tags={"Room"},
     *    description="List of rooms",
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function index()
    {
        return response()->json(Room::get(), 200);
    }

/**
 * @OA\Post(
 *    path="/api/room",
 *    summary="Create a new room",
 *    tags={"Room"},
 *    description="Create a new room with the provided details",
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"number", "type_id", "hotel_id"},
 *           @OA\Property(property="number", type="integer", example="101"),
 *           @OA\Property(property="type_id", type="integer", example="1"),
 *           @OA\Property(property="hotel_id", type="integer", example="1"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Room created successfully",
 *    ),
 *    @OA\Response(
 *        response=422,
 *        description="Validation error"
 *    )
 * )
*/
    public function store(StoreRequest $request)
    {
        return response()->json(Room::create($request->validated()), 200);
    }
    
/**
 * Display the specified room.
 *
 * @OA\Get(
 *    path="/api/room/{id}",
 *    summary="Get a specific room",
 *    tags={"Room"},
 *    description="Retrieve a specific room by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the room",
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
 *        description="Room not found"
 *    )
 * )
 */
    public function show($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room, 200);
    }

/**
 * Update an existing room.
 *
 * @OA\Put(
 *    path="/api/room/{id}",
 *    summary="Update an existing room",
 *    tags={"Room"},
 *    description="Update an existing room by ID",
 *    @OA\Parameter(
 *        name="id",
 *        in="path",
 *        required=true,
 *        description="ID of the room",
 *        @OA\Schema(
 *            type="integer",
 *            format="int64"
 *        )
 *    ),
 *    @OA\RequestBody(
 *       required=true,
 *       @OA\JsonContent(
 *           required={"number", "type_id", "hotel_id"},
 *           @OA\Property(property="number", type="integer", example="101"),
 *           @OA\Property(property="type_id", type="integer", example="3"),
 *           @OA\Property(property="hotel_id", type="integer", example="2"),
 *       )
 *    ),
 *    @OA\Response(
 *        response=200,
 *        description="Room updated successfully",
 *    ),
 *    @OA\Response(
 *        response=404,
 *        description="Room not found"
 *    )
 * )
 */
    public function update(StoreRequest $request, $id)
    {
        $room = Room::findOrFail($id);
        $room->update($request->validated());
        return response()->json($room, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/room/{id}",
     *    summary="Delete a specific room",
     *    tags={"Room"},
     *    description="Delete a specific room by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Room deleted successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Room not found"
     *    )
     * )
     */
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return response()->json(null, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/room/search/{number}",
     *    summary="Search rooms by number",
     *    tags={"Room"},
     *    description="Search rooms by number",
     *    @OA\Parameter(
     *        name="number",
     *        in="path",
     *        required=true,
     *        description="Number of the room",
     *        @OA\Schema(
     *            type="integer",
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Successful operation",
     *    )
     * )
     */
    public function search($number)
    {
        return response()->json(Room::where('number', 'like', "%$number%")->get(), 200);
    }

    /**
     * @OA\Get(
     *    path="/api/room/{id}/type",
     *    summary="Get types of a specific room",
     *    tags={"Room"},
     *    description="Get types of a specific room by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
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
     *        description="Room not found"
     *    )
     * )
     */
    public function getType($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room->type, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/room/{id}/hotels",
     *    summary="Get hotels of a specific room",
     *    tags={"Room"},
     *    description="Get hotels of a specific room by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
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
     *        description="Room not found"
     *    )
     * )
     */
    public function getHotel($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room->hotel, 200);
    }

    /**
     * @OA\Get(
     *    path="/api/room/{id}/guests",
     *    summary="Get guests of a specific room",
     *    tags={"Room"},
     *    description="Get guests of a specific room by ID",
     *    @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
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
     *        description="room not found"
     *    )
     * )
     */
    public function getGuests($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room->guests, 200);
    }

    /**
     * @OA\Post(
     *    path="/api/room/{room}/guests",
     *    summary="Add guests to a specific room",
     *    tags={"Room"},
     *    description="Add guests to a specific room by ID",
     *    @OA\Parameter(
     *        name="room",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="room_id", type="integer", example=1),
     *           @OA\Property(property="guest_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guests added successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="room not found"
     *    )
     * )
     */
    public function addGuests(Room $room, StoreGuestRequest $request)
    {
        $guestIds = $request->validated()['guest_id'] ?? [];
        $room->guests()->attach($guestIds, [
            'checkInDate' => $request->validated()['checkInDate'],
            'checkOutDate' => $request->validated()['checkOutDate'],
        ]);
        return response()->json($room->guests, 200);
    }

    /**
     * @OA\Delete(
     *    path="/api/room/{room}/guests",
     *    summary="Remove guests from a specific room",
     *    tags={"Room"},
     *    description="Remove guests from a specific room by ID",
     *    @OA\Parameter(
     *        name="room",
     *        in="path",
     *        required=true,
     *        description="ID of the room",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *    ),
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"guest_id", "room_id", "checkInDate", "checkOutDate"},
     *           @OA\Property(property="guest_id", type="integer", example=1),
     *           @OA\Property(property="room_id", type="array", @OA\Items(type="integer", example=1)),
     *           @OA\Property(property="checkInDate", type="string", format="date", example="2021-10-01"),
     *           @OA\Property(property="checkOutDate", type="string", format="date", example="2021-10-10"),
     *       )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="Guests removed successfully",
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="room not found"
     *    )
     * )
     */
    public function removeGuests(Room $room, StoreGuestRequest $request)
    {
        $guestIds = $request->validated()['guest_id'] ?? [];
        $room->guests()->detach($guestIds, [
            'checkInDate' => $request->validated()['checkInDate'],
            'checkOutDate' => $request->validated()['checkOutDate'],
        ]);
        return response()->json($room->guests, 200);
    }
}
