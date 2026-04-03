<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Presentation extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'project_id', 'theme_id', 'language_id', 'master_template_id', 'header_options', 'footer_options', 'status'];
    protected $casts = [
        'header_options' => 'array',
        'footer_options' => 'array',
    ];
    
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function theme(): BelongsTo { return $this->belongsTo(Theme::class); }
    public function language(): BelongsTo { return $this->belongsTo(Language::class); }
    public function masterTemplate(): BelongsTo { return $this->belongsTo(MasterTemplate::class); }
    public function slides(): HasMany { return $this->hasMany(Slide::class)->orderBy('order'); }
    public function dataSources(): HasMany { return $this->hasMany(DataSource::class); }
}
