<?php

namespace Microsoft\Kiota\Abstractions\Tests;

use Microsoft\Kiota\Abstractions\ApiClientBuilder;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriter;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Abstractions\Store\BackingStoreParseNodeFactory;
use Microsoft\Kiota\Abstractions\Store\BackingStoreSerializationWriterProxyFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class ApiClientBuilderTest extends TestCase
{
    public function testEnableBackingStoreForSerializationWriterFactoryDoesNotDoubleWrap(): void
    {
        $factory = $this->createMock(SerializationWriterFactory::class);
        $factory->method('getValidContentType')->willReturn('application/json');

        $wrapped1 = ApiClientBuilder::enableBackingStoreForSerializationWriterFactory($factory);
        $this->assertInstanceOf(BackingStoreSerializationWriterProxyFactory::class, $wrapped1);

        $wrapped2 = ApiClientBuilder::enableBackingStoreForSerializationWriterFactory($wrapped1);
        $this->assertSame($wrapped1, $wrapped2);
    }

    public function testEnableBackingStoreForParseNodeFactoryDoesNotDoubleWrap(): void
    {
        $factory = $this->createMock(ParseNodeFactory::class);
        $factory->method('getValidContentType')->willReturn('application/json');

        $wrapped1 = ApiClientBuilder::enableBackingStoreForParseNodeFactory($factory);
        $this->assertInstanceOf(BackingStoreParseNodeFactory::class, $wrapped1);

        $wrapped2 = ApiClientBuilder::enableBackingStoreForParseNodeFactory($wrapped1);
        $this->assertSame($wrapped1, $wrapped2);
    }
}
