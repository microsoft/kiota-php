<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */


namespace Microsoft\Kiota\Abstractions;

use Attribute;

/**
 * Class QueryParameter
 *
 * Attribute for query parameter class properties
 *
 * @package Microsoft\Kiota\Abstractions
 * @copyright 2022 Microsoft Corporation
 * @license https://opensource.org/licenses/MIT MIT License
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class QueryParameter
{
    /**
     * @var string
     */
    public string $name = "";

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}