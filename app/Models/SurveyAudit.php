<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAudit extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;
}
