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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\TranslationListing;
use Pimcore\Model\Translation;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventDispatcher;


class ResolveTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp(): void
    {
        $this->addTranslations();
    }

    public function testGraphQLTranslationListingResolveListing()
    {
        $service = \Pimcore::getContainer()->get("Pimcore\Bundle\DataHubBundle\GraphQL\Service");
        $translationListing = new TranslationListing($service, new EventDispatcher());
        $listRes = $translationListing->resolveListing([], []);

        for($i = 0; $i < 4; $i++) {
            $this->assertEquals('translation-k' .$i, $listRes['edges'][$i]['cursor']);

            $translation = $listRes['edges'][$i]['node'];
            $this->assertEquals('k' .$i, $translation->getKey());
            $this->assertEquals('dek' .$i, $translation->getTranslations()['de']);
            $this->assertEquals('enk' .$i, $translation->getTranslations()['en']);
        }
    }

    public function testGraphQLTranslationListingResolveListingWithDomain()
    {
        $service = \Pimcore::getContainer()->get("Pimcore\Bundle\DataHubBundle\GraphQL\Service");
        $translationListing = new TranslationListing($service, new EventDispatcher());
        $listRes = $translationListing->resolveListing([], ["domain" => "admin"]);

        for($i = 0; $i < 2; $i++) {
            $this->assertEquals('translation-ka' .$i, $listRes['edges'][$i]['cursor']);

            $translation = $listRes['edges'][$i]['node'];
            $this->assertEquals('ka' .$i, $translation->getKey());
            $this->assertEquals('deka' .$i, $translation->getTranslations()['de']);
            $this->assertEquals('enka' .$i, $translation->getTranslations()['en']);
        }
    }

    public function testGraphQLTranslationListingResolveListingWithKey()
    {
        $key = "k2";

        $service = \Pimcore::getContainer()->get("Pimcore\Bundle\DataHubBundle\GraphQL\Service");
        $translationListing = new TranslationListing($service, new EventDispatcher());
        $listRes = $translationListing->resolveListing([], ["keys" => $key]);

        $this->assertEquals('translation-' . $key, $listRes['edges'][0]['cursor']);

        $translation = $listRes['edges'][0]['node'];
        $this->assertEquals($key, $translation->getKey());
        $this->assertEquals('de' . $key, $translation->getTranslations()['de']);
        $this->assertEquals('en' . $key, $translation->getTranslations()['en']);
    }

    public function testGraphQLTranslationListingResolveListingWithKeys()
    {
        $keys = "k1,k2,k3";

        $service = \Pimcore::getContainer()->get("Pimcore\Bundle\DataHubBundle\GraphQL\Service");
        $translationListing = new TranslationListing($service, new EventDispatcher());
        $listRes = $translationListing->resolveListing([], ["keys" => $keys]);


        for($i = 0; $i < 2; $i++) {
            $this->assertEquals('translation-k' .$i + 1, $listRes['edges'][$i]['cursor']);

            $translation = $listRes['edges'][$i]['node'];
            $this->assertEquals('k' . $i + 1, $translation->getKey());
            $this->assertEquals('dek' . $i + 1, $translation->getTranslations()['de']);
            $this->assertEquals('enk' . $i + 1, $translation->getTranslations()['en']);
        }
    }

    private function addTranslations(): void {
        for($i = 0; $i < 4; $i++) {
            $this->addTranslation('k' . $i);
        }
        for($i = 0; $i < 2; $i++) {
            $this->addTranslation('ka' . $i, 'admin');
            $this->addTranslation('ka' . $i, 'admin');
        }
    }

    private function addTranslation(string $key, string $domain = "messages"): void {
        $t = new Translation();
        $t->setDomain($domain);
        $t->setKey($key);
        $t->setCreationDate(time());
        $t->setModificationDate(time());
        $t->setType($data['type'] ?? null);

        foreach (Tool::getValidLanguages() as $lang) {
            $t->addTranslation($lang, $lang . $key);
        }
        $t->save();

    }
}

