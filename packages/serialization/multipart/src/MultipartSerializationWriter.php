<?php

namespace Microsoft\Kiota\Serialization\Multipart;

use DateInterval;
use DateTime;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\{Enum,
    MultiPartBody,
    Serialization\Parsable,
    Serialization\SerializationWriter,
    Types\Date,
    Types\Time};
use Microsoft\Kiota\Serialization\Multipart\Exceptions\NotImplementedException;
use Psr\Http\Message\StreamInterface;

class MultipartSerializationWriter implements SerializationWriter
{
    /** @var callable|null  */
    private $onAfterObjectSerialization = null;
    /** @var callable|null  */
    private $onBeforeObjectSerialization = null;
    /** @var callable|null */
    private $onStartObjectSerialization = null;

    private StreamInterface $writer;

    public function __construct()
    {
        // Initialize empty stream
        $this->writer = Utils::streamFor(null);
    }

    /**
     * @inheritDoc
     */
    public function writeStringValue(?string $key, ?string $value): void
    {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writer->write("$key: ");
            }
            $this->writer->write($value);
            $this->writer->write("\n");
        }
    }

    /**
     * @inheritDoc
     */
    public function writeBooleanValue(?string $key, ?bool $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeFloatValue(?string $key, ?float $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeIntegerValue(?string $key, ?int $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeDateTimeValue(?string $key, ?DateTime $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeCollectionOfObjectValues(?string $key, ?array $values): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeObjectValue(?string $key, ?Parsable $value, ?Parsable ...$additionalValuesToMerge): void
    {
        if ($value !== null) {
            if (!$value instanceof MultiPartBody) {
                $multipartBodyClass = MultiPartBody::class;
                $class = get_class($value);
                throw new InvalidArgumentException("Expected value to be of type $multipartBodyClass found $class");
            }
            $this->serializeValue($this, $value);
            if ($this->onAfterObjectSerialization !== null) {
                call_user_func($this->onAfterObjectSerialization, $value);
            }
        }

    }

    /**
     * @inheritDoc
     */
    public function getSerializedContent(): StreamInterface
    {
        $this->writer->rewind();
        return $this->writer;
    }

    /**
     * @inheritDoc
     */
    public function writeEnumValue(?string $key, ?Enum $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeCollectionOfEnumValues(?string $key, ?array $values): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeNullValue(?string $key): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeAdditionalData(?array $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeDateValue(?string $key, ?Date $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeTimeValue(?string $key, ?Time $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeDateIntervalValue(?string $key, ?DateInterval $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeCollectionOfPrimitiveValues(?string $key, ?array $value): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function writeAnyValue(?string $key, $value): void
    {
        if ($value instanceof StreamInterface) {
            $this->writeBinaryContent($key, $value);
        } elseif (is_string($value)){
            $this->writeStringValue($key, $value);
        } elseif ($value instanceof MultiPartBody){
            $this->writeObjectValue($key, $value);
        } else {
            $type = get_debug_type($value);
            throw new InvalidArgumentException("Serialization of $type is not supported.");
        }
    }

    /**
     * @inheritDoc
     */
    public function writeBinaryContent(?string $key, ?StreamInterface $value): void
    {
        if ($value !== null) {
            if ($key !== null) {
                $this->writer->write("$key: ");
            }
            $this->writer->write((string)$value);
        }
    }

    /**
     * @inheritDoc
     */
    public function setOnBeforeObjectSerialization(?callable $value): void
    {
        $this->onBeforeObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnBeforeObjectSerialization(): ?callable
    {
        return $this->onBeforeObjectSerialization;
    }

    /**
     * @inheritDoc
     */
    public function setOnAfterObjectSerialization(?callable $value): void
    {
        $this->onAfterObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnAfterObjectSerialization(): ?callable
    {
        return $this->onAfterObjectSerialization;
    }

    /**
     * @inheritDoc
     */
    public function setOnStartObjectSerialization(?callable $value): void
    {
        $this->onStartObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnStartObjectSerialization(): ?callable
    {
        return $this->onStartObjectSerialization;
    }

    /**
     * @param MultipartSerializationWriter $writer
     * @template T of Parsable
     * @param T $value
     * @return void
     */
    public function serializeValue(MultipartSerializationWriter $writer, $value): void
    {
        if ($this->onBeforeObjectSerialization !== null) {
            call_user_func($this->onBeforeObjectSerialization, $value);
        }
        if ($this->onStartObjectSerialization !== null) {
            call_user_func($this->onStartObjectSerialization, $value);
        }
        $value->serialize($writer);
    }
}
