<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlideSlot extends Model
{
    protected $fillable = ['slide_id', 'slot_key', 'slot_type', 'content', 'is_locked'];
    protected $casts = ['is_locked' => 'boolean'];
    
    public function slide(): BelongsTo { return $this->belongsTo(Slide::class); }
    
    public function getContentDecodedAttribute(): mixed
    {
        $decoded = json_decode($this->content, true);
        return $decoded ?? $this->content;
    }
}
