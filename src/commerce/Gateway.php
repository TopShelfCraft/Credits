<?php

namespace topshelfcraft\credits\commerce;

use Craft;
use craft\commerce\base\Gateway as BaseGateway;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\NotImplementedException;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\PaymentSource;
use craft\commerce\models\Transaction;
use craft\web\Response as WebResponse;
use craft\web\View;
use topshelfcraft\credits\Credits;
use topshelfcraft\credits\models\Account;


class Gateway extends BaseGateway
{

	/*
	 * Public methods
	 */

	/**
	 * @inheritdoc
	 */
	public function getPaymentFormHtml(array $params)
	{

		$view = Craft::$app->getView();

		$defaults = [
			'paymentForm' => $this->getPaymentFormModel()
		];
		$params = array_merge($defaults, $params);

		$previousMode = $view->getTemplateMode();
		$view->setTemplateMode(View::TEMPLATE_MODE_CP);
		$html = Craft::$app->getView()->renderTemplate('credits/_components/commerce/_gatewayFields', $params);
		$view->setTemplateMode($previousMode);

		return $html;

	}

	/**
	 * @inheritdoc
	 */
	public static function displayName(): string
	{
		return "Credits";
	}

	/**
	 * @inheritdoc
	 */
	public function getPaymentFormModel(): BasePaymentForm
	{
		return new PaymentForm();
	}

	/**
	 * @inheritdoc
	 */
	public function authorize(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function capture(Transaction $transaction, string $reference): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function completeAuthorize(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function completePurchase(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function createPaymentSource(BasePaymentForm $sourceData, int $userId): PaymentSource
	{
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function deletePaymentSource($token): bool
	{
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function purchase(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
	{

		$user = Craft::$app->getUser();

		// Fetch the Credits account
		/** @var PaymentForm $form */

		if ($form->accountId)
		{
			$account = Credits::$plugin->accounts->getAccountById($form->accountId);
		}
		elseif ($form->accountReference)
		{
			$account = Credits::$plugin->accounts->getAccountByReference($form->accountReference);
		}

		// Make sure the account is valid...

		if (empty($account))
		{
			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => 'Payment failed because the Credits account could not be found.'
			]);
		}
		/** @var Account $account */

		// Make sure we have a logged in user.

		if (!$user->getId())
		{
			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => 'Payment failed because the User is not logged in.'
			]);
		}

		// Is the current user the owner, or an admin?

		if (! ((int)$account->ownerId === (int)$user->getId() || $user->getIsAdmin()))
		{
			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => 'Payment failed because the User is not the owner of the Credits account.'
			]);
		}

		// Check the current balance.

		if ($account->getBalance() < $transaction->paymentAmount)
		{
			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => 'Payment failed because the Credits account does not have sufficient balance.'
			]);
		}

		// Make it rain.

		$debit = $account->debit($transaction->paymentAmount, [
			'note' => Craft::t('commerce', 'Order') . " #" . $transaction->orderId
		]);

		// Did it work?

		if (!$debit)
		{
			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => 'Payment failed because the Transaction could not be saved.'
			]);
		}

		// It worked!

		return new RequestResponse(true, [
			'transactionReference' => $debit->hash
		]);

	}

	/**
	 * @inheritdoc
	 */
	public function processWebHook(): WebResponse
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function refund(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function supportsAuthorize(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCapture(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCompleteAuthorize(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCompletePurchase(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPaymentSources(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPurchase(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsRefund(): bool
	{
		// TODO: Implement
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPartialRefund(): bool
	{
		// TODO: Implement
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsWebhooks(): bool
	{
		return false;
	}

}
