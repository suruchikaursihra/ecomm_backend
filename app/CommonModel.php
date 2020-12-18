<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonModel extends Model
{
    /**
     * Generate JSON response
     *
     * @param  Request  $request
     * @return token
     */
    public function generateResponse($status, $message, $code, $data = [])
    {
        $res['status'] = $status;
        $res['responseCode'] = $code;
        $res['responseMessage'] = $message;
        if (!empty($data)) {
            $res['data'] = $data;
        }
        return $res;
    }
}
