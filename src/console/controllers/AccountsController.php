<?php

namespace topshelfcraft\credits\console\controllers;

use topshelfcraft\credits\Credits;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 */
class AccountsController extends Controller
{

	/*
	 * Public properties
	 */


	/**
	 * @var string $defaultAction
	 */
	public $defaultAction = 'create';

	/**
	 * @var int $user
	 */
	public $user;

	/**
	 * @var string $ref
	 */
	public $reference;

	/**
	 * @var float $amount
	 */
	public $amount;

	/**
	 * @var int $accountId
	 */
	public $accountId;

	/**
	 * @var string $note
	 */
	public $note;


	/*
	 * Public methods
	 */


	/**
	 * @inheritdoc
	 */
	public function options($actionId): array
	{
		$options = parent::options($actionId);
		$options[] = 'accountId';
		$options[] = 'amount';
		$options[] = 'note';
		$options[] = 'reference';
		$options[] = 'user';
		return $options;
	}

	/**
	 * @throws \Exception
	 */
	public function actionCreate()
	{

		$config = [];

		if ($this->user)
		{
			$config['ownerId'] = $this->user;
		}

		if ($this->reference)
		{
			$config['reference'] = $this->reference;
		}

		$amount = (float) $this->amount;

		$account = Credits::$plugin->accounts->createAccount($config, $amount);

		Console::output("Created account {$account->id} ({$account->reference}) with starting balance: {$amount}");
		return ExitCode::OK;

	}

	/**
	 * @throws \Exception
	 */
	public function actionCheckBalance()
	{

		$account = $this->_findAccountFromParams();

		if (empty($account))
		{
			Console::output("Could not find account.");
			return ExitCode::UNSPECIFIED_ERROR;
		}

		Console::output("Account {$account->id} ({$account->reference}) has current balance: {$account->getBalance()}");
		return ExitCode::OK;

	}

	/**
	 * @throws \Exception
	 */
	public function actionCredit()
	{

		$account = $this->_findAccountFromParams();

		if (empty($account))
		{
			Console::output("Could not find account.");
			return ExitCode::UNSPECIFIED_ERROR;
		}

		try
		{
			$amount = (float) $this->amount;
			$account->credit($amount, ['note' => $this->note]);
			Console::output("Credit to account {$account->id} ({$account->reference}): {$amount} credits");
			return ExitCode::OK;
		}
		catch (\Exception $e)
		{
			$this->_writeErr($e->getMessage());
		}

		return ExitCode::UNSPECIFIED_ERROR;

	}

	/**
	 * @throws \Exception
	 */
	public function actionDebit()
	{

		$account = $this->_findAccountFromParams();

		if (empty($account))
		{
			Console::output("Could not find account.");
			return ExitCode::UNSPECIFIED_ERROR;
		}

		try
		{
			$amount = (float) $this->amount;
			$account->debit($amount, ['note' => $this->note]);
			Console::output("Debit from account {$account->id} ({$account->reference}): {$amount} credits");
			return ExitCode::OK;
		}
		catch (\Exception $e)
		{
			$this->_writeErr($e->getMessage());
		}

		return ExitCode::UNSPECIFIED_ERROR;

	}

	/**
	 * @throws \Exception
	 */
	public function actionAdjust()
	{

		$account = $this->_findAccountFromParams();

		if (empty($account))
		{
			Console::output("Could not find account.");
			return ExitCode::UNSPECIFIED_ERROR;
		}

		try
		{
			$amount = (float) $this->amount;
			$account->adjust($amount, ['note' => $this->note]);
			Console::output("Adjustment on account {$account->id} ({$account->reference}): {$amount} credits");
			return ExitCode::OK;
		}
		catch (\Exception $e)
		{
			$this->_writeErr($e->getMessage());
		}

		return ExitCode::UNSPECIFIED_ERROR;

	}


	/*
	 * Private methods
	 */

	private function _findAccountFromParams()
	{

		if ($this->accountId)
		{
			$account = Credits::$plugin->accounts->getAccountById($this->accountId);
		}
		elseif ($this->reference)
		{
			$account = Credits::$plugin->accounts->getAccountByReference($this->reference);
		}
		elseif ($this->user)
		{
			$account = Credits::$plugin->accounts->getFirstAccountByOwner($this->user);
		}

		return $account;

	}



	/**
	 * Writes an error to console
	 * @param string $msg
	 */
	private function _writeErr($msg)
	{
		$this->stderr('Error', Console::BOLD, Console::FG_RED);
		$this->stderr(': ', Console::FG_RED);
		$this->stderr($msg . PHP_EOL);
	}


}
