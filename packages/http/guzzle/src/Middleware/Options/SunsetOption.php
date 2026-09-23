<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */

namespace Microsoft\Kiota\Http\Middleware\Options;

use Microsoft\Kiota\Abstractions\RequestOption;

/**
 * Class SunsetOption
 *
 * Configures the SunsetHandler middleware.
 *
 * @package Microsoft\Kiota\Http\Middleware\Options
 */
class SunsetOption implements RequestOption
{
    private bool $enabled;

    /**
     * @param bool $enabled Set to false to disable the middleware for a given request.
     */
    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
