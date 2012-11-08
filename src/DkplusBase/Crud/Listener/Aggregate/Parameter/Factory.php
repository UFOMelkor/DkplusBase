<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Crud\Listener\Aggregate\Parameter;

use DkplusBase\Crud\Listener\ListenerInterface;
use InvalidArgumentException;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Crud\Listener
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class Factory
{
    /**
     *
     * @param callable|ListenerInterface|ListenerAggregateInterface $listener
     * @param string|int $eventOrPriority
     * @param int $priority
     * @return ParameterInterface
     * @throws InvalidArgumentException on invalid listener
     */
    public function create($listener, $eventOrPriority = 1, $priority = 1)
    {
        if ($listener instanceof ListenerAggregateInterface) {
            return new AggregateParameter($listener, $eventOrPriority);
        }

        $parameter = new ListenerParameter($eventOrPriority, $priority);

        if ($listener instanceof ListenerInterface) {
            $parameter->setListener($listener);
        } elseif (\is_callable($listener)) {
            $parameter->setCallback($listener);
        } else {
            throw new InvalidArgumentException('First argument must be an valid listener');
        }
        return $parameter;
    }
}
