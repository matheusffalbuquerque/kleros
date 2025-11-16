<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaPastoralPostAttachment extends Model
{
    use HasFactory;

    protected $table = 'areapastoral_post_attachments';

    protected $fillable = [
        'post_id',
        'titulo',
        'tipo',
        'caminho',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(AreaPastoralPost::class, 'post_id');
    }
}
