<?php

namespace topshelfcraft\credits\commerce;

use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\NotImplementedException;


class RequestResponse implements RequestResponseInterface
{

	private $_success;

	private $_data;

	/*
	 * Public methods
	 * ----------------------------------------------------------------
	 */

	/**
	 * @param bool $success
	 * @param array $data
	 */
	public function __construct($success = false, $data = [])
	{
		$this->_success = $success;
		$this->_data = $data;
	}

	/**
	 * @inheritdoc
	 */
	public function isSuccessful(): bool
	{
		return $this->_success;
	}

	/**
	 * @inheritdoc
	 */
	public function isRedirect(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function getRedirectMethod(): string
	{
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getRedirectData(): array
	{
		return [];
	}

	/**
	 * @inheritdoc
	 */
	public function getRedirectUrl(): string
	{
		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getTransactionReference(): string
	{
		return !empty($this->_data['transactionReference']) ? $this->_data['transactionReference'] : '';
	}

	/**
	 * @inheritdoc
	 */
	public function getCode(): string
	{
		// e.g. 'payment.failed'
		return !empty($this->_data['code']) ? $this->_data['code'] : '';
	}

	/**
	 * @inheritdoc
	 */
	public function getMessage(): string
	{
		// e.g. "Payment failed."
		return !empty($this->_data['message']) ? $this->_data['message'] : '';
	}

	/**
	 * @inheritdoc
	 */
	public function redirect()
	{
		throw new NotImplementedException('Redirecting directly is not implemented for this gateway.');
	}

	/**
	 * @inheritdoc
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @inheritdoc
	 */
	public function isProcessing(): bool
	{
		return false;
	}

}
