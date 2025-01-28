<?php

namespace Microsoft\Kiota\Serialization\Multipart;

use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;

class MultipartSerializationWriterFactory implements SerializationWriterFactory
{

    /**
     * @inheritDoc
     */
    public function getSerializationWriter(string $contentType): SerializationWriter
    {
        if (strcasecmp($this->getValidContentType(), $contentType) !== 0) {
            throw new InvalidArgumentException("Expected {$this->getValidContentType()} as content type $contentType given.");
        }
        return new MultipartSerializationWriter();
    }

    /**
     * @inheritDoc
     */
    public function getValidContentType(): string
    {
        return 'multipart/form-data';
    }
}