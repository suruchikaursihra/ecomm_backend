<?php

namespace App\Http\Controllers;

use App\CommonModel;
use App\orderDetails;
use App\orderItems;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class CustomerController extends Controller
{
    /**
     * Save Order Details
     *
     * @param  Request  $request
     * @return Order Json
     */
    public function saveOrderDetails(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                $saveOrder = orderDetails::insert([
                    'customer_id' => $request->user_id,
                    'order_id' => uniqid('ODR_'),
                    'total_amount' => $request->paymentAmount,
                    'created_at' => now(),
                ]);
                if ($saveOrder) {
                    $getOrder = orderDetails::select('order_id')->where('customer_id', $request->user_id)->latest('created_at')->first();
                    foreach ($request->items as $value) {
                        $saveOrderItems = orderItems::insert([
                            'item_id' => $value['itemId'],
                            'order_id' => $getOrder->order_id,
                            'min_price' => $value['minPrice'],
                        ]);
                    }

                    $getOrderList = orderDetails::join('order_items', 'order_items.order_id', '=', 'order_details.order_id')
                        ->where('customer_id', $request->user_id)->where('order_items.order_id', $getOrder->order_id)->get();
                    $data = [
                        'order_items' => $getOrderList,
                        'order_id' => $getOrder->order_id,
                        'amount' => $request->paymentAmount,
                    ];

                    $res = $commonModel->generateResponse(
                        config('apiconstants.status.success'),
                        config('apiconstants.success_message.ORDER_PLACED_SUCCESS_MESSAGE'),
                        config('apiconstants.success_code'), $data);

                }
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
     * Save Cart data to redis(cache)
     *
     * @param  Request  $request
     * @return
     */
    public function saveToRedis(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                $redis = Redis::connection();
                $getRedis = $redis->get('cartItems');
                $cartItems = [];
                if ($getRedis) {
                    $cartItems = json_decode($getRedis, true);
                    $cartItems[] = $request->items;
                    $cartItems = $cartItems;
                } else {
                    $cartItems[] = $request->items;
                }

                $redis->set('cartItems', json_encode($cartItems));

                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.CART_UPDATED_SUCESSS_MESSGAE'),
                    config('apiconstants.success_code'));

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
     * Delete cart Items from Redis
     *
     * @param  Request  $request
     * @return
     */
    public function removeFromRedis(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                $redis = Redis::connection();
                $getRedis = $redis->get('cartItems');
                $cartItems = [];
                if ($getRedis) {
                    $cartItems = json_decode($getRedis, true);
                    foreach ($cartItems as $key => $object) {
                        if ($object['id'] === $request->item['id']) {
                            unset($cartItems[$key]);
                        }
                    }
                    $redis->set('cartItems', json_encode($cartItems));
                }

                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.CART_UPDATED_SUCESSS_MESSGAE'),
                    config('apiconstants.success_code'));

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
     * Reset Redis after sucessfull order
     *
     * @param  Request  $request
     * @return
     */
    public function resetRedis(Request $request)
    {
        try {
            $commonModel = (new CommonModel());
            $checkUser = User::where('token', $request->token)->where('id', $request->user_id)->first();
            if ($checkUser) {
                $redis = Redis::connection();
                $resetRedis = Redis::del('cartItems');
                $res = $commonModel->generateResponse(
                    config('apiconstants.status.success'),
                    config('apiconstants.success_message.CART_RESET_SUCCESS_MESSAGE'),
                    config('apiconstants.success_code'));

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
