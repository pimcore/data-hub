# Events

Datahub GraphQL events are based on the Symfony event dispatcher, and are triggered during execution of Query and Mutation requests.
[Read more](https://github.com/pimcore/pimcore/blob/master/doc/Development_Documentation/20_Extending_Pimcore/11_Event_API_and_Event_Manager.md) about events on Pimcore documentation.

All Datahub events are defined as a constant on component classes:
- [Query](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL/QueryEvents.php)
- [Mutation](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL/MutationEvents.php)
- [Executor](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL/ExecutorEvents.php)
- [Listing](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL/ListingEvents.php)
- [Ouput cache](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL/OutputCacheEvents.php)

## Event Subscriber Examples

With Symfony, you can listen to events using either [Event Listeners or Event Subscribers](https://symfony.com/doc/current/event_dispatcher.html).
Event Subscribers are used in the examples below.

Create a new class in `src/AppBundle/EventListener/GraphQlSubscriber.php` and use `autoconfigure: true` in your service configuration file 
(`app/config/services.yml` or `src/AppBundle/Resources/config/services.yml`)
to let Symfony automatically recognize it as an Event Subscriber,
either under `_defaults` or directly with the service:

```yml
services:
    _defaults: # Defaults for this file
        autoconfigure: true  # Let Symfony automatically configure Event Subscribers, Commands etc.
        # ...

    AppBundle\EventListener\:
        resource: '../../EventListener'
```

#### Example 1: Query & Mutation execution
```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ExecutorEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphQlSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ExecutorEvents::PRE_EXECUTE => 'onPreExecute', //Pre execute on Query & Mutation
            ExecutorEvents::POST_EXECUTE => 'onPostExecute' //Post execute on Query & Mutation
        ];
    }

    /**
     * @param ExecutorEvent $event
     */
    public function onPreExecute(ExecutorEvent $event)
    {
        // do something with the query or schema
        $query = $event->getQuery();
        $schema = $event->getSchema();
    }

    /**
     * @param ExecutorResultEvent $event
     */
    public function onPostExecute(ExecutorResultEvent $event)
    {
        // do something with output result
        $result = $event->getResult();
    }
}

```

#### Example 2: Bypass workspace permissions with Query/Mutation build events
```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\MutationEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\QueryTypeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphQlSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MutationEvents::PRE_BUILD => 'onMutationPreBuild',
            QueryEvents::PRE_BUILD => 'onQueryPreBuild'
        ];
    }

    /**
     * @param MutationTypeEvent $event
     */
    public function onMutationPreBuild(MutationTypeEvent $event)
    {
        $mutationType = $event->getMutationType();
        $mutationType->setOmitPermissionCheck(true); //omit permission check for mutations
    }

    /**
     * @param QueryTypeEvent $event
     */
    public function onQueryPreBuild(QueryTypeEvent $event)
    {
        $queryType = $event->getQueryType();
        $queryType->setOmitPermissionCheck(true); //omit permission check for queries
    }
}

```

#### Example 3: Add custom arguments to existing types
```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\MutationEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\MutationTypeEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\QueryTypeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use GraphQL\Type\Definition\Type;

class GraphQlSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MutationEvents::POST_BUILD => 'onMutationPostBuild',
            QueryEvents::POST_BUILD => 'onQueryPostBuild'
        ];
    }

    /**
     * @param MutationTypeEvent $event
     */
    public function onMutationPostBuild(MutationTypeEvent $event)
    {
        $config = $event->getConfig();
        
        //additional check for a field value
        $config['fields']['createProduct']['args']['foo'] = [
            'type' => Type::nonNull(Type::string())
        ];
        
        //additional checks for input fields
        $input = $config['fields']['createProduct']['args']['input'];
        $input->config['fields']['foo'] = [
            'type' => Type::nonNull(Type::string())
        ];
        $event->setConfig($config);
    }

    /**
     * @param QueryTypeEvent $event
     */
    public function onQueryPostBuild(QueryTypeEvent $event)
    {
        $config = $event->getConfig();
        $config['fields']['getProductListing']['args']['foo'] = [
            'type'  => Type::boolean()
        ];
        $event->setConfig($config);
    }
}

```

#### Example 4: Add custom query conditions to object listing

- For global SQL conditions also [General Settings](https://github.com/pimcore/data-hub/blob/master/doc/graphl/General.md#general-settings)
- For simple filter conditions also see [Filtering](https://github.com/pimcore/data-hub/blob/master/doc/graphl/Filtering.md#request)

```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Model\DataObject\Service;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ListingEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ListingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphQlSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ListingEvents::PRE_LOAD => 'onListingPreLoad',
        ];
    }

    /**
     * @param ListingEvent $event
     */
    public function onListingPreLoad(ListingEvent $event)
    {
        $listing = $event->getListing();

        if ($listing->getClassName() === 'Product') {
          $listing->setCondition(sprintf('(%s IN (SELECT %s FROM objects WHERE %s=\'object\') AND %s = \'variant\')',
          Service::getVersionDependentDatabaseColumnName('o_parentid'),
          Service::getVersionDependentDatabaseColumnName('o_id'),
          Service::getVersionDependentDatabaseColumnName('o_type'),
          Service::getVersionDependentDatabaseColumnName('o_type')));
        }

        $event->setListing($listing);
    }
}

```

#### Example 5: Add custom conditions to enable/disable output (responses) cache per request

- `OutputCacheEvents::PRE_LOAD`: is triggered before trying to load an entry from cache, if cache is enabled. You can disable the cache  for this request by setting `$event->setUseCache(false)`. If you disable the cache, the entry won't be loaded nor saved
- `OutputCacheEvents::PRE_SAVE`: if cache is enabled, it's triggered before saving an entry into the cache. You can use it to modify the response before it gets saved.

```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\OutputCachePreLoadEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\OutputCachePreSaveEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\OutputCacheEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphqlListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OutputCacheEvents::PRE_LOAD => 'onPreLoadCache',
            OutputCacheEvents::PRE_SAVE => 'onPreSaveCache'
        ];
    }

    /**
     * @param OutputCachePreLoadEvent $event
     */
    public function onPreLoadCache(OutputCachePreLoadEvent $event) 
    {
        $uri = $event->getRequest()->getMethod();
        
        if(str_contains($uri, "my-special-endpoint"))
        {
            $event->setUseCache(false);
        }
    }

    /**
     * @param OutputCachePreSaveEvent $event
     */
    public function onPreSaveCache(OutputCachePreSaveEvent $event) 
    {
        $uri = $event->getRequest()->getMethod();
        
        if(str_contains($uri, "my-awesome-endpoint"))
        {
            $response = $event->getResponse();
            // modify the response as you want it to be saved...

            $event->setResponse($response);
        }
    }
}
```
