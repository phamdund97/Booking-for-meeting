<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Meeting;
use App\Models\Location;
use App\Http\Resources\BookingCollection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    protected $listId = 'all';
    protected $statusBooking = 0;

    /**
     * @OA\Get(
     *      path="/",
     *      tags={"booking"},
     *      summary="Get list of meeting room page and location",
     *      description="Returns list of meeting room and location",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     *     )
     */
    public function index()
    {
        $locations = Location::all();
        // return response()->json($locations, Response::HTTP_OK);
        return view('booking.index')->with('abc', $locations);
    }

    /**
     * @OA\Get(
     *      path="/location/{id}",
     *      operationId="id",
     *      tags={"booking"},
     *      summary="Get data of meeting room by locations",
     *      description="Returns meeting room data from locations",
     *      @OA\Parameter(
     *          name="id",
     *          description="Location Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     * )
     */
    public function listBooking($id)
    {
        if($id == $this->listId) {
            $data = Meeting::scopeMeetingAll()->get();
            return response()->json($data, Response::HTTP_OK);
        } else {
            $data = Meeting::scopeMeetingById($id)->get();
            if(!$data->isEmpty()) {
                return response()->json($data, Response::HTTP_OK);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => trans('booking.message_missingData'),
                ], Response::HTTP_NOT_FOUND);
            }
        } 
    }

    /**
     * @OA\Get(
     *      path="/create/{id}",
     *      operationId="id",
     *      tags={"booking"},
     *      summary="Get data of meeting room by id and show calendar of booking",
     *      description="Returns meeting room data from id and show calendar of booking",
     *      @OA\Parameter(
     *          name="id",
     *          description="Meeting Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found"
     *      )
     * )
     */
    public function create($id)
    {
        $data = Meeting::getDataByMeeting($id)->get();
        if(!$data->isEmpty()) {
            return response()->json($data, Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => false,
                'message' => trans('booking.message_missingData'),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Get(
     *      path="/listUsers",
     *      tags={"booking"},
     *      summary="Get data of list users",
     *      description="Returns users data",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function getListUsers()
    {
        $listUsers = User::all('id', 'email', 'full_name', 'image');
        return response()->json($listUsers, Response::HTTP_OK);
    }

    /**
     * @OA\POST  (
     *  path="/create/{id}",
     *  description="create a booking",
     *  tags={"booking"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Fill ",
     *      @OA\JsonContent(
     *          required={"title","time_start","time_end","description","user_id"},
     *          @OA\Property(property="title", type="string", format="text", example="Title Example"),
     *          @OA\Property(property="time_start", type="datetime", format="datetime", example="2021-06-18 15:00:00"),
     *          @OA\Property(property="time_end", type="datetime", format="datetime", example="2021-06-18 15:00:00"),
     *     @OA\Property(property="description", type="string", format="text", example="Descreption Example!"),
     *     @OA\Property(property="user_id", type="array",
 *                  @OA\Items(type="object",
 *                      @OA\Property(property="key", type="integer", example="value")
 *                  ),
 *              )
     *      ),
     *  ),
     * @OA\Response(
     *      response=200,
     *      description="Created success",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Created")
     *      )
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="Created fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Cant create")
     *      )
     *  )
     * )
     */
    public function store(Request $request, $id)
    {
        // title, time_start, time_end, participants->count(user_id)
        // description, user_id
        $data = $request->only([
            'title', 'time_start', 'time_end', 'description', 'user_id'
        ]);
        if (empty($data['title']) || empty($data['time_start']) 
        || empty($data['time_end']) || empty($data['description']) || empty($data['user_id'])) {
            return new \Exception(trans('booking.message_missingData'), Response::HTTP_NO_CONTENT);
        }
        $booking = new Booking([
            'title' => $data['title'],
            'time_start' => date('Y-m-d H:i:s', strtotime($data['time_start'])),
            'time_end' => date('Y-m-d H:i:s', strtotime($data['time_end'])),
            'description' => $data['description'],
            'user_id' => 1, //fix after
            'participants' => json_encode($data['user_id']),
            'meeting_id' => $id,
            'status' => $this->statusBooking
        ]);
        
        if(!$booking->save()){
            $responses = [
                'success' => false,
                'status' => Response::HTTP_CONFLICT,
                'message' => trans('booking.create_unsuccess')
            ];
        }else{
            $responses = [
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => trans('booking.success'),
                'data' => $booking
            ];
        }
        return response()->json($responses);
    }
}
