<?php

namespace App\Observers;

use App\Models\HeadResident;
use App\Models\Resident;

class ResidentObserver
{
    /**
     * Handle the Resident "created" event.
     */
    public function created(Resident $resident): void
    {
        // Update resident count when a new resident is added to a family
        if ($resident->kk_number) {
            $headResident = HeadResident::where('kk_number', $resident->kk_number)->first();

            if ($headResident) {
                $headResident->increment('resident_count');
            } else {
                // Create new head resident record if it doesn't exist
                HeadResident::create([
                    'kk_number' => $resident->kk_number,
                    'head_nik' => $resident->nik,
                    'head_name' => $resident->full_name,
                    'resident_count' => 1,
                ]);
            }
        }

        // If this resident is marked as head, remove is_head from other residents in same family
        if ($resident->is_head && $resident->kk_number) {
            Resident::where('kk_number', $resident->kk_number)
                ->where('id', '!=', $resident->id)
                ->update(['is_head' => false]);
        }
    }

    /**
     * Handle the Resident "updated" event.
     */
    public function updated(Resident $resident): void
    {
        // Handle kk_number change
        if ($resident->isDirty('kk_number')) {
            $oldKkNumber = $resident->getOriginal('kk_number');
            $newKkNumber = $resident->kk_number;

            // Decrement old family's resident count
            if ($oldKkNumber) {
                $oldHeadResident = HeadResident::where('kk_number', $oldKkNumber)->first();
                if ($oldHeadResident && $oldHeadResident->resident_count > 1) {
                    $oldHeadResident->decrement('resident_count');
                }
            }

            // Increment new family's resident count
            if ($newKkNumber) {
                $newHeadResident = HeadResident::where('kk_number', $newKkNumber)->first();
                if ($newHeadResident) {
                    $newHeadResident->increment('resident_count');
                }
            }
        }

        // If is_head is changed to true, remove is_head from other residents in same family
        if ($resident->isDirty('is_head') && $resident->is_head) {
            Resident::where('kk_number', $resident->kk_number)
                ->where('id', '!=', $resident->id)
                ->update(['is_head' => false]);

            // Update head resident info
            $headResident = HeadResident::where('kk_number', $resident->kk_number)->first();
            if ($headResident) {
                $headResident->update([
                    'head_nik' => $resident->nik,
                    'head_name' => $resident->full_name,
                ]);
            }
        }
    }

    /**
     * Handle the Resident "deleted" event.
     */
    public function deleted(Resident $resident): void
    {
        // Decrease resident count when a resident is deleted
        if ($resident->kk_number) {
            $headResident = HeadResident::where('kk_number', $resident->kk_number)->first();

            if ($headResident && $headResident->resident_count > 1) {
                $headResident->decrement('resident_count');
            } elseif ($headResident && $headResident->resident_count == 1) {
                // Delete the head resident if it's the last one
                $headResident->delete();
            }
        }
    }

    /**
     * Handle the Resident "restored" event.
     */
    public function restored(Resident $resident): void
    {
        //
    }

    /**
     * Handle the Resident "force deleted" event.
     */
    public function forceDeleted(Resident $resident): void
    {
        // Same logic as soft delete
        if ($resident->kk_number) {
            $headResident = HeadResident::where('kk_number', $resident->kk_number)->first();

            if ($headResident && $headResident->resident_count > 1) {
                $headResident->decrement('resident_count');
            } elseif ($headResident && $headResident->resident_count == 1) {
                $headResident->delete();
            }
        }
    }
}
