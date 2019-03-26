<?php

namespace topshelfcraft\credits\records;

use craft\db\ActiveRecord;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 *
 * @property int $id
 * @property string $reference
 * @property string $name
 * @property int $ownerId
 * @property string $note
 */
class Account extends ActiveRecord
{

	const TABLE_NAME = 'credits_accounts';

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
	 * If an Account reference isn't already set, provide a normalized default.
	 *
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if (parent::beforeSave($insert))
		{
			if (empty($this->reference))
			{
				$this->reference = md5(join($this->getAttributes(), '/'));
			}
			return true;
		}
		return false;
	}

}
