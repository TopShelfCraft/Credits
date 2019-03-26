<?php

namespace topshelfcraft\credits\services;

use Craft;
use craft\base\Component;
use craft\elements\User;
use topshelfcraft\credits\models\Account as AccountModel;
use topshelfcraft\credits\records\Account as AccountRecord;
use yii\base\Exception;
use yii\base\InvalidArgumentException;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 */
class Accounts extends Component
{

	/*
	 * Public methods
	 */

	/**
	 * @param array $config
	 * @param float $startingBalance
	 *
	 * @return AccountModel
	 * @throws Exception
	 */
    public function createAccount($config = [], $startingBalance = 0.0)
    {

		$account = new AccountModel($config);

		$transaction = Craft::$app->getDb()->beginTransaction();

		try {

			if (!$this->saveAccount($account))
			{
				throw new Exception("Could not create the account.");
			}

			if (!empty($startingBalance))
			{
				if (!$account->adjust($startingBalance, ['note' => "Account created with opening balance: {$startingBalance}"]))
				{
					throw new Exception("Could not apply the starting balance.");
				}
			}

			$transaction->commit();

		} catch (Exception $e) {
			$transaction->rollBack();
			throw $e;
		}

    	return $account;

    }

	/**
	 * @param AccountModel|AccountRecord $account
	 * @param bool $runValidation
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function saveAccount($account, $runValidation = true)
	{

		if ($runValidation && !$account->validate()) {
			Craft::info('Account not saved due to validation error.', __METHOD__);
			return false;
		}

		if ($account instanceof AccountModel)
		{
			$record = $account->id ? AccountRecord::findOne($account->id) : new AccountRecord();
			// At this point, $record should be an AccountRecord instance. If it's null, it means `findOne` failed.
			if (!$record)
			{
				throw new Exception("No account exists with ID {$account->id}.");
			}
			$record->setAttributes($account->getAttributes(), false);
		}
		elseif ($account instanceof AccountRecord)
		{
			$record = $account;
		}
		else
		{
			throw new InvalidArgumentException("saveAccount() requires an instance of an Account model or record.");
		}

		$saved = $record->save($runValidation);

		if ($saved)
		{
			// Update the original record/model with the attributes of the newly saved record.
			// (In most cases, we'll just be interested in the ID that gets set on a newly created account.)
			$account->setAttributes($record->getAttributes(), false);
		}
		else
		{
			// TODO: Handle errors
		}

		return $saved;

	}

	/**
	 * @param int $id
	 *
	 * @return AccountModel|null
	 */
	public function getAccountById($id)
	{
		$record = AccountRecord::findOne($id);
		return $record ? new AccountModel($record) : null;
	}

	/**
	 * @param string $reference
	 *
	 * @return AccountModel|null
	 */
	public function getAccountByReference($reference)
	{
		if (empty($reference))
		{
			return null;
		}
		$record = AccountRecord::findOne(['reference' => $reference]);
		return $record ? new AccountModel($record) : null;
	}

	/**
	 * Finds all accounts for the given owner.
	 *
	 * @param User|int $owner
	 *
	 * @return AccountModel[]
	 */
	public function getAccountsByOwner($owner)
	{

		if (empty($owner))
		{
			return [];
		}
		if ($owner instanceof User)
		{
			$owner = $owner->id;
		}

		$records = AccountRecord::findAll(['ownerId' => $owner]);
		$results = [];

		// TODO: Is there a more succinct way to cast records into models?
		foreach ($records as $record)
		{
			$results[] = new AccountModel($record);
		}

		return $results;

	}

	/**
	 * Finds the first account found for the given owner.
	 *
	 * @param User|int $owner
	 *
	 * @return AccountModel
	 */
	public function getFirstAccountByOwner($owner)
	{

		$accounts = $this->getAccountsByOwner($owner);

		if (!empty($accounts))
		{
			return $accounts[0];
		}

		return null;

	}

	/**
	 * @param $id
	 *
	 * @return float|null
	 */
	public function checkAccountBalanceById($id)
	{
		$account = $this->getAccountById($id);
		if (empty($account))
		{
			return null;
		}
		return $account->getBalance();
	}

	/**
	 * @param $reference
	 *
	 * @return float|null
	 */
	public function checkAccountBalanceByReference($reference)
	{
		$account = $this->getAccountByReference($reference);
		if (empty($account))
		{
			return null;
		}
		return $account->getBalance();
	}

}
