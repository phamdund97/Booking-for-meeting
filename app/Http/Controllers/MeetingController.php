<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\MeetingResource;

class MeetingController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * @OA\GET  (
     *  path="/meeting/",
     *  description="get meeting data",
     *  operationId="meeting data",
     *  tags={"meeting"},
     *
     *  @OA\Response(
     *      response=401,
     *      description="Get data fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Cant get data")
     *      )
     *  )
     * )
     */

    public function index()
    {
        $meeting = Meeting::all();
        if (count($meeting) == 0) {
            return response()->json([
                'message' => 'Không có phòng họp nào!',
                'status' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        return response([
            'message' => 'Hiển thị thành công',
            'status' => Response::HTTP_OK,
            'data' => MeetingResource::collection($meeting),
        ], Response::HTTP_OK);

    }

    /**
     * @OA\POST  (
     *  path="/meeting/create",
     *  description="create a meeting",
     *  operationId="meetingCreate",
     *  tags={"meeting"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Fill ",
     *     @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 allOf={
     *                     @OA\Schema(ref="#components/schemas/item"),
     *                     @OA\Schema(
     *                         @OA\Property(
     *                             description="Item image",
     *                             property="item_images[]",
     *                             type="array",
     *                             @OA\Items(type="string", format="binary")
     *                         )
     *                     )
     *                 }
     *             )
     *         ),
     *      @OA\JsonContent(
     *          required={"name","capacity","location_id","description","image","status"},
     *          @OA\Property(property="name", type="string", format="text", example="nam@gmail.com"),
     *          @OA\Property(property="capacity", type="number", format="number", example="12"),
     *          @OA\Property(property="location_id", type="number", format="number", example="2"),
     *          @OA\Property(property="status", type="number", format="number", example="1"),
     *          @OA\Property(property="description", type="string", format="text", example="This is the best room i have booked! awesome!"),
     *      ),
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
    public function createMeeting(Request $request)
    {
        //validate zone
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|min:1',
            'capacity' => 'required|numeric|max:100',
            'description' => 'required',
            'location_id' => 'required',
            'image' => 'required|mimes:png,svg,gif,jpg,jpeg',
//            'status' => 'required|numeric'
        ],

            $message = [
                'name.required' => trans('meeting.name_required'),
                'name.max' => trans('meeting.name_max'),
                'name.min' => trans('meeting.nam_min'),
                'capacity.required' => trans('meeting.capacity_required'),
                'capacity.numeric' => trans('meeting.capacity_numeric'),
                'capacity.max' => trans('meeting.capacity_max'),
                'description.required' => trans('meeting.description_required'),
                'location_id.required' => trans('meeting.location_id_required'),
                'image.required' => trans('meeting.image_required'),
                'image.mimes' => trans('meeting.image_mimes'),
                'image.max' => trans('meeting.image_max'),
                'image.image' => trans('meeting.image_image'),
//            'status.required' => trans('meeting.status_required')
            ]);
// end of validate zone
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $appendImg = $request->file('image');
        $saveImage = $appendImg->store('images', 'public');
        Meeting::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'location_id' => $request->location_id,
            'image' => $saveImage,
//           'status' => $request->status
        ]);
        return response()->json([
            'message' => 'Thêm mới thành công',
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);

    }

    /**
     * @OA\GET  (
     *  path="/meeting/show/{id}",
     *  description="show meeting",
     *  operationId="meetingData",
     *  tags={"meeting"},
     *
     *  @OA\Response(
     *      response=401,
     *      description="Get data fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Cant get data")
     *      )
     *  )
     * )
     */
    public function showMeeting($id)
    {
        $currentMeeting = Meeting::find($id);

        if (!$currentMeeting) {
            return response()->json([
                'message' => 'Không tìm thấy phòng họp này!',
                'status' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);

        }

        $data = [
            'id' => $currentMeeting->id,
            'name' => $currentMeeting->name,
            'capacity' => $currentMeeting->capacity,
            'location' => $currentMeeting->getLocation->name,
            'description' => $currentMeeting->description,
            'status' => $currentMeeting->status,
            'image' => $currentMeeting->image
        ];

        return response()->json(['data' => $data, 'status' => Response::HTTP_OK], Response::HTTP_OK);

    }

    /**
     * @OA\Put   (
     *  path="/meeting/update/{id}",
     *  description="Update meeting with id",
     *  operationId="meetingUpdate",
     *  tags={"meeting"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="",
     *      @OA\JsonContent(
     *          required={"name","capacity","description","image","status","location_id"},
     *          @OA\Property(property="name", type="string", format="text", example="phong 1"),
     *          @OA\Property(property="capacity", type="number", format="number", example="18"),
     *          @OA\Property(property="description", type="string", format="text", example="dssfdfhvjbk"),
     *          @OA\Property(property="image", type="string", format="string", example="image.jpg"),
     *          @OA\Property(property="status", type="number", format="string", example="1"),
     *          @OA\Property(property="location_id", type="number", format="string", example="1"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="Update fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Unauthorized")
     *      ),
     *  )
     * )
     */
    public function updateMeeting(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:1',
            'capacity' => 'required|numeric|max:100',
            'description' => 'required',
            'location_id' => 'required',
            'image' => 'required|mimes:png,svg,gif,jpg,jpeg|max:15360|image',
        ],
            $message = [
                'name.required' => trans('meeting.name_required'),
                'name.max' => trans('meeting.name_max'),
                'name.min' => trans('meeting.nam_min'),
                'capacity.required' => trans('meeting.capacity_required'),
                'capacity.numeric' => trans('meeting.capacity_numeric'),
                'capacity.max' => trans('meeting.capacity_max'),
                'description.required' => trans('meeting.description_required'),
                'location_id.required' => trans('meeting.location_id_required'),
                'image.required' => trans('meeting.image_required'),
                'image.mimes' => trans('meeting.image_mimes'),
                'image.max' => trans('meeting.image_max'),
                'image.image' => trans('meeting.image_image'),
            ]);
        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors(),
                'error' => $validate->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $up = Meeting::find($id);
        if (empty($up)) {
            return response()->json([
                'message' => 'Not Found',
                'error' => [

                ],
                'status' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        if (!empty($up['image']) && File::exists($up['image'])) {
            Storage::delete('public/images/' . $up['image']);
        }
        $image = $request->file('image');
        $name_image = time() . "_" . $image->getClientOriginalName();
//        Storage::put('public/images', $name_image);
        Meeting::where('id', $id)->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'image' => $name_image,
            'location_id' => $request->location_id,
        ]);
        return response()->json([
            'message' => 'Update success',
            'status' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Delete (
     *  path="/meeting/delete/{id}",
     *  description="Delete meeting with id",
     *  operationId="meetingDelete",
     *  tags={"meeting"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="",
     *      @OA\JsonContent(
     *          required={},
     *      ),
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="Delete fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Unauthorized")
     *      ),
     *  )
     * )
     */
    public function deleteMeeting($id)
    {
        if (!is_numeric($id)) {
            return response()->json([
                'message' => 'ID is not Number',
                'status' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }
        $find = Meeting::find($id);
        if (empty($find)) {
            return response()->json([
                'message' => 'Not Found',
                'status' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        if (!empty($find) && File::exists($find['image'])) {
            unlink($find['image']);
        }
        Meeting::destroy($id);
        return response()->json([
            'message' => 'Delete success',
            'status' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }
}
