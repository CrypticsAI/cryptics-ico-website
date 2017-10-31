<?php
namespace WpPepVN\Event;

use WpPepVN\Event\ManagerInterface
;

/**
 * WpPepVN\Event\EventsAwareInterface
 *
 * This interface must for those classes that accept an EventsManager and dispatch events
 */
interface EventsAwareInterface
{

	/**
	 * Sets the events manager
	 */
	public function setEventsManager(ManagerInterface $eventsManager);

	/**
	 * Returns the internal event manager
	 */
	public function getEventsManager();

}
