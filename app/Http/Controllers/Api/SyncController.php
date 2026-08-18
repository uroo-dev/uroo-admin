<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bidirectional sync API used by the Flutter mobile app.
 *
 * Pull  : GET  /api/v1/sync/pull?since={ISO}  -> rows changed after since
 * Push  : POST /api/v1/sync/push              -> upsert + tombstones (LWW)
 */
class SyncController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    public function pull(Request $request): JsonResponse
    {
        $user = $request->user();
        $since = $request->query('since');
        $sinceDate = null;

        if ($since) {
            try {
                $sinceDate = Carbon::parse($since);
            } catch (\Throwable) {
                return response()->json(['message' => 'since tidak valid.'], 422);
            }
        }

        $tables = [];

        foreach (array_keys(config('sync.tables')) as $table) {
            $query = $this->scopedQuery($table, $user->id);

            if ($sinceDate) {
                $query->where('updated_at', '>', $sinceDate);
            }

            $rows = $query->get()->map(fn ($row) => $this->normalizeRow($row))->all();

            if ($rows !== []) {
                $tables[$table] = $rows;
            }
        }

        return response()->json([
            'tables' => $tables,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function push(Request $request): JsonResponse
    {
        $user = $request->user();
        $payload = $request->validate(['tables' => 'required|array']);

        $idMap = []; // temp_id (negative, phone-generated) => server id
        $created = [];

        foreach ($payload['tables'] as $table => $rows) {
            if (! config("sync.tables.$table")) {
                return response()->json(["message" => "Tabel '$table' tidak disinkronkan."], 422);
            }

            $columns = Schema::getColumnListing($table);
            $own = $this->ownerColumn($table);

            foreach (collect($rows)->sortBy('id') as $row) {
                $tempId = $row['temp_id'] ?? null;
                $incomingUpdated = isset($row['updated_at']) ? Carbon::parse($row['updated_at']) : now();
                $deletedAt = $row['deleted_at'] ?? null;
                $id = isset($row['id']) && (int) $row['id'] > 0 ? (int) $row['id'] : null;

                unset($row['temp_id']);

                if ($own !== null) {
                    $row[$own] = $user->id; // enforce ownership
                }

                if ($id) {
                    $existing = DB::table($table)->where('id', $id)->first();

                    if (! $existing || ! $this->belongsToUser($table, $existing, $user->id)) {
                        continue;
                    }

                    // Last-write-wins: skip stale rows from the phone.
                    if ($incomingUpdated->lessThan(Carbon::parse($existing->updated_at))) {
                        continue;
                    }

                    $data = $this->flattenRow(array_intersect_key($row, array_flip($columns)));
                    $data['updated_at'] = $incomingUpdated;

                    if ($deletedAt) {
                        $data['deleted_at'] = $deletedAt;
                    } else {
                        $data['deleted_at'] = null;
                    }

                    DB::table($table)->where('id', $id)->update($data);
                } elseif ($row !== []) {
                    $data = $this->flattenRow($this->remapRow($row, $idMap));

                    if (isset($data['deleted_at']) && $data['deleted_at']) {
                        continue; // never create a row that was already deleted
                    }

                    unset($data['id']);
                    $newId = DB::table($table)->insertGetId($data);

                    if ($tempId !== null) {
                        $idMap[(int) $tempId] = (int) $newId;
                        $created[$table][] = ['temp_id' => (int) $tempId, 'id' => (int) $newId];
                    }
                }
            }
        }

        return response()->json(['created' => $created]);
    }

    /**
     * Base query scoped to the given user (children resolved via their parent).
     */
    protected function scopedQuery(string $table, int $userId): Builder
    {
        $config = config("sync.tables.$table");
        $query = DB::table($table);

        if (is_string($config['user'])) {
            return $query->where($config['user'], $userId);
        }

        [$parent, $fk, $parentUserColumn] = $config['user'];

        return $query->whereIn($fk, DB::table($parent)->where($parentUserColumn, $userId)->select('id'));
    }

    /**
     * Owner column for a table, or null for child tables without their own.
     */
    protected function ownerColumn(string $table): ?string
    {
        $config = config("sync.tables.$table");

        return is_string($config['user']) ? $config['user'] : null;
    }

    protected function belongsToUser(string $table, object $row, int $userId): bool
    {
        $config = config("sync.tables.$table");

        if (is_string($config['user'])) {
            return (int) $row->{$config['user']} === $userId;
        }

        [$parent, $fk, $parentUserColumn] = $config['user'];

        return DB::table($parent)
            ->where('id', $row->{$fk})
            ->where($parentUserColumn, $userId)
            ->exists();
    }

    /**
     * Rewrite negative FK values (temp ids) to the server ids created above.
     */
    protected function remapRow(array $row, array $idMap): array
    {
        foreach (['client_id', 'invoice_id', 'goal_id'] as $fk) {
            if (isset($row[$fk]) && (int) $row[$fk] < 0 && isset($idMap[(int) $row[$fk]])) {
                $row[$fk] = $idMap[(int) $row[$fk]];
            }
        }

        return $row;
    }

    /**
     * Encode nested values (json columns) so DB::table can store them.
     */
    protected function flattenRow(array $row): array
    {
        return array_map(
            fn ($value) => is_array($value) ? json_encode($value) : $value,
            $row,
        );
    }

    /**
     * Decode json columns so the phone receives native structures.
     */
    protected function normalizeRow(object $row): array
    {
        $out = (array) $row;
        foreach ($out as $key => $value) {
            if (is_string($value) && str_starts_with($value, '[')) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $out[$key] = $decoded;
                }
            }
        }

        return $out;
    }
}