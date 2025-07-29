<?php

declare(strict_types=1);

namespace Shopware\PhpStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<New_>
 */
class NoDALFilterByID implements Rule
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->class instanceof Node\Name) {
            return [];
        }

        $className = $node->class->toString();

        // When we find a NotFilter, mark all nested New_ nodes as allowed
        if ($className === NotFilter::class) {
            $this->markNestedNodesAsAllowed($node);
            return [];
        }

        // Check direct EqualsFilter/EqualsAnyFilter usage
        if (in_array($className, [EqualsFilter::class, EqualsAnyFilter::class], true)) {
            return $this->checkFilterNode($node, $scope);
        }

        return [];
    }

    private function markNestedNodesAsAllowed(Node $notFilterNode): void
    {
        $nodeFinder = new NodeFinder();

        // Find all New_ nodes within this NotFilter
        $nestedNewNodes = $nodeFinder->findInstanceOf($notFilterNode, New_::class);

        foreach ($nestedNewNodes as $nestedNode) {
            // Mark this node as being inside a NotFilter
            $nestedNode->setAttribute('insideNotFilter', true);
        }
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFilterNode(Node $node, Scope $scope): array
    {
        // Check if this node is marked as being inside a NotFilter
        if ($node->getAttribute('insideNotFilter') === true) {
            return [];
        }

        if (empty($node->args)) {
            return [];
        }

        $args = $node->args;
        if (!is_array($args) || !isset($args[0])) {
            return [];
        }

        $firstArgNode = $args[0];
        if (!$firstArgNode instanceof Node\Arg) {
            return [];
        }

        $firstArgValue = $firstArgNode->value;

        if (!$firstArgValue instanceof Node\Scalar\String_) {
            return [];
        }

        if (strtolower($firstArgValue->value) === 'id') {
            return [
                RuleErrorBuilder::message('Using "id" directly in EqualsFilter or EqualsAnyFilter is forbidden. Pass the ids directly to the constructor of Criteria or use setIds instead')
                    ->line($node->getLine())
                    ->identifier('shopware.dal.filterById')
                    ->build(),
            ];
        }

        return [];
    }
}
