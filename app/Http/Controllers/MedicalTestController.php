<?php

namespace App\Http\Controllers;

use App\CommonModel;
use App\medicalTest;
use App\User;
use Illuminate\Http\Request;

class MedicalTestController extends Controller
{
    /**
     * get Medical test list from db
     *
     * @param  Request  $request
     * @return MedicalTest json
     */
    public function getTestsList(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                $getList = medicalTest::orderByRaw("FIELD(popular , 'TRUE', 'false') ASC")->get();
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.DATA_SUCCESS_MESSAGE'),
                    config('apiconstants.success_code'), $getList);
            } else {
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.failure'),
                    config('apiconstants.error_message.UNAUTHORIZE_USER_ERROR_MESSAGE'),
                    config('apiconstants.error_code.UNAUTHORIZE_USER_ERROR_CODE'));
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
     * Search from medical Tests
     *
     * @param  Request  $request
     * @return MedicalTests json
     */
    public function searchTestsList(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                if ($request->serach == '') {
                    $getList = medicalTest::get();
                }
                $getList = medicalTest::where('Keyword', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orderByRaw("FIELD(popular , 'TRUE', 'false') ASC")->get();

                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.DATA_SUCCESS_MESSAGE'),
                    config('apiconstants.success_code'), $getList);
            } else {
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.failure'),
                    config('apiconstants.error_message.UNAUTHORIZE_USER_ERROR_MESSAGE'),
                    config('apiconstants.error_code.UNAUTHORIZE_USER_ERROR_CODE'));
            }

        } catch (Exception $e) {
            $res = $commonModel->generateResponse(
                config('apiconstants.status.failure'),
                config('apiconstants.error_message.SOMETHING_WENT_WRONG_ERROR_MESSAGE'),
                config('apiconstants.error_code.SOMETHING_WENT_WRONG_ERROR_CODE'));
        }
        return $res;
    }

}
