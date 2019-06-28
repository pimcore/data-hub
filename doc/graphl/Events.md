#Events

DataHub GraphQL events are based on symfony event dispatcher, which are triggered during execution of Query and Mutation requests.
[Read more](https://github.com/pimcore/pimcore/blob/master/doc/Development_Documentation/20_Extending_Pimcore/11_Event_API_and_Event_Manager.md) about events on Pimcore documentation.

All DataHub events are defined as a constant on component classes:
- [Query](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL\QueryEvents.php)
- [Mutation](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL\MutationEvents.php)
- [Executor](https://github.com/pimcore/data-hub/blob/master/src/Event/GraphQL\ExecutorEvents.php)

## Event Listener examples

Add configuration in your `app/config/services.yml`:
```yml
services:
    AppBundle\EventListener\GraphQlListener:
        tags:
            - { name: kernel.event_listener}
```

Add Listener class `src/AppBundle/EventListener/GraphQlListener`

#### Example 1: Query & Mutation execution
```php
<?php

namespace AppBundle\EventListener;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ExecutorEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GraphQlListener implements EventSubscriberInterface
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

class GraphqlListener implements EventSubscriberInterface
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
     * @param ExecutorEvent $event
     */
    public function onMutationPreBuild(MutationTypeEvent $event)
    {
        $mutationType = $event->getMutationType();
        $mutationType->setOmitPermissionCheck(true); //omit permission check for mutations
    }

    /**
     * @param ExecutorResultEvent $event
     */
    public function onQueryPreBuild(QueryTypeEvent $event)
    {
        $queryType = $event->getQueryType();
        $queryType->setOmitPermissionCheck(true); //omit permission check for queries
    }
}

```