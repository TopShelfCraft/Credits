<?php

namespace topshelfcraft\credits\models;

use Craft;
use craft\base\Model;
use craft\db\Query;
use topshelfcraft\credits\records\Transaction;
use yii\base\InvalidArgumentException;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 */
class Account extends Model
{

	/*
	 * Public properties
	 */

	/**
	 * @var int $id
	 */
	public $id;

	/**
	 * @var string $reference
	 */
	public $reference;

	/**
	 * @var string $name
	 */
	public $name;

	/**
	 * @var int|null $ownerId
	 */
	public $ownerId;

	/**
	 * @var string $note
	 */
	public $note;

	/**
	 * @var string $dateCreated
	 */
	public $dateCreated;

	/**
	 * @var string $dateUpdated
	 */
	public $dateUpdated;

	/**
	 * @var string $uid
	 */
	public $uid;

	/*
	 * Protected properties
	 */

	protected $_balance;

    /*
     * Public methods
     */

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

	/**
	 * @return \craft\elements\User|null
	 */
    public function getOwner()
	{
		if (!empty($this->ownerId))
		{
			return Craft::$app->getUsers()->getUserById($this->ownerId);
		}
	}

	/**
	 * @return float
	 */
	public function getBalance()
	{
		if (!$this->id)
		{
			return null;
		}
		$query = (new Query())
			->select('SUM(amount)')
			->from(Transaction::tableName())
			->groupBy('accountId')
			->where(['accountId' => $this->id]);
		return floatval($query->scalar());
	}

	/**
	 * @param float $amount
	 * @param array $config
	 *
	 * @return Transaction|false
	 */
	public function enterTransaction($amount, $config = [])
	{

		$config['accountId'] = $this->id;
		$config['amount'] = $amount;

		if (empty($config['userId']) && Craft::$app->getUser()->getId())
		{
			$config['userId'] = Craft::$app->getUser()->getId();
		}

		$transaction = new Transaction($config);
		if ($transaction->save())
		{
			return $transaction;
		}
		else
		{
			return false;
		}

	}

	/**
	 * @param float $amount
	 * @param array $config
	 *
	 * @return Transaction|false
	 */
	public function credit($amount, $config = [])
	{

		// Make sure the amount is positive
		$amount = floatval($amount);
		if ($amount < 0)
		{
			throw new InvalidArgumentException("The amount of the `credit()` should be positive. The amount will be added to the account.");
		}

		$config['type'] = Transaction::TYPE_CREDIT;

		return $this->enterTransaction($amount, $config);

	}

	/**
	 * @param float $amount
	 * @param array $config
	 *
	 * @return Transaction|false
	 */
	public function debit($amount, $config = [])
	{

		// Make sure the amount is positive
		$amount = floatval($amount);
		if ($amount < 0)
		{
			throw new InvalidArgumentException("The amount of the `debit()` should be positive. The amount will be subtracted from the account.");
		}

		$config['type'] = Transaction::TYPE_DEBIT;

		return $this->enterTransaction(-$amount, $config);

	}

	/**
	 * @param float $amount
	 * @param array $config
	 *
	 * @return Transaction|false
	 */
	public function adjust($amount, $config = [])
	{

		$amount = floatval($amount);

		$config['type'] = Transaction::TYPE_ADJUSTMENT;

		return $this->enterTransaction($amount, $config);

	}

}
