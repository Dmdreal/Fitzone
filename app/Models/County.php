<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = ['name'];

    protected $appends = ['display_name'];

    public function getDisplayNameAttribute(): string
    {
        $name = $this->attributes['name'] ?? '';
        return ucwords(strtolower($name), " -'");
    }

    public function scopeSearchByName($query, string $term)
    {
        $term = strtolower(trim($term));
        return $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
    }
}
