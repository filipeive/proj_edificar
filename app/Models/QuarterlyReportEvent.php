<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyReportEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'quarterly_report_id',
        'event_type_id',
        'count',
        'description',
    ];

    public function quarterlyReport()
    {
        return $this->belongsTo(QuarterlyReport::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }
}
