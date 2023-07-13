<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class forgotController extends Controller
{
     public function forgot_password(Request $request)
     {
     $input = $request->all();
     $rules = array(
     'email' => "required|email",
     );
     $validator = Validator::make($input, $rules);
     if ($validator->fails()) {
     $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
     } else {

     try {
     $response = Password::sendResetLink($request->only('email'), function (Message $message) {
     $message->subject($this->getEmailSubject());
     });

     switch ($response) {
     case Password::RESET_LINK_SENT:
     return Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
     case Password::INVALID_USER:
     return Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
     }
     } catch (Swift_TransportException $ex) {
     $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
     } catch (Exception $ex) {
     $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
     }
     }
     return Response::json($arr);
     }
}
