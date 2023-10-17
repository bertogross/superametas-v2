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

    // Specifies the table associated with the model.
    protected $table = 'user_metas';

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id', 'meta_key', 'meta_value'
    ];

    /**
     * Retrieve a user's meta value based on the given key.
     *
     * @param int    $userId The ID of the user.
     * @param string $key    The meta key to retrieve.
     * @return string|null The meta value or null if not found.
     */
    public static function getUserMeta($userId, $key)
    {
        // Query the 'user_metas' table to find the meta value for the given user and key.
        $meta = DB::connection('smAppTemplate')
                  ->table('user_metas')
                  ->where('user_id', $userId)
                  ->where('meta_key', $key)
                  ->first();

        // Return the meta value if found, otherwise return null.
        return $meta ? $meta->meta_value : null;
    }
}
