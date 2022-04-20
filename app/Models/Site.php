<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'introduction_text_nl',
        'introduction_text_en',
        'name',
        'year',
        'path_nl',
        'path_en',
        'publisher_id',
        'allow_unsafe',
    ];

    protected $searchableFields = ['*'];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }
}
