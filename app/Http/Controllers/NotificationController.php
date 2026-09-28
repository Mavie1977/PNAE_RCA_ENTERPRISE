<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    public function read(
        Request $request,
        DatabaseNotification $notification
    ): RedirectResponse {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
            && $notification->notifiable_type === $request->user()::class,
            403,
            'Cette notification ne vous appartient pas.'
        );

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if (
            is_string($url)
            && str_starts_with($url, url('/'))
        ) {
            return redirect()->to($url);
        }

        return back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return back()->with(
            'success',
            'Toutes les notifications ont été marquées comme lues.'
        );
    }

    public function destroy(
        Request $request,
        DatabaseNotification $notification
    ): RedirectResponse {
        abort_unless(
            $notification->notifiable_id === $request->user()->id
            && $notification->notifiable_type === $request->user()::class,
            403,
            'Cette notification ne vous appartient pas.'
        );

        $notification->delete();

        return back()->with(
            'success',
            'La notification a été supprimée.'
        );
    }
}