<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterTemplate extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    
    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class);
    }
    
    public function activeVersion(): ?TemplateVersion
    {
        return $this->versions()->where('is_active', true)->latest()->first();
    }
}
