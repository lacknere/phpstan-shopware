<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Tests\Rule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Shopware\PhpStan\Rule\ForbidLocalDiskWriteRule;

/**
 * @internal
 *
 * @extends RuleTestCase<ForbidLocalDiskWriteRule>
 */
class ForbidLocalDiskWriteRuleTest extends RuleTestCase
{
    public function testAnalyse(): void
    {
        $this->analyse([__DIR__ . '/fixtures/ForbidLocalDiskWriteRule/file-put-contents.php'], [
            [
                "Usage of file_put_contents is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                5,
            ],
            [
                "Usage of file_put_contents is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                7,
            ],
            [
                "Usage of file_put_contents is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                9,
            ],
            [
                "Usage of fopen with write mode is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                25,
            ],
            [
                "Usage of fopen with write mode is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                28,
            ],
            [
                "Usage of fopen with write mode is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                31,
            ],
            [
                "Usage of ZipArchive::open with create mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                44,
            ],
            [
                "Usage of ZipArchive::open with create mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                47,
            ],
            [
                "Usage of symlink is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                66,
            ],
            [
                "Usage of symlink is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                69,
            ],
            [
                "Usage of mkdir is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                78,
            ],
            [
                "Usage of rmdir is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                81,
            ],
            [
                "Usage of unlink is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                84,
            ],
            [
                "Usage of rename is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                87,
            ],
            [
                "Usage of Symfony Filesystem::dumpFile is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                114,
            ],
            [
                "Usage of Symfony Filesystem::mkdir is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                115,
            ],
            [
                "Usage of Symfony Filesystem::remove is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                116,
            ],
            [
                "Usage of Symfony Filesystem::copy is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.\n    💡 Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html",
                117,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new ForbidLocalDiskWriteRule();
    }
}
