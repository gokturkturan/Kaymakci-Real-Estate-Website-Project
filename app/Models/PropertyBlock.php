<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyBlock extends Model
{
    protected $fillable = [
        'property_id',
        'start_date',
        'end_date',
        'label',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Every date (Y-m-d) covered by a manual block for this property.
     * Same shape and same exclusive-end convention as Booking::getBookedDates().
     */
    public static function getBlockedDates(int $propertyId): array
    {
        $blocks = self::where('property_id', $propertyId)
            ->where('end_date', '>=', now()->toDateString())
            ->get(['start_date', 'end_date']);

        $dates = [];
        foreach ($blocks as $block) {
            $current = $block->start_date->copy();
            while ($current < $block->end_date) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        return array_unique($dates);
    }

    /**
     * Check if a date range overlaps with an existing manual block.
     */
    public static function hasOverlap(int $propertyId, string $startDate, string $endDate, ?int $excludeId = null): bool
    {
        $query = self::where('property_id', $propertyId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($q2) use ($startDate, $endDate) {
                    // New range starts during an existing block
                    $q2->where('start_date', '<=', $startDate)
                       ->where('end_date', '>', $startDate);
                })->orWhere(function ($q2) use ($startDate, $endDate) {
                    // New range ends during an existing block
                    $q2->where('start_date', '<', $endDate)
                       ->where('end_date', '>=', $endDate);
                })->orWhere(function ($q2) use ($startDate, $endDate) {
                    // New range contains an existing block
                    $q2->where('start_date', '>=', $startDate)
                       ->where('end_date', '<=', $endDate);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
