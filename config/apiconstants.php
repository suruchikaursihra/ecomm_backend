<?php

return [
    'status' => [
        'success' => 'success',
        'failure' => 'failure',
    ],
    'success_code' => 200,
    'success_message' => [
        'DATA_SUCCESS_MESSAGE' => 'Data fetched successfully',
        'ORDER_PLACED_SUCCESS_MESSAGE' => 'Order Placed Succesfully!',
        'CART_UPDATED_SUCESSS_MESSGAE' => 'Cart Updated Succesfully!',
        'CART_RESET_SUCCESS_MESSAGE' => 'Cart Reset Sucessfully!',
    ],

    'error_code' => [
        'PARAMS_MISSING_ERROR_CODE' => 409,
        'SOMETHING_WENT_WRONG_ERROR_CODE' => 500,
        'CUSTOMER_NOT_FOUND_ERROR_CODE' => 404,
        'UNAUTHORIZE_USER_ERROR_CODE' => 401,
    ],

    'error_message' => [
        'SOMETHING_WENT_WRONG_ERROR_MESSAGE' => 'Something went wrong. Please try again.',
        'CUSTOMER_NOT_FOUND_ERROR_MESSAGE' => 'Customer Not found !',
        'UNAUTHORIZE_USER_ERROR_MESSAGE' => 'User not Authenticated !',
        'CUSTOMER_ALREADY_EXISTS' => 'Customer Already Exits',
    ],
];
