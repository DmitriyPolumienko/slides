<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slide extends Model
{
    protected $fillable = ['presentation_id', 'order', 'slide_type', 'user_prompt', 'is_locked'];
    protected $casts = ['is_locked' => 'boolean'];
    
    public function presentation(): BelongsTo { return $this->belongsTo(Presentation::class); }
    public function slots(): HasMany { return $this->hasMany(SlideSlot::class); }
    public function aiGenerations(): HasMany { return $this->hasMany(AiGeneration::class); }
}
