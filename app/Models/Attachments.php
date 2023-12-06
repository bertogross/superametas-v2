<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachments extends Model
{
    use HasFactory;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    protected $fillable = ['user_id', 'parent_id', 'path', 'type', 'title', 'description', 'size', 'order'];

    public static function getAttachmentPathById($attachmentId)
    {
        if($attachmentId){
            $attachment = self::find($attachmentId);
            return $attachment ? URL::asset('storage/'.$attachment->path) : '';
            //URL::asset('storage/' .. )

        }
    }

    public static function getAttachmentDateById($attachmentId)
    {
        if($attachmentId){
            $attachment = self::find($attachmentId);
            return $attachment ? date("d/m/Y H:i", strtotime($attachment->created_at)) : '';
            //URL::asset('storage/' .. )

        }
    }


}
