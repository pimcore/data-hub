<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BlockType extends ObjectType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /** @var ClassDefinition */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param Service $graphQlService
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param array $config
     */
    public function __construct(Service $graphQlService, Data $fieldDefinition, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $this->setGraphQLService($graphQlService);

        $this->build($config);

        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $typeName = 'block_'.$this->class->getName().'_'.$this->fieldDefinition->getName() . '_entry';
        $type = BlockEntryType::getInstance($typeName, $this->graphQlService, $this->fieldDefinition, $this->class);

        $config['name'] = 'block_'.$this->class->getName().'_'.$this->fieldDefinition->getName();
        $config['fields'] = [
            'entries' => Type::listOf($type)
        ];
    }
}
