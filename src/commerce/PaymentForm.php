<?php

namespace topshelfcraft\credits\commerce;

use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\PaymentSource;


class PaymentForm extends BasePaymentForm
{

	/*
	 * Properties
	 * ----------------------------------------------------------------
	 */

	/**
	 * @var string Account ID
	 */
	public $accountId;

	/**
	 * @var string Account reference
	 */
	public $accountReference;

	/**
	 * @var string Token
	 */
	public $token;


	/*
	 * Public methods
	 * ----------------------------------------------------------------
	 */

	/**
	 * @param PaymentSource $paymentSource
	 */
	public function populateFromPaymentSource(PaymentSource $paymentSource)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function setAttributes($values, $safeOnly = true)
	{

		parent::setAttributes($values, $safeOnly);

		// TODO: remove spaces from reference (?)
		// $this->number = preg_replace('/\D/', '', $values['accountReference'] ?? '');

	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			// [['accountReference', 'accountId'], 'required'],
			[['accountId'], 'integer', 'integerOnly' => true],
			[['accountReference'], 'string'],
		];
	}

}
