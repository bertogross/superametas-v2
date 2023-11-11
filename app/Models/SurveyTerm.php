<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SurveyTerm extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    public $timestamps = false;

    protected $table = 'survey_terms';

    protected $fillable = ['name', 'slug', 'status'];

    public static function preListing($termsToArray = false)
    {
        $terms = DB::connection('smAppTemplate')
            ->table('survey_terms')
            ->where('status', 1)
            ->limit(3)
            ->get(['id AS topic_id', 'name AS topic_name']);

        if($termsToArray){
            // If needed to transform the results into an associative array with 'id' as keys and 'name' as values:
            $termsArray = $terms->mapWithKeys(function ($item) {
                return [$item->topic_id => $item->topic_name];
            })->toArray();

            return $termsArray ? $termsArray : null;

        }else{
            return $terms ? json_decode($terms, true) : null;
        }
    }


}
