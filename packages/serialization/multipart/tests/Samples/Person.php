<?php

namespace Microsoft\Kiota\Serialization\Multipart\Tests\Samples;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Psr\Http\Message\StreamInterface;

class Person implements Parsable
{
    private ?string $name = null;
    private ?StreamInterface $bio = null;

    /**
     * @inheritDoc
     */
    public function getFieldDeserializers(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function serialize(SerializationWriter $writer): void
    {
        $writer->writeStringValue('name', $this->name);
        $writer->writeBinaryContent('bio', $this->bio);
    }

    /**
     * @return StreamInterface|null
     */
    public function getBio(): ?StreamInterface
    {
        return $this->bio;
    }

    /**
     * @param StreamInterface|null $bio
     */
    public function setBio(?StreamInterface $bio): void
    {
        $this->bio = $bio;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}