<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class RemoveTestSite implements ShouldQueue
{
    use Queueable;

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
        if (Storage::disk('public')->exists($this->zipPath))
            Storage::disk('public')->delete($this->zipPath);

        if (Storage::disk('public')->exists($this->sitePath))
            Storage::disk('public')->deleteDirectory($this->sitePath);
    }
}
