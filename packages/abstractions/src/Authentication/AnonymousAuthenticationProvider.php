<?php

namespace Microsoft\Kiota\Abstractions\Authentication;

use Microsoft\Kiota\Abstraction\Promise\FulfilledPromise;
use Microsoft\Kiota\Abstraction\Promise\Promise;
use Microsoft\Kiota\Abstractions\RequestInformation;

class AnonymousAuthenticationProvider implements AuthenticationProvider {

    /**
     * @param RequestInformation $request Request information
     * @param array<string, mixed> $additionalAuthenticationContext
     * @return Promise<RequestInformation>
     */
    public function authenticateRequest(
        RequestInformation $request,
        array $additionalAuthenticationContext = []
    ): Promise
    {
        return new FulfilledPromise($request);
    }
}
