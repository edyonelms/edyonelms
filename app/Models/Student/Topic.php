<?php

namespace App\Models\Student;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['organization_id', 'chapter_id', 'topic_name', 'topic_content', 'order', 'image_path', 'pdf_path'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
