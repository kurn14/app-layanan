<?php

namespace App\Services;

use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatusTransitionService
{
    /**
     * Transition a model's status and log to status_histories table.
     */
    public static function transition(
        Model $record,
        string|\BackedEnum $toStatus,
        ?string $notes = null,
        ?User $user = null,
        array $extraAttributes = [],
    ): Model {
        $toStatusValue = $toStatus instanceof \BackedEnum ? $toStatus->value : (string) $toStatus;
        $fromStatusValue = $record->status instanceof \BackedEnum ? $record->status->value : (string) $record->status;
        $user = $user ?? Auth::user();

        return DB::transaction(function () use ($record, $fromStatusValue, $toStatusValue, $notes, $user, $extraAttributes) {
            $updateData = array_merge([
                'status' => $toStatusValue,
            ], $extraAttributes);

            $record->update($updateData);

            StatusHistory::create([
                'statusable_type' => $record->getMorphClass(),
                'statusable_id' => $record->getKey(),
                'from_status' => $fromStatusValue,
                'to_status' => $toStatusValue,
                'notes' => $notes,
                'user_id' => $user?->id,
                'created_at' => now(),
            ]);

            return $record->fresh();
        });
    }
}
