<?php

namespace App\Http\Controllers\API\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait PersistsAdminNotifications
{
    protected function persistNotification(array $payload): void
    {
        try {
            if (!Schema::hasTable('notifications')) {
                return;
            }

            $title = (string) ($payload['title'] ?? 'Notification');
            $message = (string) ($payload['message'] ?? '');
            $receivers = array_values(array_map(function ($receiver) {
                return [
                    'id'   => isset($receiver['id']) ? (int) $receiver['id'] : null,
                    'role' => (string) ($receiver['role'] ?? 'unknown'),
                    'read' => (int) ($receiver['read'] ?? 0),
                ];
            }, $payload['receivers'] ?? []));

            if (empty($receivers)) {
                return;
            }

            $metadata = $payload['metadata'] ?? null;
            $type = (string) ($payload['type'] ?? 'general');
            $linkUrl = $payload['link_url'] ?? null;
            $priority = in_array(($payload['priority'] ?? 'normal'), ['low', 'normal', 'high', 'urgent'], true)
                ? (string) $payload['priority']
                : 'normal';
            $status = in_array(($payload['status'] ?? 'active'), ['active', 'archived', 'deleted'], true)
                ? (string) $payload['status']
                : 'active';

            DB::table('notifications')->insert([
                'title'      => $title,
                'message'    => $message,
                'receivers'  => json_encode($receivers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'metadata'   => $metadata !== null
                    ? json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'type'       => $type,
                'link_url'   => $linkUrl,
                'priority'   => $priority,
                'status'     => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Notifications must never break the primary API flow.
        }
    }

    protected function adminNotificationReceivers(array $excludeIds = []): array
    {
        try {
            if (!Schema::hasTable('users')) {
                return [];
            }

            $excluded = array_flip(array_map('intval', $excludeIds));

            $query = DB::table('users')
                ->select('id', 'role', 'status')
                ->whereIn('role', ['admin', 'hr']);

            if (Schema::hasColumn('users', 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            if (Schema::hasColumn('users', 'status')) {
                $query->where(function ($q) {
                    $q->where('status', 'active')
                        ->orWhere('status', 'Active');
                });
            }

            return $query
                ->get()
                ->filter(function ($row) use ($excluded) {
                    return !isset($excluded[(int) $row->id]);
                })
                ->map(function ($row) {
                    return [
                        'id'   => (int) $row->id,
                        'role' => (string) ($row->role ?? 'admin'),
                        'read' => 0,
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function notifyAdmins(
        string $title,
        string $message,
        array $metadata = [],
        ?string $linkUrl = null,
        string $type = 'general',
        string $priority = 'normal',
        array $excludeIds = []
    ): void {
        $receivers = $this->adminNotificationReceivers($excludeIds);
        if (empty($receivers)) {
            return;
        }

        $this->persistNotification([
            'title'     => $title,
            'message'   => $message,
            'receivers' => $receivers,
            'metadata'  => $metadata,
            'link_url'  => $linkUrl,
            'type'      => $type,
            'priority'  => $priority,
            'status'    => 'active',
        ]);
    }
}
