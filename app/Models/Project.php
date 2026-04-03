<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name', 'slug', 'description'];
    
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }
}
