<?php
/**
 * @license MIT
 *
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\Stripe;

/**
 * Class OrderItem.
 *
 * @property string $object
 * @property int $amount
 * @property string $currency
 * @property string $description
 * @property string $parent
 * @property int $quantity
 * @property string $type
 */
class OrderItem extends StripeObject
{
    const OBJECT_NAME = 'order_item';
}
