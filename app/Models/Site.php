<?php

namespace App\Models;

use App\Http\Controllers\SiteController;
use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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

    public function getSiteUrl()
    {
        $lang = 'nl';

        //TODO: en

        return SiteController::getSitePathFromZip(Storage::url($this->{'path_'.$lang}));
    }

    public function getYearText()
    {
        switch ($this->year) {
            case 1:
                return __('eerstejaars student');
            case 2:
                return __('tweedejaars student');
            case 3:
                return __('derdejaars student');
            case 4:
                return __('vierdejaars student');
            default:
                return __('student');
        }
    }
}
