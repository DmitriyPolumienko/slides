<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSource extends Model
{
    protected $fillable = ['presentation_id', 'source_type', 'file_path', 'raw_content', 'dataset_json'];
    protected $casts = ['dataset_json' => 'array'];
    
    public function presentation(): BelongsTo { return $this->belongsTo(Presentation::class); }
}
