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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class EmailType extends AbstractDocumentType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'document_email'], $context = [])
    {
        parent::__construct($graphQlService, $config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver\Email();
        $resolver->setGraphQLService($this->getGraphQlService());

        $this->buildBaseFields($config);
        $config['fields'] = array_merge($config['fields'], [
                'subject' => Type::string(),
                'from' => Type::string(),
                'replyTo' => Type::string(),
                'to' => Type::string(),
                'cc' => Type::string(),
                'bcc' => Type::string()
            ]
        );
    }
}
