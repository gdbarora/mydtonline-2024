<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe;

/**
 * PaymentMethod objects represent your customer's payment instruments. You can use
 * them with <a
 * href="https://stripe.com/docs/payments/payment-intents">PaymentIntents</a> to
 * collect payments or save them to Customer objects to store instrument details
 * for future payments.
 *
 * Related guides: <a
 * href="https://stripe.com/docs/payments/payment-methods">Payment Methods</a> and
 * <a href="https://stripe.com/docs/payments/more-payment-scenarios">More Payment
 * Scenarios</a>.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property \StellarWP\Learndash\Stripe\StripeObject $acss_debit
 * @property \StellarWP\Learndash\Stripe\StripeObject $afterpay_clearpay
 * @property \StellarWP\Learndash\Stripe\StripeObject $alipay
 * @property \StellarWP\Learndash\Stripe\StripeObject $au_becs_debit
 * @property \StellarWP\Learndash\Stripe\StripeObject $bacs_debit
 * @property \StellarWP\Learndash\Stripe\StripeObject $bancontact
 * @property \StellarWP\Learndash\Stripe\StripeObject $billing_details
 * @property \StellarWP\Learndash\Stripe\StripeObject $boleto
 * @property \StellarWP\Learndash\Stripe\StripeObject $card
 * @property \StellarWP\Learndash\Stripe\StripeObject $card_present
 * @property int $created Time at which the object was created. Measured in seconds since the Unix epoch.
 * @property null|string|\StellarWP\Learndash\Stripe\Customer $customer The ID of the Customer to which this PaymentMethod is saved. This will not be set when the PaymentMethod has not been saved to a Customer.
 * @property \StellarWP\Learndash\Stripe\StripeObject $customer_balance
 * @property \StellarWP\Learndash\Stripe\StripeObject $eps
 * @property \StellarWP\Learndash\Stripe\StripeObject $fpx
 * @property \StellarWP\Learndash\Stripe\StripeObject $giropay
 * @property \StellarWP\Learndash\Stripe\StripeObject $grabpay
 * @property \StellarWP\Learndash\Stripe\StripeObject $ideal
 * @property \StellarWP\Learndash\Stripe\StripeObject $interac_present
 * @property \StellarWP\Learndash\Stripe\StripeObject $klarna
 * @property \StellarWP\Learndash\Stripe\StripeObject $konbini
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property null|\StellarWP\Learndash\Stripe\StripeObject $metadata Set of <a href="https://stripe.com/docs/api/metadata">key-value pairs</a> that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 * @property \StellarWP\Learndash\Stripe\StripeObject $oxxo
 * @property \StellarWP\Learndash\Stripe\StripeObject $p24
 * @property \StellarWP\Learndash\Stripe\StripeObject $paynow
 * @property \StellarWP\Learndash\Stripe\StripeObject $sepa_debit
 * @property \StellarWP\Learndash\Stripe\StripeObject $sofort
 * @property string $type The type of the PaymentMethod. An additional hash is included on the PaymentMethod with a name matching this value. It contains additional information specific to the PaymentMethod type.
 * @property \StellarWP\Learndash\Stripe\StripeObject $us_bank_account
 * @property \StellarWP\Learndash\Stripe\StripeObject $wechat_pay
 *
 * @license MIT
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class PaymentMethod extends ApiResource
{
    const OBJECT_NAME = 'payment_method';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\PaymentMethod the attached payment method
     */
    public function attach($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/attach';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }

    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\PaymentMethod the detached payment method
     */
    public function detach($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/detach';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
