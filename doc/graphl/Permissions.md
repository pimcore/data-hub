## Permissions
## Add Custom Permissions

If you want to create custom permissions, you can create an event-listener for the event `Pimcore\Bundle\DataHubBundle\ConfigEvents::SPECIAL_ENTITIES` and add them:

The EventListener adds a new Entity of type `Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting` with the corresponding permissions it allows:

 - Read Possible
 - Create Possible
 - Update Possible
 - Delete Possible
 - Read Allowed
 - Create Allowed
 - Update Allowed
 - Delete Allowed

```php

use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\QueryTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\QueryEvents;
use Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphQlCustomListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::SPECIAL_ENTITIES => 'specialEntities'
        ];
    }

    public function specialEntities(SpecialEntitiesEvent $event)
    {
        $custom = new SpecialEntitySetting(
            'custom',
            true,
            true,
            true,
            false,
            $event->getConfig()['schema']['specialEntities']['custom']['read'] ?? false,
            $event->getConfig()['schema']['specialEntities']['custom']['crate'] ?? false,
            $event->getConfig()['schema']['specialEntities']['custom']['update'] ?? false,
            $event->getConfig()['schema']['specialEntities']['custom']['delete'] ?? false
        );

        $event->addSpecialSetting($custom);
    }
}
```
