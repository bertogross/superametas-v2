<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyExecution extends Model
{
    use HasFactory;

    // Specify the connection name
    protected $connection = 'smAppTemplate';

    // Fillable fields for a survey execution
    protected $fillable = [
        'survey_id',
        'user_id',
        'status',
        'start_time',
        'end_time',
    ];

    // Define the relationship with the Survey model
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // You can add more methods here as needed...
}
