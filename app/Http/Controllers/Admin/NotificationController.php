<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\Astro;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        $users = User::active()->whereNotNull('name')->get();
        return view('content.tables.notification.notifications', compact('users'));
    }

    public function store(Request $request)
    {
        $data              = new Notification;
        $data->title       = $request->title;
        $data->description = $request->description;
        $data->type        = $request->type;

        if ($request->type === 'user' && strtolower($request->user_id) === 'all') {
            $data->user_id = null;
        } else {
            $data->user_id = $request->user_id;
        }

        $data->save();

        $notification = new SendNotification();

        if ($request->type === 'astro') {

            $astroTokens = Astro::whereNotNull('device_id')
                ->pluck('device_id')
                ->toArray();

            if (! empty($astroTokens)) {
                // $notification->sendNotificationToTokens(
                //     $astroTokens,
                //     $request->title,
                //     $request->description,
                //     ['notification_id' => $data->id]
                // );
                foreach ($astroTokens as $astroDeviceId) {
                    dispatch(new SendNotificationJob(
                        title: $request->title,
                        message: $request->description,
                        device_id: $astroDeviceId,
                        additional_data: ['notification_id' => $data->id]
                    ))->onQueue('low');
                }
            }
        } else {

            if (strtolower($request->user_id) === 'all') {

                $tokens = User::whereNotNull('device_id')
                    ->pluck('device_id')
                    ->toArray();

                if (! empty($tokens)) {
                    // $notification->sendNotificationToTokens(
                    //     $tokens,
                    //     $request->title,
                    //     $request->description,
                    //     ['notification_id' => $data->id]
                    // );
                    foreach ($tokens as $deviceId) {
                        dispatch(new SendNotificationJob(
                            title: $request->title,
                            message: $request->description,
                            device_id: $deviceId,
                            additional_data: ['notification_id' => $data->id]
                        ))->onQueue('low');
                    }
                }
            } else {

                $user = User::find($request->user_id);
                if ($user && $user->device_id) {
                    // $notification->sendNotification(
                    //     $user->device_id,
                    //     $request->title,
                    //     $request->description,
                    //     ['notification_id' => $data->id]
                    // );
                    dispatch(new SendNotificationJob(
                        title: $request->title,
                        message: $request->description,
                        device_id: $user->device_id,
                        additional_data: ['notification_id' => $data->id]
                    ))->onQueue('low');
                }
            }
        }

        return response([
            'header'  => 'Added!',
            'message' => 'notification Added successfully',
            'table'   => 'notification-table',
        ]);
    }

    public function edit($id)
    {
        $data = Notification::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $data              = Notification::findOrFail($request->id);
        $data->title       = $request->edit_title;
        $data->description = $request->edit_description;
        $data->type        = $request->edit_type;
        $data->user_id     = $request->edit_user_id;
        $data->save();
        return response([
            'header'  => 'Updated!',
            'message' => 'notification Updated successfully',
            'table'   => 'notification-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id'     => 'required|numeric|exists:notifications,id',
            'status' => 'required|in:active,blocked',
        ]);

        Notification::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'notification status updated successfully',
            'table'   => 'notification-table',
        ]);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response([
            'header'  => 'Deleted!',
            'message' => 'notification deleted successfully',
            'table'   => 'notification-table',
        ]);
    }
}
