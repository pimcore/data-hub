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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\MultihrefMetadata;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MultihrefMetadataType extends ObjectType
{
    protected $class;

    protected $fieldDefinition;

    /**
     * PimcoreObjectType constructor.
     *
     * @param $class
     */
    public function __construct(Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $config['name'] = 'object_'.$class->getName().'_'.$fieldDefinition->getName();
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.object');
        $fieldDefinition = $this->fieldDefinition;
        $class = $this->class;
        $resolver = new MultihrefMetadata($fieldDefinition, $class, $fieldHelper);
        $fields = ['element'  =>
                       [
                           'type'    => new HrefType($this->fieldDefinition, $this->class),
                           'resolve' => [$resolver, "resolveElement"]
                       ],
                   'metadata' => [
                       'type'    => Type::listOf(new ElementMetadataKeyValuePairType()),
                       'resolve' => [$resolver, "resolveMetadata"]
                   ]];

        $config['fields'] = $fields;

        return;
    }
}
