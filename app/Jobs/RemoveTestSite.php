<?php

namespace App\Jobs;

use App\Http\Controllers\SiteController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RemoveTestSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $zipPath;
    public $sitePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($zipPath, $sitePath)
    {
        $this->zipPath = $zipPath;
        $this->sitePath = $sitePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Storage::delete($this->zipPath);
        Storage::deleteDirectory($this->sitePath);
    }
}
