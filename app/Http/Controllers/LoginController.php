<?php

namespace App\Http\Controllers;

use App\CommonModel;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    /**
     * User login with email and password
     *
     * @param  Request  $request
     * @return user Data
     */
    public function login(Request $request)
    {
        try {
            $commonModel = (new CommonModel);
            $getDetails = User::where('email', $request->email)->where('password', $request->password)->first();
            if ($getDetails) {
                $token = md5(uniqid(rand(), true));
                $updateToken = User::where('email', $request->email)->where('password', $request->password)
                    ->update(['token' => $token]);
                $redis = Redis::connection();
                $getRedis = $redis->get('cartItems');
                if ($getRedis) {
                    $uniqueCart = $this->unique_multidimensional_array(json_decode($getRedis), 'id');
                } else {
                    $uniqueCart = [];
                }
                $data = ['token' => $token, 'user_id' => $getDetails->id, 'cartItems' => $uniqueCart];
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.DATA_SUCCESS_MESSAGE'),
                    config('apiconstants.success_code'), $data);
            } else {
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.failure'),
                    config('apiconstants.error_message.CUSTOMER_NOT_FOUND_ERROR_MESSAGE'),
                    config('apiconstants.error_code.CUSTOMER_NOT_FOUND_ERROR_CODE'));
            }
        } catch (Exception $e) {
            $res = $commonModel->generateResponse(
                config('apiconstants.status.failure'),
                config('apiconstants.error_message.SOMETHING_WENT_WRONG_ERROR_MESSAGE'),
                config('apiconstants.error_code.SOMETHING_WENT_WRONG_ERROR_CODE'));
        }

        return $res;
    }

    /**
     * Remove duplicate objects from array
     *
     * @param  array,key  $array, $key
     * @return array
     */
    private function unique_multidimensional_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val->$key, $key_array)) {
                $key_array[$i] = $val->$key;
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}
