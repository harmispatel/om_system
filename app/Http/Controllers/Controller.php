<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Set Global Response
     *
     * @param Boolean $success
     * @param String $message
     * @param Array $data
     * @param Integer $status
     *
     * @return JSON
     */
    public function sendResponse($success, $message, $data = [])
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ], Response::HTTP_OK);
    }

    public function sendApiResponse($status, $total=0, $message, $data = [])
    {
        $mydata['status'] = $status;
        if($total != 0)
        {
            $mydata['total'] = $total;
        }
        $mydata['message'] = $message;
        $mydata['data'] = $data;

        return response()->json($mydata, Response::HTTP_OK);
    }
}
