<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\User;


class BaseController extends Controller
{
    protected $token = 'd1k6Lpr2nxEa0R96jCSI5xxUjNkJOLFo2vGllglbqZ1MTHFNunB5b8wfy2pc';
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
      $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
      $response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }


    protected function checkToken($token)
    {
        $row = User::where([
          ['api_token', $token],
          ['role','<>', 'user']
        ])->get();
        if(count($row)){
            return true;
        }
        else{
            return false;
        }
    }
}