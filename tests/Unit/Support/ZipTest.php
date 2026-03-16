<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Zip;
use Dcat\Admin\Tests\TestCase;

class ZipTest extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/dcat_zip_test_'.uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
        parent::tearDown();
    }

    protected function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function test_zip_extends_zip_archive(): void
    {
        $zip = new Zip;

        $this->assertInstanceOf(\ZipArchive::class, $zip);
    }

    public function test_make_creates_zip_file_from_string_source(): void
    {
        $sourceFile = $this->tempDir.'/test.txt';
        file_put_contents($sourceFile, 'Hello World');

        $zipPath = $this->tempDir.'/test.zip';
        Zip::make($zipPath, $sourceFile);

        $this->assertFileExists($zipPath);

        // Verify the zip contains the file
        $zip = new \ZipArchive;
        $zip->open($zipPath);
        $this->assertSame(1, $zip->numFiles);
        $zip->close();
    }

    public function test_make_creates_zip_with_callable(): void
    {
        $file1 = $this->tempDir.'/file1.txt';
        $file2 = $this->tempDir.'/file2.txt';
        file_put_contents($file1, 'Content 1');
        file_put_contents($file2, 'Content 2');

        $zipPath = $this->tempDir.'/callback.zip';
        Zip::make($zipPath, function (Zip $zip) use ($file1, $file2) {
            $zip->add($file1);
            $zip->add($file2);
        });

        $this->assertFileExists($zipPath);

        $zip = new \ZipArchive;
        $zip->open($zipPath);
        $this->assertSame(2, $zip->numFiles);
        $zip->close();
    }

    public function test_make_creates_zip_with_array_source(): void
    {
        $file1 = $this->tempDir.'/a.txt';
        $file2 = $this->tempDir.'/b.txt';
        file_put_contents($file1, 'A');
        file_put_contents($file2, 'B');

        $zipPath = $this->tempDir.'/array.zip';
        Zip::make($zipPath, [$file1, $file2]);

        $this->assertFileExists($zipPath);

        $zip = new \ZipArchive;
        $zip->open($zipPath);
        $this->assertSame(2, $zip->numFiles);
        $zip->close();
    }

    public function test_extract_extracts_to_destination(): void
    {
        // Create a zip first
        $sourceFile = $this->tempDir.'/extract_test.txt';
        file_put_contents($sourceFile, 'Extract Me');

        $zipPath = $this->tempDir.'/extract.zip';
        Zip::make($zipPath, $sourceFile);

        $extractDir = $this->tempDir.'/extracted';
        $result = Zip::extract($zipPath, $extractDir);

        $this->assertTrue($result);
        $this->assertDirectoryExists($extractDir);
    }

    public function test_extract_returns_false_for_invalid_zip(): void
    {
        $invalidFile = $this->tempDir.'/invalid.zip';
        file_put_contents($invalidFile, 'not a zip');

        $result = Zip::extract($invalidFile, $this->tempDir.'/out');

        $this->assertFalse($result);
    }

    public function test_remove_path_prefix(): void
    {
        $zip = new Zip;

        $ref = new \ReflectionMethod(Zip::class, 'removePathPrefix');
        $ref->setAccessible(true);

        $result = $ref->invoke($zip, '/var/sites/', '/var/sites/moo/cow/');

        $this->assertSame('moo/cow/', $result);
    }

    public function test_remove_path_prefix_returns_full_path_when_no_match(): void
    {
        $zip = new Zip;

        $ref = new \ReflectionMethod(Zip::class, 'removePathPrefix');
        $ref->setAccessible(true);

        $result = $ref->invoke($zip, '/other/prefix/', '/var/sites/moo/');

        $this->assertSame('/var/sites/moo/', $result);
    }

    public function test_folder_creates_folder_in_zip(): void
    {
        $zipPath = $this->tempDir.'/folder.zip';

        Zip::make($zipPath, function (Zip $zip) {
            $zip->folder('my-folder');
        });

        $this->assertFileExists($zipPath);

        $archive = new \ZipArchive;
        $archive->open($zipPath);
        $found = false;
        for ($i = 0; $i < $archive->numFiles; $i++) {
            $name = $archive->getNameIndex($i);
            if (str_contains($name, 'my-folder')) {
                $found = true;
                break;
            }
        }
        $archive->close();

        $this->assertTrue($found);
    }

    public function test_folder_prefix_is_restored_after_folder_call(): void
    {
        $zip = new Zip;
        $zipPath = $this->tempDir.'/prefix_test.zip';
        $zip->open($zipPath, \ZipArchive::CREATE);

        $ref = new \ReflectionProperty(Zip::class, 'folderPrefix');
        $ref->setAccessible(true);

        $this->assertSame('', $ref->getValue($zip));

        $zip->folder('test-folder');

        // After folder() the prefix should be restored
        $this->assertSame('', $ref->getValue($zip));

        $zip->close();
    }

    public function test_extract_creates_destination_directory(): void
    {
        $sourceFile = $this->tempDir.'/source.txt';
        file_put_contents($sourceFile, 'data');

        $zipPath = $this->tempDir.'/mkdir_test.zip';
        Zip::make($zipPath, $sourceFile);

        $newDir = $this->tempDir.'/new/nested/dir';
        $result = Zip::extract($zipPath, $newDir);

        $this->assertTrue($result);
        $this->assertDirectoryExists($newDir);
    }
}
