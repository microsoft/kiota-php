<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */

namespace Microsoft\Kiota\Http\Middleware\Options;

use Microsoft\Kiota\Abstractions\RequestOption;
use Psr\Http\Message\StreamInterface;

/**
 * Class BodyInspectionOption
 *
 * RequestOption that enables the inspection of the request and response bodies.
 * 
 * @package Microsoft\Kiota\Http\Middleware\Options
 */
class BodyInspectionOption implements RequestOption
{
    private bool $inspectRequestBody;
    private bool $inspectResponseBody;
    private ?StreamInterface $requestBody = null;
    private ?StreamInterface $responseBody = null;

    /**
     * @param bool $inspectRequestBody Set to true to instruct the handler to copy the request body
     * @param bool $inspectResponseBody Set to true to instruct the handler to copy the response body
     */
    public function __construct(bool $inspectRequestBody = false, bool $inspectResponseBody = false)
    {
        $this->inspectRequestBody = $inspectRequestBody;
        $this->inspectResponseBody = $inspectResponseBody;
    }

    /**
     * @return bool
     */
    public function getInspectRequestBody(): bool
    {
        return $this->inspectRequestBody;
    }

    /**
     * @param bool $inspectRequestBody
     * @return void
     */
    public function setInspectRequestBody(bool $inspectRequestBody): void
    {
        $this->inspectRequestBody = $inspectRequestBody;
    }

    /**
     * @return bool
     */
    public function getInspectResponseBody(): bool
    {
        return $this->inspectResponseBody;
    }

    /**
     * @param bool $inspectResponseBody
     * @return void
     */
    public function setInspectResponseBody(bool $inspectResponseBody): void
    {
        $this->inspectResponseBody = $inspectResponseBody;
    }

    /**
     * Gets the request body stream copy. Callers are responsible for closing the stream.
     * @return StreamInterface|null
     */
    public function getRequestBody(): ?StreamInterface
    {
        return $this->requestBody;
    }

    /**
     * @param StreamInterface|null $requestBody
     * @return void
     */
    public function setRequestBody(?StreamInterface $requestBody): void
    {
        $this->requestBody = $requestBody;
    }

    /**
     * Gets the response body stream copy. Callers are responsible for closing the stream.
     * @return StreamInterface|null
     */
    public function getResponseBody(): ?StreamInterface
    {
        return $this->responseBody;
    }

    /**
     * @param StreamInterface|null $responseBody
     * @return void
     */
    public function setResponseBody(?StreamInterface $responseBody): void
    {
        $this->responseBody = $responseBody;
    }
}
