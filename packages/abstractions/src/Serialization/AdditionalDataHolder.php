<?php

namespace Microsoft\Kiota\Abstractions\Serialization;
/**
 * Interface for models that hold additional data not described in the OpenAPI description found when deserializing. Can be used for serialization as well.
 */
interface AdditionalDataHolder {
    /**
     * Gets the additional data for this object that did not belong to the properties.
     * @return array<string,mixed>|null The additional data for this object.
     */
    public function getAdditionalData(): ?array;

    /**
     * Sets the additional data for this object that did not belong to the properties.
     * @param array<string, mixed> $value The additional data for this object.
     */
    public function setAdditionalData(array $value): void;
}
