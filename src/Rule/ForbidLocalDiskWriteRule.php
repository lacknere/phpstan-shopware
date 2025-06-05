<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\VerbosityLevel;

/**
 * @implements Rule<Node>
 */
class ForbidLocalDiskWriteRule implements Rule
{
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            return $this->processFuncCall($node, $scope);
        }

        if ($node instanceof MethodCall) {
            return $this->processMethodCall($node, $scope);
        }

        return [];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function processFuncCall(FuncCall $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Name) {
            return [];
        }

        $name = $node->name->toString();

        if ($name === 'file_put_contents') {
            return $this->checkFilePutContents($node, $scope);
        }

        if ($name === 'fopen') {
            return $this->checkFopen($node, $scope);
        }

        if ($name === 'symlink') {
            return $this->checkSymlink($node, $scope);
        }

        if ($name === 'mkdir') {
            return $this->checkMkdir($node, $scope);
        }

        if ($name === 'rmdir') {
            return $this->checkRmdir($node, $scope);
        }

        if ($name === 'unlink') {
            return $this->checkUnlink($node, $scope);
        }

        if ($name === 'rename') {
            return $this->checkRename($node, $scope);
        }

        return [];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function processMethodCall(MethodCall $node, Scope $scope): array
    {
        if (!$node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        if ($methodName === 'open') {
            return $this->checkZipArchiveOpen($node, $scope);
        }

        // Check for Symfony Filesystem component methods
        if ($this->isSymfonyFilesystemMethod($methodName)) {
            return $this->checkSymfonyFilesystem($node, $scope);
        }

        return [];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkFilePutContents(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) === 0) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;

        if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of file_put_contents is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkFopen(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) < 2) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;
        $secondArg = $node->getArgs()[1]->value;

        // Check if the mode contains write operations (w, a, x, c with +)
        if ($secondArg instanceof String_) {
            $mode = $secondArg->value;
            if ($this->isWriteMode($mode)) {
                if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
                    return [];
                }

                return [
                    RuleErrorBuilder::message('Usage of fopen with write mode is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                        ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                        ->line($node->getLine())
                        ->identifier('shopware.forbidLocalDiskWrite')
                        ->build(),
                ];
            }
        }

        return [];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkSymlink(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) < 2) {
            return [];
        }

        // For symlink, the second argument is the link path (where the symlink is created)
        $secondArg = $node->getArgs()[1]->value;

        if ($this->isTemporaryDirectoryPath($secondArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of symlink is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkMkdir(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) === 0) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;

        if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of mkdir is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkRmdir(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) === 0) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;

        if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of rmdir is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkUnlink(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) === 0) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;

        if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of unlink is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkRename(FuncCall $node, Scope $scope): array
    {
        if (count($node->getArgs()) < 2) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;
        $secondArg = $node->getArgs()[1]->value;

        // Check both source and destination paths
        if ($this->isTemporaryDirectoryPath($firstArg, $scope) && $this->isTemporaryDirectoryPath($secondArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message('Usage of rename is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.')
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkZipArchiveOpen(MethodCall $node, Scope $scope): array
    {
        // Check if this is a ZipArchive method call
        $callerType = $scope->getType($node->var);
        if (!$callerType->isObject()->yes()) {
            return [];
        }

        $className = $callerType->getObjectClassNames();
        if (!in_array('ZipArchive', $className, true)) {
            return [];
        }

        if (count($node->getArgs()) < 2) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;
        $secondArg = $node->getArgs()[1]->value;

        // Check if the flags contain create operations (ZipArchive::CREATE, ZipArchive::OVERWRITE)
        if ($this->isZipCreateMode($secondArg, $scope)) {
            if ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
                return [];
            }

            return [
                RuleErrorBuilder::message('Usage of ZipArchive::open with create mode is forbidden. Use temporary directory with sys_get_temp_dir() if needed.')
                    ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                    ->line($node->getLine())
                    ->identifier('shopware.forbidLocalDiskWrite')
                    ->build(),
            ];
        }

        return [];
    }

    private function isSymfonyFilesystemMethod(string $methodName): bool
    {
        $filesystemMethods = [
            'dumpFile',
            'mkdir',
            'exists',
            'touch',
            'remove',
            'chmod',
            'chown',
            'chgrp',
            'rename',
            'symlink',
            'hardlink',
            'readlink',
            'makePathRelative',
            'mirror',
            'copy',
            'tempnam',
            'appendToFile',
        ];

        return in_array($methodName, $filesystemMethods, true);
    }

    /**
     * @return array<array-key, RuleError|string>
     */
    private function checkSymfonyFilesystem(MethodCall $node, Scope $scope): array
    {
        // Check if this is a Symfony Filesystem method call
        $callerType = $scope->getType($node->var);
        if (!$callerType->isObject()->yes()) {
            return [];
        }

        $className = $callerType->getObjectClassNames();
        if (!in_array('Symfony\\Component\\Filesystem\\Filesystem', $className, true)) {
            return [];
        }

        if (!$node->name instanceof Identifier) {
            return [];
        }

        $methodName = $node->name->toString();

        // Methods that only read (like exists, readlink) don't need to be checked
        $readOnlyMethods = ['exists', 'readlink', 'makePathRelative'];
        if (in_array($methodName, $readOnlyMethods, true)) {
            return [];
        }

        if (count($node->getArgs()) === 0) {
            return [];
        }

        $firstArg = $node->getArgs()[0]->value;

        // For methods like copy, mirror, rename that have multiple path arguments,
        // we need to check different arguments
        if (in_array($methodName, ['copy', 'rename'], true) && count($node->getArgs()) >= 2) {
            $secondArg = $node->getArgs()[1]->value;
            // Both paths should be in temp directory
            if ($this->isTemporaryDirectoryPath($firstArg, $scope) && $this->isTemporaryDirectoryPath($secondArg, $scope)) {
                return [];
            }
        } elseif ($this->isTemporaryDirectoryPath($firstArg, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf('Usage of Symfony Filesystem::%s is forbidden to local files. Use temporary directory with sys_get_temp_dir() if needed.', $methodName))
                ->addTip('Use flysystem instead https://developer.shopware.com/docs/guides/plugins/plugins/framework/filesystem/filesystem.html')
                ->line($node->getLine())
                ->identifier('shopware.forbidLocalDiskWrite')
                ->build(),
        ];
    }

    private function isZipCreateMode(Node $node, Scope $scope): bool
    {
        if (!$node instanceof Expr) {
            return false;
        }

        // For simplicity, we'll check for common create flags
        // In a real implementation, we might want to be more sophisticated
        $type = $scope->getType($node);
        $typeDescription = $type->describe(VerbosityLevel::precise());

        // Check for ZipArchive::CREATE, ZipArchive::OVERWRITE constants or their numeric values
        return strpos($typeDescription, 'CREATE') !== false ||
               strpos($typeDescription, 'OVERWRITE') !== false ||
               $typeDescription === '1' ||
               $typeDescription === '8';
    }

    private function isWriteMode(string $mode): bool
    {
        // Check for write modes: w, w+, a, a+, x, x+, c, c+
        return (bool) preg_match('/^[waxc][\+b]*$/', $mode);
    }

    private function isTemporaryDirectoryPath(Node $node, Scope $scope): bool
    {
        if ($node instanceof Concat) {
            return $this->containsSysGetTempDir($node, $scope);
        }

        if ($this->isAllowedStream($node, $scope)) {
            return true;
        }

        return false;
    }

    private function isAllowedStream(Node $node, Scope $scope): bool
    {
        // Check for string literals containing allowed streams
        if ($node instanceof String_) {
            $value = $node->value;
            // Allow php:// streams for standard input/output/error
            if (in_array($value, ['php://stdin', 'php://stdout', 'php://stderr'], true)) {
                return true;
            }
        }

        // Check for constants STDIN, STDOUT, STDERR
        if ($node instanceof ConstFetch) {
            $constName = $node->name->toString();
            if (in_array($constName, ['STDIN', 'STDOUT', 'STDERR'], true)) {
                return true;
            }
        }

        return false;
    }

    private function containsSysGetTempDir(Node $node, Scope $scope): bool
    {
        if ($node instanceof FuncCall && $node->name instanceof Node\Name) {
            if ($node->name->toString() === 'sys_get_temp_dir') {
                return true;
            }
        }

        if ($node instanceof Variable && is_string($node->name)) {
            // Check if the variable was assigned the result of sys_get_temp_dir()
            $variableType = $scope->getType($node);
            $typeDescription = $variableType->describe(VerbosityLevel::precise());

            // This is a simple heuristic - in a real implementation we might need more sophisticated tracking
            // For now, we'll allow variables that contain 'temp' in their type description or name
            if (strpos($node->name, 'temp') !== false || strpos($typeDescription, 'temp') !== false) {
                return true;
            }
        }

        if ($node instanceof Concat) {
            return $this->containsSysGetTempDir($node->left, $scope) || $this->containsSysGetTempDir($node->right, $scope);
        }

        return false;
    }
}
