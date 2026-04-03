<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVersion extends Model
{
    protected $fillable = ['master_template_id', 'version', 'is_active', 'schema', 'locked_zones', 'editable_slots'];
    protected $casts = [
        'is_active' => 'boolean',
        'schema' => 'array',
        'locked_zones' => 'array',
        'editable_slots' => 'array',
    ];
    
    public function masterTemplate(): BelongsTo
    {
        return $this->belongsTo(MasterTemplate::class);
    }
}
