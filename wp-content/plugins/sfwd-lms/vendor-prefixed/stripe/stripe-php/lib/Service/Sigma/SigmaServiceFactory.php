<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\Sigma;

/**
 * Service factory class for API resources in the Sigma namespace.
 *
 * @property ScheduledQueryRunService $scheduledQueryRuns
 *
 * @license MIT
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class SigmaServiceFactory extends \StellarWP\Learndash\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'scheduledQueryRuns' => ScheduledQueryRunService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}