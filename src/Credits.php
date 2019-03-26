<?php
/**
 * @link      https://topshelfcraft.com
 * @copyright Copyright (c) 2018 Top Shelf Craft (Michael Rog)
 */

namespace topshelfcraft\credits;

use craft\base\Plugin;
use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;
use topshelfcraft\credits\commerce\Gateway;
use topshelfcraft\credits\services\Accounts as AccountsService;
use topshelfcraft\credits\services\Transactions as TransactionsService;
use topshelfcraft\credits\variables\CreditsVariable;
use yii\base\Event;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 *
 * @property  AccountsService $accounts
 * @property  TransactionsService $transactions
 */
class Credits extends Plugin
{

	/*
	 * Static properties
	 */

    /**
     * @var Credits
     */
    public static $plugin;

    /*
     * Public properties
     */

    /**
     * @var string
     */
    public $schemaVersion = '3.0.0.0';

    /*
     * Public methods
     */

    /**
	 *
     */
    public function init()
    {

        self::$plugin = $this;

		parent::init();

		Event::on(Gateways::class, Gateways::EVENT_REGISTER_GATEWAY_TYPES, function(RegisterComponentTypesEvent $e) {
			$e->types[] = Gateway::class;
		});

		Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
			/** @var CraftVariable $craftVariable */
			$craftVariable = $e->sender;
			$craftVariable->set('credits', CreditsVariable::class);
		});

    }

}
