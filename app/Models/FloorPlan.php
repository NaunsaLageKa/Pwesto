<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'hub_owner_id',
        'name',
        'layout_data',
        'description',
        'is_active',
    ];

    protected $casts = [
        'layout_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function hubOwner()
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }

    /**
     * Normalize legacy flat item arrays and v2 multi-floor payloads.
     *
     * @return array{version: int, floors: list<array{id: int, name: string, items: array}>}
     */
    public static function normalizeLayoutPayload(mixed $layoutData): array
    {
        if (! is_array($layoutData)) {
            return self::emptyMultiFloorPayload();
        }

        if (isset($layoutData['version'], $layoutData['floors']) && is_array($layoutData['floors'])) {
            $floors = [];
            foreach ($layoutData['floors'] as $i => $floor) {
                if (! is_array($floor)) {
                    continue;
                }
                $floors[] = [
                    'id' => (int) ($floor['id'] ?? ($i + 1)),
                    'name' => (string) ($floor['name'] ?? self::defaultFloorName($i + 1)),
                    'items' => is_array($floor['items'] ?? null) ? $floor['items'] : [],
                ];
            }

            if ($floors !== []) {
                return ['version' => 2, 'floors' => $floors];
            }

            return self::emptyMultiFloorPayload();
        }

        if ($layoutData === [] || array_is_list($layoutData)) {
            return [
                'version' => 2,
                'floors' => [
                    [
                        'id' => 1,
                        'name' => '1st Floor',
                        'items' => $layoutData,
                    ],
                ],
            ];
        }

        return self::emptyMultiFloorPayload();
    }

    /**
     * @return list<array{id: int, name: string, items: array}>
     */
    public function floorsList(): array
    {
        return self::normalizeLayoutPayload($this->layout_data)['floors'];
    }

    public function layoutItemsForFloor(?int $floorId = null): array
    {
        return self::itemsForFloor($this->layout_data, $floorId);
    }

    /**
     * @return list<array>
     */
    public static function itemsForFloor(mixed $layoutData, ?int $floorId = null): array
    {
        $normalized = self::normalizeLayoutPayload($layoutData);

        if ($floorId !== null) {
            foreach ($normalized['floors'] as $floor) {
                if ((int) $floor['id'] === (int) $floorId) {
                    return $floor['items'];
                }
            }
        }

        return $normalized['floors'][0]['items'] ?? [];
    }

    public static function defaultFloorName(int $number): string
    {
        $suffix = match ($number % 100) {
            11, 12, 13 => 'th',
            default => match ($number % 10) {
                1 => 'st',
                2 => 'nd',
                3 => 'rd',
                default => 'th',
            },
        };

        return $number . $suffix . ' Floor';
    }

    /**
     * @return array{version: int, floors: list<array{id: int, name: string, items: array}>}
     */
    public static function emptyMultiFloorPayload(): array
    {
        return [
            'version' => 2,
            'floors' => [
                ['id' => 1, 'name' => '1st Floor', 'items' => []],
            ],
        ];
    }
}
