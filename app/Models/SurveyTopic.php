<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTopic extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = [
        'step_id',
        'user_id',
        'question',
        'topic_order'
    ];

    public function step()
    {
        return $this->belongsTo(SurveyStep::class);
    }
}
