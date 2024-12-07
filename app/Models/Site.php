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
        'deactivate',
    ];

    protected $searchableFields = ['*'];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function getSiteUrl($inEnglish = false)
    {
        $lang = $inEnglish ? 'en' : 'nl';
        return SiteController::getSitePathFromZip(Storage::url($this->{'path_'.$lang}));
    }

    public function getYearText($inEnglish = false)
    {
        switch ($this->year) {
            case 1:
                return __('app.student_year.first', [], $inEnglish ? 'en' : 'nl');
            case 2:
                return __('app.student_year.second', [], $inEnglish ? 'en' : 'nl');
            case 3:
                return __('app.student_year.third', [], $inEnglish ? 'en' : 'nl');
            case 4:
                return __('app.student_year.fourth', [], $inEnglish ? 'en' : 'nl');
            default:
                return __('app.student', [], $inEnglish ? 'en' : 'nl');
        }
    }
}
