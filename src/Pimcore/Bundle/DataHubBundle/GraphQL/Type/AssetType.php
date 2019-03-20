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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Model\Asset;

class AssetType extends ObjectType
{
    private static $instance;

    protected $fieldname;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(['name' => 'asset']);
        }

        return self::$instance;
    }

    /**
     * AssetType constructor.
     *
     * @param array $config
     * @param array $context
     */
    public function __construct($config = [], $context = [])
    {
        $this->build($config);
        parent::__construct($config);
    }

    public static function extractCommonFields(Asset $asset)
    {
        return [
            'id' => $asset->getId(),
            'fullpath' => $asset->getFullPath(),
            'type' => $asset->getType()
//            ,
//            "data" => $asset->getData()
        ];
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType();
        $config['fields'] = [
            'creationDate' => Type::int(),
            'id' => ['name' => 'id',
                'type' => Type::id(),
            ],
            'filename' => Type::string(),
            'fullpath' => [
                'type' => Type::string(),
                'args' => [
                    'thumbnail' => ['type' => Type::string()]

                ]
            ],
            'mimetype' => Type::string(),

            'modificationDateDate' => Type::int(),
            'type' => Type::string(),
            'filesize' => Type::int(),
            'data' => [
                'type' => Type::string(),
                'args' => [
                    'thumbnail' => ['type' => Type::string()]

                ]
            ],
            'metadata' => [
                'type' => Type::listOf(new AssetMetadataItem()),
                'resolve' => [$resolver, "resolve"]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname(string $fieldname): void
    {
        $this->fieldname = $fieldname;
    }
}
