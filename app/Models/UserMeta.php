<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

/**
 * UserMeta Model
 *
 * Represents the metadata associated with a user.
 */
class UserMeta extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id', 'meta_key', 'meta_value'
    ];

    /**
     * Retrieve a user's meta value based on the given key.
     *
     * @param int    $userId The ID of the user.
     * @param string $metaKey The meta key to retrieve.
     * @return string|null The meta value or null if not found.
     */
    public static function getUserMeta($userId, $metaKey)
    {
        // Query the 'user_metas' table to find the meta value for the given user and key.
        $meta = DB::connection('smAppTemplate')
                  ->table('user_metas')
                  ->where('user_id', $userId)
                  ->where('meta_key', $metaKey)
                  ->first();

        // Return the meta value if found, otherwise return null.
        return $meta ? $meta->meta_value : null;
    }

    /**
     * Update or create a user's meta value based on the given key.
     *
     * @param int    $userId The ID of the user.
     * @param string $metaKey    The meta key to update or create.
     * @param string $metaValue  The meta value to set.
     * @return void
     */
    public static function updateUserMeta($userId, $metaKey, $metaValue)
    {
        if($metaValue){
            // Update or create the 'user_metas' record for the given user and key.
            $meta = DB::connection('smAppTemplate')
                ->table('user_metas')
                ->updateOrInsert(
                    ['user_id' => $userId, 'meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
                return $meta ? true : null;
        }else{
            /// Delete the 'user_metas' record for the given user and key.
            $deleted = DB::connection('smAppTemplate')
                ->table('user_metas')
                ->where('user_id', $userId)
                ->where('meta_key', $metaKey)
                ->delete();

            return $deleted ? true : false;
        }
    }


}
