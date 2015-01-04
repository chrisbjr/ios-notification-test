<?php

use Sly\NotificationPusher\Adapter\Apns;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device as AppleDevice;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

class ApiController extends Controller
{

    public function devices()
    {
        $token = Input::get('token');

        if ( ! empty($token)) {
            $device = new Device;
            $device->token = $token;
            $device->save();

            $data = [
                'status' => 'ok',
                'token'  => $token
            ];

            // send the apn
            $pushManager = new PushManager(PushManager::ENVIRONMENT_PROD);

            // Then declare an adapter.
            $apnsAdapter = new Apns(array(
                'certificate' => storage_path('ssl/ck.pem'),
                'passPhrase' => '1234',
            ));

            // Set the device(s) to push the notification to.
            $devices = new DeviceCollection(array(
                new AppleDevice($token),
            ));

            // Then, create the push skel.
            $message = new Message('Nice one Rhenz! :)');

            // Finally, create and add the push to the manager, and push it!
            $push = new Push($apnsAdapter, $devices, $message);
            $pushManager->add($push);
            $pushManager->push();

            return Response::json($data);
        }

        return Response::json(['status' => 'error']);
    }

    public function sendMessage()
    {
        $deviceId = Input::get('device_id');

        if ( ! empty($deviceId)) {
            // send message
            $device = Device::where('id', '=', $deviceId)->first();

            if (empty($device) || $device->exists == false) {
                return Response::json(['status' => 'error']);
            }

            // send the apn
            $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

            // Then declare an adapter.
            $apnsAdapter = new Apns(array(
                'certificate' => storage_path('ssl/ck.pem'),
            ));

            // Set the device(s) to push the notification to.
            $devices = new DeviceCollection(array(
                new AppleDevice($device->token),
            ));

            // Then, create the push skel.
            $message = new Message('This is a basic example of push.');

            // Finally, create and add the push to the manager, and push it!
            $push = new Push($apnsAdapter, $devices, $message);
            $pushManager->add($push);
            $pushManager->push();

        }

        return Response::json(['status' => 'error']);
    }

}