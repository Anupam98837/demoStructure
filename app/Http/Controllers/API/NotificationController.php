<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    private function currentUserId(Request $request): int
    {
        return (int) ($request->attributes->get('auth_tokenable_id') ?? optional($request->user())->id ?? 0);
    }

    private function decodeReceivers($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function receiverForUser(array $receivers, int $userId): ?array
    {
        foreach ($receivers as $receiver) {
            if ((int) ($receiver['id'] ?? 0) === $userId) {
                return $receiver;
            }
        }

        return null;
    }

    private function visibleNotifications(int $userId, ?int $limit = 20)
    {
        $query = DB::table('notifications')
            ->where('status', 'active')
            ->orderByDesc('created_at');

        if ($limit !== null) {
            $query->limit(max(1, min(100, $limit)));
        }

        return $query
            ->get()
            ->filter(function ($row) use ($userId) {
                return $this->receiverForUser($this->decodeReceivers($row->receivers ?? null), $userId) !== null;
            })
            ->values();
    }

    public function unreadCount(Request $request)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['success' => true, 'unread_count' => 0]);
        }

        $userId = $this->currentUserId($request);
        $count = $this->visibleNotifications($userId, null)
            ->filter(function ($row) use ($userId) {
                $receiver = $this->receiverForUser($this->decodeReceivers($row->receivers ?? null), $userId);
                return (int) ($receiver['read'] ?? 0) !== 1;
            })
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    public function drawer(Request $request)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['success' => true, 'unread_count' => 0, 'items' => []]);
        }

        $userId = $this->currentUserId($request);
        $items = $this->visibleNotifications($userId, (int) $request->query('limit', 12))
            ->map(function ($row) use ($userId) {
                $receiver = $this->receiverForUser($this->decodeReceivers($row->receivers ?? null), $userId);

                return [
                    'id' => (int) $row->id,
                    'title' => (string) ($row->title ?? 'Notification'),
                    'message' => (string) ($row->message ?? ''),
                    'type' => (string) ($row->type ?? 'general'),
                    'link_url' => (string) ($row->link_url ?? ''),
                    'created_at' => $row->created_at,
                    'is_read' => (int) ($receiver['read'] ?? 0) === 1,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'unread_count' => $items->where('is_read', false)->count(),
            'items' => $items,
        ]);
    }

    public function markRead(Request $request, int $id)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['success' => false, 'message' => 'Notifications are unavailable'], 404);
        }

        $userId = $this->currentUserId($request);
        $row = DB::table('notifications')->where('id', $id)->where('status', 'active')->first();

        if (! $row) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $receivers = $this->decodeReceivers($row->receivers ?? null);
        $updated = false;

        foreach ($receivers as &$receiver) {
            if ((int) ($receiver['id'] ?? 0) === $userId) {
                $receiver['read'] = 1;
                $updated = true;
                break;
            }
        }
        unset($receiver);

        if (! $updated) {
            return response()->json(['success' => false, 'message' => 'Notification not assigned to this user'], 403);
        }

        DB::table('notifications')->where('id', $id)->update([
            'receivers' => json_encode($receivers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['success' => true, 'updated' => 0]);
        }

        $userId = $this->currentUserId($request);
        $updated = 0;

        foreach ($this->visibleNotifications($userId, null) as $row) {
            $receivers = $this->decodeReceivers($row->receivers ?? null);
            $dirty = false;

            foreach ($receivers as &$receiver) {
                if ((int) ($receiver['id'] ?? 0) === $userId && (int) ($receiver['read'] ?? 0) !== 1) {
                    $receiver['read'] = 1;
                    $dirty = true;
                }
            }
            unset($receiver);

            if (! $dirty) {
                continue;
            }

            DB::table('notifications')->where('id', $row->id)->update([
                'receivers' => json_encode($receivers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);

            $updated++;
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }
}
