<?php
/**
 * @license MIT
 *
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\Learndash\Stripe\Exception;

/**
 * PermissionException is thrown in cases where access was attempted on a
 * resource that wasn't allowed.
 */
class PermissionException extends ApiErrorException
{
}
