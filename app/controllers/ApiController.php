<?php


class ApiController extends Controller
{

    public function tokens()
    {
        $token = Input::get('token');

        if ( ! empty($token)) {
            $device = new Device;
            $device->token = $token;
            $device->save();
        }

        $data = [
            'status' => 'ok',
            'token'  => $token
        ];

        return Response::json($token);
    }

}