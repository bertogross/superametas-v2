<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

/**
 * AuditMeta Model
 *
 * Represents the metadata associated with a user.
 */
class AuditMeta extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    // The attributes that are mass assignable.
    protected $fillable = [
        'audit_id', 'meta_key', 'meta_value'
    ];

    /**
     * Retrieve a audit's meta value based on the given key.
     *
     * @param int    $auditId The ID of the audit.
     * @param string $metaKey The meta key to retrieve.
     * @return string|null The meta value or null if not found.
     */
    public static function getAuditMeta($auditId, $metaKey)
    {
        // Query the 'audit_metas' table to find the meta value for the given audit and key.
        $meta = DB::connection('smAppTemplate')
                  ->table('audit_metas')
                  ->where('audit_id', $auditId)
                  ->where('meta_key', $metaKey)
                  ->first();

        // Return the meta value if found, otherwise return null.
        return $meta ? $meta->meta_value : null;
    }

    /**
     * Update or create a audit's meta value based on the given key.
     *
     * @param int    $auditId The ID of the audit.
     * @param string $metaKey    The meta key to update or create.
     * @param string $metaValue  The meta value to set.
     * @return void
     */
    public static function updateAuditMeta($auditId, $metaKey, $metaValue)
    {
        if($metaValue){
            // Update or create the 'audit_metas' record for the given audit and key.
            $meta = DB::connection('smAppTemplate')
                ->table('audit_metas')
                ->updateOrInsert(
                    ['audit_id' => $auditId, 'meta_key' => $metaKey],
                    ['meta_value' => $metaValue]
                );
                return $meta ? true : null;
        }else{
            /// Delete the 'audit_metas' record for the given audit and key.
            $deleted = DB::connection('smAppTemplate')
                ->table('audit_metas')
                ->where('audit_id', $auditId)
                ->where('meta_key', $metaKey)
                ->delete();

            return $deleted ? true : false;
        }
    }


}
