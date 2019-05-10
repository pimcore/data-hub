<?php
declare(strict_types=1);
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
use Pimcore\Model\DataObject\Data\Hotspotimage;

/**
 * Class HotspotType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Type
 */
class HotspotType extends ObjectType
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var string
     */
    protected $fieldname;

    /**
     * @return HotspotType
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(['name' => 'hotspotimage']);
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

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\HotspotType();
        $config['fields'] = [
            'image' => [
                'type' => AssetType::getInstance(),
                'resolve' => [$resolver, "resolveImage"],
            ],
            'crop' => [
                'type' => HotspotCropType::getInstance(),
                'resolve' => [$resolver, "resolveCrop"],
            ],
            'hotspots' => [
                'type' => Type::listOf(HotspotHotspotType::getInstance()),
                'resolve' => [$resolver, "resolveHotspots"],
            ],
            'marker' => [
                'type' => Type::listOf(HotspotMarkerType::getInstance()),
                'resolve' => [$resolver, "resolveMarker"],
            ],
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
