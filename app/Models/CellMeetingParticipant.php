<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCongregation;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CellMeetingParticipant extends Pivot
{
    use BelongsToCongregation;

    protected $table = 'cell_meeting_participants';

    protected $fillable = ['cell_meeting_id', 'user_id', 'congregation_id'];

    public $incrementing = true;
}