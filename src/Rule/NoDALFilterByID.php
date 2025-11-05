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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
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

        // When we find a MultiFilter/NotFilter, mark all nested New_ nodes as allowed
        if (in_array($className, [MultiFilter::class, NotFilter::class], true)) {
            $this->markNestedNodesAsAllowed($node);
            return [];
        }

        // Check direct EqualsFilter/EqualsAnyFilter usage
        if (in_array($className, [EqualsFilter::class, EqualsAnyFilter::class], true)) {
            return $this->checkFilterNode($node, $scope);
        }

        return [];
    }

    private function markNestedNodesAsAllowed(Node $allowedFilterNode): void
    {
        $nodeFinder = new NodeFinder();

        // Find all New_ nodes within this allowed filter node
        $nestedNewNodes = $nodeFinder->findInstanceOf($allowedFilterNode, New_::class);

        foreach ($nestedNewNodes as $nestedNode) {
            // Mark this node as being inside an allowed filter
            $nestedNode->setAttribute('insideAllowedFilter', true);
        }
    }

    /**
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkFilterNode(Node $node, Scope $scope): array
    {
        // Check if this node is marked as being inside an allowed filter
        if ($node->getAttribute('insideAllowedFilter') === true) {
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
