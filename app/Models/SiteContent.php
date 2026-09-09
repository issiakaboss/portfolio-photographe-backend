<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = ['section', 'locale', 'content'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }
}
