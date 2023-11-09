<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTerm extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    public $timestamps = false;

    protected $table = 'survey_terms';

    protected $fillable = ['name', 'slug', 'term_status'];



}
