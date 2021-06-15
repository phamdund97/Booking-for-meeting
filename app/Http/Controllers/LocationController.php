<?php


namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LocationController
{
    public function __construct()
    {
    }

    /**
     * @OA\GET  (
     *  path="/location/",
     *  description="get meeting data",
     *  operationId="meeting data",
     *  tags={"location"},
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
    public function index(){
        $locations = Location::all();
        if (count($locations) == 0){
            return \response()->json(['message'=>'Không có địa điểm nào!','status' => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }
        return \response()->json(['data'=> $locations,'status' => Response::HTTP_OK],Response::HTTP_OK);
    }

}
