<?php
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\GraphQL\AssetType;

use Pimcore\Model\Asset;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class AssetTreeType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    /**
     * TreeObjectType constructor.
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'asset_tree'])
    {
        $this->setGraphQLService($graphQlService);
        parent::__construct($config);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTypes()
    {
        $types = [];
        $types[] = $this->getGraphQlService()->buildAssetType('asset');
        $types[] = $this->getGraphQlService()->getAssetTypeDefinition("_asset_folder");
        return $types;
    }

    /**
     * @inheritDoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        if (!$element) {
            return null;
        }
        $asset = Asset::getById($element['id']);

        if ($asset instanceof Asset\Folder) {
            return $this->getGraphQlService()->getAssetTypeDefinition('_asset_folder');
        }
        return $this->getGraphQlService()->buildAssetType('asset');
    }
}
