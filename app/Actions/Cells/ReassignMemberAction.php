<?php

namespace App\Actions\Cells;

use App\Models\User;
use App\Models\Cell;

class ReassignMemberAction
{
    /**
     * Reassign a member to a new cell and update member counts on both old and new cells.
     */
    public function execute(User $member, ?int $newCellId): void
    {
        $oldCellId = $member->cell_id;

        // Skip if same cell
        if ($oldCellId === $newCellId) {
            return;
        }

        $member->update(['cell_id' => $newCellId]);

        // Recount old cell members
        if ($oldCellId) {
            $oldCell = Cell::find($oldCellId);
            if ($oldCell) {
                $oldCell->update(['member_count' => $oldCell->getMembersCount()]);
            }
        }

        // Recount new cell members
        if ($newCellId) {
            $newCell = Cell::find($newCellId);
            if ($newCell) {
                $newCell->update(['member_count' => $newCell->getMembersCount()]);
            }
        }
    }
}
