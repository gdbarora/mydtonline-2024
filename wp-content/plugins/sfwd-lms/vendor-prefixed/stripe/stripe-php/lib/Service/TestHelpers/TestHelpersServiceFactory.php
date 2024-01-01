<?php

// File generated from our OpenAPI spec

namespace StellarWP\Learndash\Stripe\Service\TestHelpers;

/**
 * Service factory class for API resources in the TestHelpers namespace.
 *
 * @property RefundService $refunds
 * @property Terminal\TerminalServiceFactory $terminal
 * @property TestClockService $testClocks
 *
 * @license MIT
 * Modified by learndash on 09-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */
class TestHelpersServiceFactory extends \StellarWP\Learndash\Stripe\Service\AbstractServiceFactory
{
    /**
     * @var array<string, string>
     */
    private static $classMap = [
        'refunds' => RefundService::class,
        'terminal' => Terminal\TerminalServiceFactory::class,
        'testClocks' => TestClockService::class,
    ];

    protected function getServiceClass($name)
    {
        return \array_key_exists($name, self::$classMap) ? self::$classMap[$name] : null;
    }
}
