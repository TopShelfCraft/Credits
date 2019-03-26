<?php

namespace topshelfcraft\credits\variables;

use topshelfcraft\credits\Credits;
use topshelfcraft\credits\services\Accounts;

/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 */
class CreditsVariable
{

	/**
	 * @return Accounts
	 */
	public function getAccounts()
	{
		return Credits::$plugin->accounts;
	}

}
