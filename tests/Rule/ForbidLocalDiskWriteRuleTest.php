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
                'Usage of file_put_contents is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                5,
            ],
            [
                'Usage of file_put_contents is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                7,
            ],
            [
                'Usage of file_put_contents is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                9,
            ],
            [
                'Usage of fopen with write mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                25,
            ],
            [
                'Usage of fopen with write mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                28,
            ],
            [
                'Usage of fopen with write mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                31,
            ],
            [
                'Usage of ZipArchive::open with create mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                44,
            ],
            [
                'Usage of ZipArchive::open with create mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                47,
            ],
            [
                'Usage of symlink is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                59,
            ],
            [
                'Usage of symlink is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                62,
            ],
            [
                'Usage of mkdir is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                71,
            ],
            [
                'Usage of rmdir is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                74,
            ],
            [
                'Usage of unlink is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                77,
            ],
            [
                'Usage of rename is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                80,
            ],
            [
                'Usage of Symfony Filesystem::dumpFile is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                107,
            ],
            [
                'Usage of Symfony Filesystem::mkdir is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                108,
            ],
            [
                'Usage of Symfony Filesystem::remove is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                109,
            ],
            [
                'Usage of Symfony Filesystem::copy is forbidden. Use temporary directory with sys_get_temp_dir() if needed.',
                110,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new ForbidLocalDiskWriteRule();
    }
}
