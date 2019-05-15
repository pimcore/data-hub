<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class AbstractRelationsType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $class;

    protected $fieldDefinition;

    /**
     * AbstractRelationsType constructor.
     *
     * @param $class
     */
    public function __construct(Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $name = 'object_'.$class->getName().'_'.$fieldDefinition->getName();
        if ($fieldDefinition instanceof Data\MultihrefMetadata || $fieldDefinition instanceof Data\ObjectsMetadata) {
            $name .= '_element';
        }

        $config['name'] = $name;
        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    /**
     * @return array|\GraphQL\Type\Definition\ObjectType[]
     *
     * @throws \Exception
     */
    public function getTypes()
    {
        $fd = $this->getFieldDefinition();

        $types = [];

        if ($fd->getObjectsAllowed()) {
            if (!$fd->getClasses()) {
                $types = array_merge($types, array_values(ClassTypeDefinitions::getAll()));
            } else {
                $classes = $fd->getClasses();
                if (!is_array($classes)) {
                    $classes = [$classes];
                }
                foreach ($classes as $className) {
                    if (is_array($className)) {
                        $className = $className['classes'];
                    }
                    $types[] = ClassTypeDefinitions::get($className);
                }
            }
        }

        if (!$fd instanceof Data\ManyToManyObjectRelation) {
            if ($fd->getAssetsAllowed()) {
                $types[] = AssetType::getInstance();
            }
        }

        return $types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if ($element) {
            if ($element['__elementType'] == 'object') {
                $type = ClassTypeDefinitions::get($element['__elementSubtype']);

                return $type;
            } else {
                if ($element['__elementType'] == 'asset') {
                    return  AssetType::getInstance();
                }
            }
        }

        return null;
    }

    public function getFieldDefinition(): Data
    {
        return $this->fieldDefinition;
    }
}
