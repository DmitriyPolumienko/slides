<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneration extends Model
{
    protected $fillable = ['slide_id', 'provider', 'model', 'prompt_sent', 'schema_sent', 'response_raw', 'response_parsed', 'status', 'error_message', 'attempt'];
    protected $casts = [
        'schema_sent' => 'array',
        'response_raw' => 'array',
        'response_parsed' => 'array',
    ];
    
    public function slide(): BelongsTo { return $this->belongsTo(Slide::class); }
}
