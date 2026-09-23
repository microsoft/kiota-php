<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */

namespace Microsoft\Kiota\Http\Middleware;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Microsoft\Kiota\Http\Middleware\Options\BodyInspectionOption;
use Microsoft\Kiota\Http\Middleware\Options\ObservabilityOption;
use OpenTelemetry\API\Trace\TracerInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BodyInspectionHandler
 *
 * Middleware that allows inspection of the request and response bodies.
 * Disabled by default. Configured using a {@link BodyInspectionOption}
 *
 * WARNING: When enabled, this handler creates in-memory copies of the request
 * and/or response body streams. This will lead to memory pressure on the
 * application, especially with large payloads. Use adequately.
 * Callers are responsible for disposing (closing) the copied streams.
 *
 * @package Microsoft\Kiota\Http\Middleware
 * @copyright 2024 Microsoft Corporation
 * @license https://opensource.org/licenses/MIT MIT License
 */
class BodyInspectionHandler
{
    public const HANDLER_NAME = 'kiotaBodyInspectionHandler';
    private const SPAN_NAME = 'BodyInspectionHandler_intercept';
    private const HANDLER_ENABLED_KEY = 'com.microsoft.kiota.handler.bodyInspection.enable';

    /**
     * @var BodyInspectionOption
     */
    private BodyInspectionOption $inspectionOption;

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
     * @param BodyInspectionOption|null $inspectionOption
     */
    public function __construct(callable $nextHandler, ?BodyInspectionOption $inspectionOption = null)
    {
        $this->nextHandler = $nextHandler;
        $this->inspectionOption = $inspectionOption ?: new BodyInspectionOption();
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
            $inspectionOption = $this->inspectionOption;
            if (array_key_exists(BodyInspectionOption::class, $options) &&
                $options[BodyInspectionOption::class] instanceof BodyInspectionOption) {
                $inspectionOption = $options[BodyInspectionOption::class];
            }

            $span->setAttribute(self::HANDLER_ENABLED_KEY, $inspectionOption->getInspectRequestBody() || $inspectionOption->getInspectResponseBody());

            if ($inspectionOption->getInspectRequestBody()) {
                $request = $this->inspectStream($request, true, $inspectionOption);
            }

            $fn = $this->nextHandler;
            return $fn($request, $options)->then(
                function (?ResponseInterface $response) use ($inspectionOption) {
                    if (!$response) {
                        return $response;
                    }
                    if ($inspectionOption->getInspectResponseBody()) {
                        $response = $this->inspectStream($response, false, $inspectionOption);
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
     * @template T of MessageInterface
     * @param T $message
     * @param bool $isRequest
     * @param BodyInspectionOption $option
     * @return T
     */
    private function inspectStream(MessageInterface $message, bool $isRequest, BodyInspectionOption $option): MessageInterface
    {
        $body = $message->getBody();
        if ($body->isSeekable()) {
            $originalPosition = $body->tell();
            $body->rewind();
            $content = $body->getContents();
            $body->seek($originalPosition);

            $copiedStream = Utils::streamFor($content);
            if ($isRequest) {
                $option->setRequestBody($copiedStream);
            } else {
                $option->setResponseBody($copiedStream);
            }
        }
        return $message;
    }
}
