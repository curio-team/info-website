<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use ZipArchive;

abstract class TestCase extends BaseTestCase
{
    /**
     * Build a zip upload from the given entries.
     *
     * @param  array<string, string>  $entries  Map of entry name inside the zip => file contents
     * @param  array<string, int>  $unixModes  Map of entry name => unix mode (e.g. 0120777 for a symlink)
     */
    protected function makeZipUpload(array $entries, array $unixModes = [], string $name = 'site.zip'): UploadedFile
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'site').'.zip';

        $archive = new ZipArchive;
        $archive->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($entries as $entryName => $contents) {
            $archive->addFromString($entryName, $contents);
        }

        foreach ($unixModes as $entryName => $mode) {
            $archive->setExternalAttributesName($entryName, ZipArchive::OPSYS_UNIX, $mode << 16);
        }

        $archive->close();

        return new UploadedFile($zipPath, $name, 'application/zip', null, true);
    }
}
