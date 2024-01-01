<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service;

class OrderReturnService extends \StellarWP\Learndash\Stripe\Service\AbstractService
{
    /**
     * Returns a list of your order returns. The returns are returned sorted by
     * creation date, with the most recently created return appearing first.
     *
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\Collection<\Stripe\OrderReturn>
     *
     * @license MIT
     * Modified by learndash on 09-November-2023 using Strauss.
     * @see https://github.com/BrianHenryIE/strauss
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/order_returns', $params, $opts);
    }

    /**
     * Retrieves the details of an existing order return. Supply the unique order ID
     * from either an order return creation request or the order return list, and
     * Stripe will return the corresponding order information.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\StellarWP\Learndash\Stripe\Util\RequestOptions $opts
     *
     * @throws \StellarWP\Learndash\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \StellarWP\Learndash\Stripe\OrderReturn
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/order_returns/%s', $id), $params, $opts);
    }
}
