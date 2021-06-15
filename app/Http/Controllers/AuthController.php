<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Http\Requests\LogoutRequest;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware(['api', 'auth', 'jwt.auth'], [
            'except' => [
                'login',
            ]
        ]);

    }

    /**
     * @OA\POST  (
     *  path="/auth/login",
     *  description="Login by email ttc, password",
     *  operationId="authLogin",
     *  tags={"auth"},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Pass user credentials",
     *      @OA\JsonContent(
     *          required={"email","password"},
     *          @OA\Property(property="email", type="string", format="email", example="dat@gmail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="12345678"),
     *      ),
     *  ),
     *  @OA\Response(
     *      response=401,
     *      description="Login fail",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Unauthorized")
     *      )
     *  )
     * )
     */

    public function login(Request $request)
    {

        // validate zone
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:80|email',
            'password' => 'required|min:8|max:50'
        ], $message = [
            'email.required' => trans('auth.email_required'),
            'email.max' => trans('auth.email_max'),
            'email.email' => trans('auth.email_email'),
            'password.required' => trans('auth.password_required'),
            'password.min' => trans('auth.password_min'),
            'password.max' => trans('auth.password_max'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $validated = $validator->validated();

        if (!$token = $this->guard()->attempt($validated)) {
            return response()->json([
                'message' => 'Email không tồn tại hoặc mật khẩu chưa chính xác',
                'status' => Response::HTTP_BAD_REQUEST,
            ], Response::HTTP_BAD_REQUEST);
        }
        $token_new = new Token([
            'user_id' => $this->guard()->id(),
            'token' => $token,
            'hash_token' => md5($token),
            'type' => 1
        ]);

        if (Token::where('user_id', $this->guard()->id())->count() == 0) {
            $token_new->save();
        }

        Token::where('user_id', $this->guard()->id())->update([
            'token' => $token,
            'hash_token' => md5($token),
        ]);
        return response()->json([
            'message' => 'Login success',
            'status' => Response::HTTP_OK,
            'data' => $this->respondWithToken($token)
        ], Response::HTTP_OK)->withCookie('token',$token,100);
    }

    /**
     * @OA\POST  (
     *     path="/auth/change",
     *     description="change password by email ttc, new password",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass user credentials",
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="password", type="string", format="password", example="12345678"),
     *              @OA\Property(property="new_password", type="string", format="newpassword", example="123456789"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Change password success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="")
     *          )
     *      )
     *   )
     */

    public function changePassword(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'password' => 'required|min:8|max:50',
            'newpassword' => 'required|min:8|max:50',
        ], $message = [
            'password.required' => trans('auth.password_required'),
            'password.min' => trans('auth.password_min'),
            'password.max' => trans('auth.password_max'),
            'newpassword.required' => trans('auth.password_required'),
            'newpassword.min' => trans('auth.password_min'),
            'newrepassword.max' => trans('auth.password_max'),
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => $validated->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!Hash::check($request->password, $this->guard()->user()->password) || $request->password == $request->newpassword){
            return response()->json([
                'message' => 'Password fails',
                'status' => Response::HTTP_BAD_REQUEST,
            ],Response::HTTP_BAD_REQUEST);
        }
        $new_password = Hash::make($request->newpassword);
        User::where('id', $this->guard()->id())->update([
                'password' => $new_password,
            ]);
        return response()->json([
            'message' => 'Change password success',
            'success' => true,
            'status' => Response::HTTP_OK,
        ], Response::HTTP_OK);
    }


    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ];
    }

    /**
     * @OA\Get   (
     *  path="/auth/logout",
     *  description="Logout user",
     *  operationId="authLogout",
     *  tags={"auth"},
     *  @OA\RequestBody(
     *      required=true,
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="Logout success",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Logout success")
     *      )
     *  )
     * )
     */
    public function logout(Request $request)
    {
        $token = $request->cookie('token');
        $this->guard()->logout();
        $a = Token::where('hash_token', md5($token))->get()->first();
        Token::destroy($a->id);
        return response()->json([
            'message' => 'Successfully logged out',
            'status' => Response::HTTP_OK
        ],Response::HTTP_OK)->withCookie('token',$request->cookie('token'),-1);
    }


    public function refresh()
    {
        return response()->json($this->respondWithToken($this->guard()->refresh()));
    }

    public function guard()
    {
        return Auth::guard();
    }

}
