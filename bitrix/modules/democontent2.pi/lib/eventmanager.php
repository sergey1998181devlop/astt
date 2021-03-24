<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 07.01.2019
 * Time: 16:02
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi;

use Bitrix\Main\Event;
use Democontent2\Pi\I\IEventManager;

class EventManager implements IEventManager
{
    private $eventName = '';
    private $params = [];

    /**
     * EventManager constructor.
     * @param string $eventName
     * @param array $params
     */
    public function __construct($eventName, array $params)
    {
        $this->eventName = $eventName;
        $this->params = $params;
    }

    public function execute()
    {
        if ($this->eventName && count($this->params) > 0) {
            $event = new Event(
                DSPI,
                $this->eventName,
                $this->params
            );
            $event->send();
        }

        return;
    }
}