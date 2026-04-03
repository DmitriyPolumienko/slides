<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['name', 'slug', 'color_primary', 'color_secondary', 'color_accent', 'font_family', 'extra_config'];
    protected $casts = ['extra_config' => 'array'];
}
