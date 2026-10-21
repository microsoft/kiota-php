<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */

namespace Microsoft\Kiota\Http\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use Microsoft\Kiota\Http\Middleware\Options\ObservabilityOption;
use Microsoft\Kiota\Http\Middleware\Options\SunsetOption;
use OpenTelemetry\API\Trace\TracerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SunsetHandler
 *
 * Middleware that detects the Sunset and Link (rel="sunset") headers in responses
 * and logs an OpenTelemetry event to warn the application that the API is deprecated.
 *
 * @package Microsoft\Kiota\Http\Middleware
 * @copyright 2024 Microsoft Corporation
 * @license https://opensource.org/licenses/MIT MIT License
 */
class SunsetHandler
{
    public const HANDLER_NAME = 'kiotaSunsetHandler';
    private const SPAN_NAME = 'SunsetHandler_intercept';
    private const HANDLER_ENABLED_KEY = 'com.microsoft.kiota.handler.sunset.enable';
    private const EVENT_NAME = 'com.microsoft.kiota.sunset_header_received';

    private SunsetOption $sunsetOption;

    /**
     * @var TracerInterface
     */
    private TracerInterface $tracer;

    /**
     * @var callable(RequestInterface, array<string,mixed>): PromiseInterface $nextHandler
     */
    private $nextHandler;

    /**
     * @param callable(RequestInterface, array<string,mixed>): PromiseInterface $nextHandler
     * @param SunsetOption|null $sunsetOption
     */
    public function __construct(callable $nextHandler, ?SunsetOption $sunsetOption = null)
    {
        $this->nextHandler = $nextHandler;
        $this->sunsetOption = $sunsetOption ?: new SunsetOption();
        $this->tracer = ObservabilityOption::getTracer();
    }

    /**
     * @param RequestInterface $request
     * @param array<string, mixed> $options
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $span = $this->tracer->spanBuilder(self::SPAN_NAME)->startSpan();
        $scope = $span->activate();

        try {
            // Use a local variable to avoid cross-request state leakage
            $sunsetOption = $this->sunsetOption;
            if (array_key_exists(SunsetOption::class, $options) &&
                $options[SunsetOption::class] instanceof SunsetOption) {
                $sunsetOption = $options[SunsetOption::class];
            }

            $span->setAttribute(self::HANDLER_ENABLED_KEY, $sunsetOption->getEnabled());

            $fn = $this->nextHandler;
            return $fn($request, $options)->then(
                function (?ResponseInterface $response) use ($span, $sunsetOption) {
                    if (!$response) {
                        return $response;
                    }

                    if ($sunsetOption->getEnabled() && $response->hasHeader('Sunset')) {
                        $sunsetDate = $response->getHeaderLine('Sunset');
                        $attributes = [
                            'sunset_date' => $sunsetDate
                        ];

                        if ($response->hasHeader('Link')) {
                            $links = $response->getHeader('Link');
                            foreach ($links as $linkLine) {
                                // RFC 8288: a single Link header line can contain
                                // multiple comma-separated link-values
                                $linkValues = $this->splitLinkValues($linkLine);
                                foreach ($linkValues as $linkValue) {
                                    $linkValue = trim($linkValue);
                                    if (stripos($linkValue, 'rel="sunset"') !== false || stripos($linkValue, "rel='sunset'") !== false) {
                                        if (preg_match('/<([^>]+)>/', $linkValue, $matches)) {
                                            $attributes['sunset_link'] = $matches[1];
                                        }
                                    }
                                }
                            }
                        }

                        $span->addEvent(self::EVENT_NAME, $attributes);
                    }
                    return $response;
                }
            );
        } finally {
            $scope->detach();
            $span->end();
        }
    }

    /**
     * Splits a Link header line into individual link-values,
     * respecting angle brackets so commas inside URIs are not split.
     *
     * @param string $headerLine
     * @return array<string>
     */
    private function splitLinkValues(string $headerLine): array
    {
        $values = [];
        $current = '';
        $insideBrackets = false;

        for ($i = 0, $len = strlen($headerLine); $i < $len; $i++) {
            $char = $headerLine[$i];
            if ($char === '<') {
                $insideBrackets = true;
            } elseif ($char === '>') {
                $insideBrackets = false;
            }

            if ($char === ',' && !$insideBrackets) {
                $values[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '') {
            $values[] = $current;
        }

        return $values;
    }
}
