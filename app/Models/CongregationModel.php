<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCongregation;
use Illuminate\Database\Eloquent\Model;

abstract class CongregationModel extends Model
{
    use BelongsToCongregation;
}