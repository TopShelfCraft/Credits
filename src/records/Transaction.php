<?php

namespace topshelfcraft\credits\records;

use craft\db\ActiveRecord;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 *
 * @property int $id
 * @property int $parentId
 * @property int $accountId
 * @property float $amount
 * @property string $type
 * @property int $userId
 * @property string $note
 * @property string $hash
 */
class Transaction extends ActiveRecord
{

	const TABLE_NAME = 'credits_transactions';

	const TYPE_CREDIT = 'credit';
	const TYPE_DEBIT = 'debit';
	const TYPE_ADJUSTMENT = 'adjustment';

	/*
	 * Static methods
	 */

	/**
	 * @return string
	 */
	public static function tableName()
	{
		return '{{%' . static::TABLE_NAME . '}}';
	}

	/**
	 * Generates the Transaction hash.
	 * (We only do this for new records. We'll use the hash later as a transaction reference in our Gateway.)
	 *
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert))
		{
			if ($this->getIsNewRecord())
			{
				$this->hash = md5(join($this->getAttributes(), '/'));
			}
			return true;
		}
		return false;
	}

}
