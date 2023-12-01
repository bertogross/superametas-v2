<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SettingsDatabase extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;


    public static function deleteOldData($meantime){
        // Delete old data for the given period from wlsm_sales
        try {
            DB::connection('smAppTemplate')->table('wlsm_sales')->where('date_sale', 'like', $meantime . '%')->delete();
        } catch (\Exception $e) {
            \Log::error('Failed to delete from wlsm_sales: ' . $e->getMessage());
            return false;
        }
    }


}
