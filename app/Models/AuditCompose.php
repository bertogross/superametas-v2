<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditCompose extends Model
{
    use HasFactory;

    // Specifies the database connection for this model.
    protected $connection = 'smAppTemplate';

    protected $table = 'audit_composes';

    protected $fillable = [
        'user_id',
        'title',
        'jsondata',
        //'status',
    ];

    protected $casts = [
        'jsondata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
