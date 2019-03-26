<?php

namespace topshelfcraft\credits\migrations;

use Craft;
use craft\db\Migration;
use topshelfcraft\credits\records\Account;
use topshelfcraft\credits\records\Transaction;


/**
 * @author Michael Rog <michael@michaelrog.com>
 * @package Credits
 * @since 3.0.0
 */
class Install extends Migration
{

    /*
     * Public methods
     */

    /**
	 * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->createTables())
        {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
        }

        return true;

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->removeTables();

        return true;

    }

    /*
     * Protected methods
     */

    /**
     * @return bool
     */
    protected function createTables()
    {

		// Accounts table

		if (!$this->db->tableExists(Account::tableName())) {

			$this->createTable(Account::tableName(), [

				'id' => $this->primaryKey(),

				'reference' => $this->string(),
				'name' => $this->string(),
				'ownerId' => $this->integer()->unsigned(),
				'note' => $this->text(),

				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid' => $this->uid(),

			]);

		}

        // Transactions table

		if (!$this->db->tableExists(Transaction::tableName())) {

			$this->createTable(Transaction::tableName(), [

				'id' => $this->primaryKey(),
				'parentId' => $this->integer()->unsigned(),

				'accountId' => $this->integer()->unsigned()->notNull(),
				'amount' => $this->decimal(14, 4)->notNull(),
				'type' => $this->enum('type', [
					Transaction::TYPE_CREDIT,
					Transaction::TYPE_DEBIT,
					Transaction::TYPE_ADJUSTMENT]
				),
				'userId' => $this->integer()->unsigned(),
				'note' => $this->text(),

				'hash' => $this->string(32),
				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid' => $this->uid(),

			]);

		}

        return true;

    }

    /**
     * @return void
     */
    protected function createIndexes()
    {

		// Make sure Account refs are unique
		$this->createIndex(null, Account::tableName(), ['reference'], true);

    	// Index Transaction accountId so it's fast to search
		$this->createIndex(null, Transaction::tableName(), ['accountId'], false);

		// Index Transaction amount so it's easy to SUM()
		$this->createIndex(null, Transaction::tableName(), ['amount'], false);

    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {

    	// TODO: Link Account ownerId to User id

		// TODO: Link Transaction userId to User id

		// TODO: Link Transaction parentId to Transaction id

		// TODO: Link Transaction accountId to Account id

		// TODO: Link Transaction userId to User id

    }

    /**
     * Populates the new tables with default data
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    	// No default data to insert
    }

    /**
     * @return void
     */
    protected function removeTables()
    {

    	// credits_accounts table
        $this->dropTableIfExists(Account::tableName());

    	// credits_accounttransactions table
		$this->dropTableIfExists(Transaction::tableName());

    }

}
