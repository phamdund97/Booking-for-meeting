<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UploadUserImageRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\StorageImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{

    use StorageImageTrait;
    private $user;

    /**
     * UserController constructor.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @OA\Get   (
     *  path="/users",
     *  description="get all user",
     *  operationId="createUser",
     *  tags={"user"},
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *  @OA\RequestBody(
     *      required=true,
     *      description="filter data",
     *      @OA\JsonContent(
     *          required={"name","sort", "order", "page", "limit"},
     *          @OA\Property(property="name", type="string", format="email", example="Nam"),
     *          @OA\Property(property="sort", type="string", format="password", example="name"),
     *          @OA\Property(property="order", type="string", format="full_name", example="desc"),
     *          @OA\Property(property="page", type="number", format="department_id", example="1"),
     *          @OA\Property(property="limit", type="string", format="phone", example="10"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="list users",
     *      @OA\JsonContent(
     *          @OA\Property(property="status", type="number", example="200")
     *      )
     *  )
     * )
     */
    public function index(Request $request)
    {
        $filterName = $request->input('name');
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);
        $sort = $request->input('sort');
        $order = $request->input('order');

        $filterData = [
            'filter_name' => $filterName,
            'sort'        => $sort,
            'order'       => $order,
            'limit'       => intval(trim($limit)),
            'page'        => intval(trim($page)),
        ];

        $results = $this->user->getUsers($filterData);

        $data = $links = $metaData = [];

        if ($results) {
            $data = UserResource::collection($results);

            $links = [
                'first' => $request->fullUrlWithQuery(['page' => 1]),
                'last'  => $results->lastPage(),
                'next'  => $results->nextPageUrl(),
                'prev'  => $results->previousPageUrl(),
                'self'  => $request->fullUrlWithQuery(['page' => $page])
            ];

            $metaData = [
                'per_page'     => $results->perPage(),
                'total_count'  => $results->total(),
                'page_count'   => $results->lastPage(),
                'current_page' => $results->currentPage(),
                'next_page'    => ($results->lastPage() > $results->currentPage()) ? ($results->currentPage() + 1) : null,
            ];
        }

        $json = [
            'status' => Response::HTTP_OK,
            'data'   => $data,
            'links'  => $links,
            'meta'   => $metaData
        ];

        return response()->json($json, $json['status']);
    }


    /**
     * @OA\POST  (
     *  path="/user",
     *  description="creaate new user",
     *  operationId="createUser",
     *  tags={"user"},
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *  @OA\RequestBody(
     *      required=true,
     *      description="user data insert",
     *      @OA\JsonContent(
     *          required={"email","password", "full_name", "department_id", "phone", "role_id"},
     *          @OA\Property(property="email", type="string", format="email", example="nht@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="12345678"),
     *          @OA\Property(property="full_name", type="string", format="full_name", example="Nguyen Van A"),
     *          @OA\Property(property="department_id", type="number", format="department_id", example="1"),
     *          @OA\Property(property="phone", type="string", format="phone", example="0899256783"),
     *          @OA\Property(property="role_id", type="number", format="role_id", example="1"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=201,
     *      description="created user",
     *      @OA\JsonContent(
     *          @OA\Property(property="status", type="number", example="201")
     *      )
     *  )
     * )
     */
    public function store(CreateUserRequest $request)
    {
        $request->validated();

        $dataInsert = [
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'full_name'     => $request->input('full_name'),
            'department_id' => $request->input('department'),
            'phone_number'  => $request->input('phone'),
            'role_id'       => $request->input('role'),
        ];

        if ($request->hasFile('image')) {
            $name = 'image' . '_' . Str::slug($dataInsert['full_name']);
            $image = $this->storageTraitUpload(
                $request->file('image'),
                'user',
                $name
            );

            $dataInsert['image'] = $image['file_path'];
        }

        try {
            $this->user->create($dataInsert);
        } catch (\Exception $e) {
            $isCreated = false;
            Log::error('message: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line : ' . $e->getLine());
        }

        if (isset($isCreated)) {
            $json = [
                'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => trans('user.serve_error')
            ];
        } else {
            $json = [
                'status'  => Response::HTTP_CREATED,
                'message' => trans('user.user_created')
            ];
        }

        return response()->json($json, $json['status']);

    }

    /**
     * @OA\Post(
     *     path="/user/{userId}/uploadImage",
     *     tags={"user"},
     *     summary="uploads an image",
     *     operationId="uploadFile",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="ID of user to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="number", example="201")
     *          )
     *     ),
     *     @OA\RequestBody(
     *         description="Upload images request body",
     *         @OA\MediaType(
     *             mediaType="application/form-data",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     )
     * )
     */
    public function uploadImage(UploadUserImageRequest $request, $id)
    {
        $request->validated();

        $name = 'image' . '_' . Str::slug(auth()->user()->full_name) . '.' . $ext;
        $imagePath = $this->storageTraitUpload($request->file('image'), 'user', $name);

        try {
            $user = $this->user->find($id);
            $isUpdated = $user->update([
                'image' => $imagePath['file_path'],
            ]);
        } catch (\Exception $e) {
            Log::error('message: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line : ' . $e->getLine());
        }

        if ($isUpdated) {
            $json = [
                'status'  => Response::HTTP_OK,
                'message' => trans('user.user_image_upload')
            ];
        } else {
            $json = [
                'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => trans('user.serve_error')
            ];
        }

        return response()->json($json, $json['status']);
    }

    /**
     * @OA\Delete(
     *     path="/user/delete/{userId}",
     *     tags={"user"},
     *     summary="Deletes a user",
     *     operationId="deleteUser",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User is deleted",
     *      @OA\JsonContent(
     *          @OA\Property(property="status", type="number", example="200")
     *      )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *      @OA\JsonContent(
     *          @OA\Property(property="status", type="number", example="404")
     *      )
     *     ),
     * )
     */
    public function destroy(Request $request, $id)
    {
        $user = $this->user->find($id);

        if (!$user) {
            return response()->json([
                'status'  => Response::HTTP_NOT_FOUND,
                'message' => trans('user.user_not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->id === auth()->user()->getAuthIdentifier()) {
            return response()->json([
                'status'  => Response::HTTP_CONFLICT,
                'message' => trans('user.user_conflict')
            ], Response::HTTP_CONFLICT);
        }

        try {
            $isDeleted = $user->delete();
        } catch (\Exception $e) {
            Log::error('message: ' . $e->getMessage() . ' File: ' . $e->getFile() . ' Line : ' . $e->getLine());
        }

        if ($isDeleted) {
            $json = [
                'status'  => Response::HTTP_OK,
                'message' => trans('user.user_deleted')
            ];
        } else {
            $json = [
                'status'  => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => trans('user.serve_error')
            ];
        }

        return response()->json($json, $json['status']);
    }
}
