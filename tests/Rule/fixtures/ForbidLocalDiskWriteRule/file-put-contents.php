<?php

declare(strict_types=1);

file_put_contents('/some/file.txt', 'content');

file_put_contents('./relative/path.txt', 'content');

file_put_contents($someVariable, 'content');

// Valid: using sys_get_temp_dir()
file_put_contents(sys_get_temp_dir() . '/temp.txt', 'content');

// Valid: using sys_get_temp_dir() with DIRECTORY_SEPARATOR
file_put_contents(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'temp.txt', 'content');

// Valid: more complex concatenation with sys_get_temp_dir()
file_put_contents(sys_get_temp_dir() . '/subdir/' . 'temp.txt', 'content');

// Valid: sys_get_temp_dir() assigned to variable and then concatenated
$tempDir = sys_get_temp_dir();
file_put_contents($tempDir . '/temp.txt', 'content');

// Invalid: fopen with write mode
fopen('/some/file.txt', 'w');

// Invalid: fopen with append mode
fopen('./relative/path.txt', 'a');

// Invalid: fopen with write+ mode
fopen($someVariable, 'w+');

// Valid: fopen with read mode
fopen('/some/file.txt', 'r');

// Valid: fopen with write mode in temp directory
fopen(sys_get_temp_dir() . '/temp.txt', 'w');

// Valid: fopen with write mode using temp variable
fopen($tempDir . '/temp.txt', 'w+');

// Invalid: ZipArchive create
$zip = new ZipArchive();
$zip->open('/some/file.zip', ZipArchive::CREATE);

// Invalid: ZipArchive overwrite
$zip->open('./relative/path.zip', ZipArchive::OVERWRITE);

// Valid: ZipArchive read mode
$zip->open('/some/file.zip', ZipArchive::RDONLY);

// Valid: ZipArchive create in temp directory
$zip->open(sys_get_temp_dir() . '/temp.zip', ZipArchive::CREATE);

// Valid: ZipArchive create using temp variable
$zip->open($tempDir . '/temp.zip', ZipArchive::CREATE);

// Invalid: symlink
symlink('/some/target', '/some/link');

// Invalid: symlink with relative path
symlink('./target', './link');

// Valid: symlink in temp directory
symlink('/some/target', sys_get_temp_dir() . '/temp_link');

// Valid: symlink using temp variable
symlink('/some/target', $tempDir . '/temp_link');

// Invalid: mkdir
mkdir('/some/directory');

// Invalid: rmdir
rmdir('./relative/directory');

// Invalid: unlink
unlink('/some/file.txt');

// Invalid: rename
rename('/source/file.txt', '/dest/file.txt');

// Valid: mkdir in temp directory
mkdir(sys_get_temp_dir() . '/temp_dir');

// Valid: rmdir using temp variable
rmdir($tempDir . '/temp_dir');

// Valid: unlink in temp directory
unlink(sys_get_temp_dir() . '/temp_file.txt');

// Valid: rename within temp directory
rename(sys_get_temp_dir() . '/source.txt', sys_get_temp_dir() . '/dest.txt');

// Valid: php:// streams
file_put_contents('php://stdout', 'content');
file_put_contents('php://stderr', 'error');
fopen('php://stdin', 'r');
fopen('php://stdout', 'w');

// Valid: STDIN/STDOUT/STDERR constants
file_put_contents(STDOUT, 'content');
file_put_contents(STDERR, 'error');
fopen(STDIN, 'r');

// Invalid: Symfony Filesystem component
$filesystem = new \Symfony\Component\Filesystem\Filesystem();
$filesystem->dumpFile('/some/file.txt', 'content');
$filesystem->mkdir('/some/directory');
$filesystem->remove('/some/file.txt');
$filesystem->copy('/source.txt', '/dest.txt');

// Valid: Symfony Filesystem in temp directory
$filesystem->dumpFile(sys_get_temp_dir() . '/temp.txt', 'content');
$filesystem->mkdir($tempDir . '/temp_dir');

// Valid: Symfony Filesystem read-only operations
$filesystem->exists('/some/file.txt');
