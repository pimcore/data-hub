# Custom Permissions

When creating custom queries or creating other custom extensions to Datahub, it might be useful to add additional permissions to define access rules for certain data entities (like it is possible to define access for Documents, Assets, etc.). 

## Adding Custom Permissions 

If you want to create custom permissions, you can create an event-listener for the event 
`Pimcore\Bundle\DataHubBundle\ConfigEvents::SPECIAL_ENTITIES` and add them as follows. 

The EventListener adds a new Entity of type `Pimcore\Bundle\DataHubBundle\Model\SpecialEntitySetting` 
with the corresponding permissions it allows:

 - Read Possible: defines if `read` checkbox is available
 - Create Possible: defines if `create` checkbox is available
 - Update Possible: defines if `update` checkbox is available
 - Delete Possible: defines if `delete` checkbox is available
 - Read Allowed: define default value for `read`
 - Create Allowed: define default value for `create`
 - Update Allowed: define default value for `update`
 - Delete Allowed: define default value for `delete`


#### Sample Implementation
 
```php

use Pimcore\Bundle\DataHubBundle\ConfigEvents;
use Pimcore\Bundle\DataHubBundle\Event\Config\SpecialEntitiesEvent;
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
