<?php

namespace Microsoft\Kiota\Serialization\Json;

use DateInterval;
use DateTime;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\Enum;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterToStringTrait;
use Microsoft\Kiota\Abstractions\Types\Date;
use Microsoft\Kiota\Abstractions\Types\Time;
use Psr\Http\Message\StreamInterface;
use stdClass;
use Microsoft\Kiota\Abstractions\Serialization\ComposedTypeWrapper;

/**
 * @method onBeforeObjectSerialization(?Parsable $value);
 * @method onStartObjectSerialization(?Parsable $value, SerializationWriter $writer);
 * @method onAfterObjectSerialization(?Parsable $value);
 */
class JsonSerializationWriter implements SerializationWriter
{
    use SerializationWriterToStringTrait;

    /** @var array<mixed> $writer */
    private array $writer = [];

    /** @var string PROPERTY_SEPARATOR */
    private const PROPERTY_SEPARATOR = ',';

    /** @var callable|null $onStartObjectSerialization */
    private $onStartObjectSerialization;

    /** @var callable|null $onAfterObjectSerialization */
    private $onAfterObjectSerialization;

    /** @var callable|null $onBeforeObjectSerialization */
    private $onBeforeObjectSerialization;

    private function writePropertyName(string $propertyName): void {
        $this->writer []= "\"$propertyName\":";
    }

    /**
     * @inheritDoc
     */
    public function writeStringValue(?string $key, ?string $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, "\"{$this->getStringValueAsEscapedString($value)}\"");
        }
    }

    /**
     * @inheritDoc
     */
    public function writeBooleanValue(?string $key, ?bool $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, $this->getBooleanValueAsString($value));
        }
    }

    /**
     * @inheritDoc
     */
    public function writeFloatValue(?string $key, ?float $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeIntegerValue(?string $key, ?int $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeDateTimeValue(?string $key, ?DateTime $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, "\"{$this->getDateTimeValueAsString($value)}\"");
        }
    }

    /**
     * @param string|null $key
     * @param Date|null $value
     * @return void
     */
    public function writeDateValue(?string $key, ?Date $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $valueString = (string)$value;
            $this->writePropertyValue($key, "\"$valueString\"");
        }
    }

    public function writeBinaryContent(?string $key, ?StreamInterface $value): void {
        if ($value !== null) {
            $val = $value->getContents();
            $value->rewind();
            $this->writeStringValue($key, $val);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeCollectionOfObjectValues(?string $key, ?array $values): void {
        if ($values !== null) {
            if($key !== null){
                $this->writePropertyName($key);
            }
            $this->writer [] = '[';
            foreach ($values as $v) {
                $this->writeObjectValue(null, $v);
                $this->writer [] = self::PROPERTY_SEPARATOR;
            }
            if (count($values) > 0) {
                array_pop($this->writer);
            }
            $this->writer [] = ']';
            if ($key !== null) {
                $this->writer []= self::PROPERTY_SEPARATOR;
            }
        }
    }

    /**
     * Serializes additional object values
     *
     * @param array<Parsable|null> $additionalValuesToMerge
     * @return void
     */
    private function writeAdditionalObjectValues(array $additionalValuesToMerge): void {
        foreach ($additionalValuesToMerge as $additionalValueToMerge) {
            if (is_null($additionalValueToMerge)) {
                continue;
            }
            if ($this->getOnBeforeObjectSerialization() !== null) {
                call_user_func($this->getOnBeforeObjectSerialization(), $additionalValueToMerge, $this);
            }
            if ($this->getOnStartObjectSerialization() !== null) {
                call_user_func($this->getOnStartObjectSerialization(), $additionalValueToMerge, $this);
            }
            $additionalValueToMerge->serialize($this);
            if ($this->getOnAfterObjectSerialization() !== null) {
                call_user_func($this->getOnAfterObjectSerialization(), $additionalValueToMerge);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function writeObjectValue(?string $key, $value, ?Parsable ...$additionalValuesToMerge): void {
        if ($value == null && count($additionalValuesToMerge) === 0) {
            return;
        }
        if(!empty($key)) {
            $this->writePropertyName($key);
        }
        if ($this->getOnBeforeObjectSerialization() !== null) {
            $this->getOnBeforeObjectSerialization()($value);
        }
        $isComposedType = $value instanceof ComposedTypeWrapper;

        if (!$isComposedType) {
            $this->writer [] = '{';
        }
        if ($this->getOnStartObjectSerialization() !== null) {
            $this->getOnStartObjectSerialization()($value, $this);
        }
        if ($value !== null) {
            $value->serialize($this);
        }
        $this->writeAdditionalObjectValues($additionalValuesToMerge);
        if ($this->writer[count($this->writer) - 1] === ',') {
            array_pop($this->writer);
        }
        if ($this->getOnAfterObjectSerialization() !== null) {
            $this->getOnAfterObjectSerialization()($value);
        }
        if (!$isComposedType) {
            $this->writer [] = '}';
        }
        if ($key !== null && $value !== null) {
            $this->writer [] = self::PROPERTY_SEPARATOR;
        }
    }

    /**
     * @inheritDoc
     */
    public function getSerializedContent(): StreamInterface {
        if (count($this->writer) > 0 && $this->writer[count($this->writer) - 1] === ','){
            array_pop($this->writer);
        }
        return Utils::streamFor(implode('', $this->writer));
    }

    /**
     * @inheritDoc
     */
    public function writeEnumValue(?string $key, ?Enum $value): void {
        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, "\"{$value->value()}\"");
        }
    }

    /**
     * @inheritDoc
     */
    public function writeCollectionOfEnumValues(?string $key, ?array $values): void {
        if ($values !== null) {
            if($key !== null){
                $this->writePropertyName($key);
            }
            $this->writer [] = '[';
            foreach ($values as $v) {
                $this->writeEnumValue(null, $v);
                $this->writer [] = self::PROPERTY_SEPARATOR;
            }
            if (count($values) > 0) {
                array_pop($this->writer);
            }
            $this->writer [] = ']';
            if ($key !== null) {
                $this->writer []= self::PROPERTY_SEPARATOR;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function writeNullValue(?string $key): void {
        if (!empty($key)) {
            $this->writePropertyName($key);
        }
        $this->writePropertyValue($key, 'null');
    }

    /**
     * @inheritDoc
     */
    public function writeAdditionalData(?array $value): void {
        if($value === null) {
            return;
        }
        foreach ($value as $key => $val) {
            $this->writeAnyValue($key, $val);
        }
    }

    /**
     * @inheritDoc
     */
    public function setOnBeforeObjectSerialization(?callable $value): void {
        $this->onBeforeObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnBeforeObjectSerialization(): ?callable {
        return $this->onBeforeObjectSerialization;
    }

    /**
     * @inheritDoc
     */
    public function setOnAfterObjectSerialization(?callable $value): void {
        $this->onAfterObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnAfterObjectSerialization(): ?callable {
        return $this->onAfterObjectSerialization;
    }

    /**
     * @inheritDoc
     */
    public function setOnStartObjectSerialization(?callable $value): void {
        $this->onStartObjectSerialization = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOnStartObjectSerialization(): ?callable {
        return $this->onStartObjectSerialization;
    }

    /**
     * @param string|null $key
     * @param mixed $value
     */
    public function writeAnyValue(?string $key, $value): void{
        if (is_null($value)) {
            $this->writeNullValue($key);
        } elseif (is_float($value)) {
            $this->writeFloatValue($key, $value);
        } elseif (is_string($value)) {
            $this->writeStringValue($key, $value);
        } elseif (is_int($value)) {
            $this->writeIntegerValue($key, $value);
        } elseif (is_bool($value)) {
            $this->writeBooleanValue($key, $value);
        } elseif ($value instanceof Date) {
            $this->writeDateValue($key, $value);
        } elseif ($value instanceof Time) {
            $this->writeTimeValue($key, $value);
        } elseif ($value instanceof DateInterval) {
            $this->writeDateIntervalValue($key, $value);
        } elseif ($value instanceof DateTime) {
            $this->writeDateTimeValue($key, $value);
        } elseif (is_array($value)) {
            $keys = array_filter(array_keys($value), 'is_string');
            // If there are string keys then that means this is a single
            // object we are dealing with
            // otherwise it is a collection of objects.
            if (!empty($keys)) {
                $this->writeNonParsableObjectValue($key, (object)$value);
            } elseif (!empty($value)) {
                if ($value[0] instanceof Parsable) {
                    $this->writeCollectionOfObjectValues($key, $value);
                } elseif ($value[0] instanceof Enum) {
                    $this->writeCollectionOfEnumValues($key, $value);
                } else {
                    $this->writeCollectionOfPrimitiveValues($key, $value);
                }
            }
        } elseif ($value instanceof stdClass) {
            $this->writeNonParsableObjectValue($key, $value);
        } elseif ($value instanceof Parsable) {
                $this->writeObjectValue($key, $value);
        } elseif ($value instanceof Enum) {
                $this->writeEnumValue($key, $value);
        } elseif ($value instanceof StreamInterface) {
            $this->writeStringValue($key, $value->getContents());
        } else {
            $type = gettype($value);
            throw new InvalidArgumentException("Could not serialize the object of type $type ");
        }
    }

    /**
     * @param string|null $key
     * @param mixed|null $value
     */
    public function writeNonParsableObjectValue(?string $key, $value): void{
        if ($value !== null) {
            if(!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writer [] = '{';
            $value = (array)$value;
            foreach ($value as $kKey => $kVal) {
                $this->writeAnyValue($kKey, $kVal);
            }
            if (count($value) > 0) {
                array_pop($this->writer);
            }
            $this->writer [] = '}';
            if ($key !== null) {
                $this->writer [] = self::PROPERTY_SEPARATOR;
            }
        }
    }

    /**
     * @param string|null $key
     * @param mixed $value
     * @return void
     */
    private function writePropertyValue(?string $key, $value): void {
        $this->writer []= $value;

        if ($key !== null) {
            $this->writer []= self::PROPERTY_SEPARATOR;
        }
    }

    /**
     * @param string|null $key
     * @param array<mixed> $values
     * @return void
     */
    public function writeCollectionOfPrimitiveValues(?string $key, ?array $values): void
    {
        if ($values !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writer [] = '[';
            foreach ($values as $value) {
                $this->writeAnyValue(null, $value);
                $this->writer [] = self::PROPERTY_SEPARATOR;
            }
            if (count($values) > 0) {
                array_pop($this->writer);
            }
            $this->writer [] = ']';
            if ($key !== null) {
                $this->writer [] = self::PROPERTY_SEPARATOR;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function writeTimeValue(?string $key, ?Time $value): void {


        if ($value !== null) {
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $val = "\"$value\"";
            $this->writePropertyValue($key, $val);
        }
    }

    public function writeDateIntervalValue(?string $key, ?DateInterval $value): void {
        if ($value !== null){
            if (!empty($key)) {
                $this->writePropertyName($key);
            }
            $this->writePropertyValue($key, "\"{$this->getDateIntervalValueAsString($value)}\"");
        }
    }
}
