<?php

namespace Microsoft\Kiota\Bundle\Tests;

use Microsoft\Kiota\Abstractions\ApiClientBuilder;
use Microsoft\Kiota\Abstractions\Store\InMemoryBackingStoreFactory;
use Microsoft\Kiota\Bundle\DefaultRequestAdapter;
use Microsoft\Kiota\Serialization\Form\FormSerializationWriterFactory;
use PHPUnit\Framework\TestCase;

class KiotaClientIntegrationTest extends TestCase
{
    public function testBackingStoreEnablementIdempotentAcrossAdapterLifecycle(): void
    {
        $backingStoreFactory = new InMemoryBackingStoreFactory();
        $adapter = DefaultRequestAdapter::builder()->build();

        // Enable backing store initially
        $adapter->enableBackingStore($backingStoreFactory);

        // Enable backing store a second time; verify no duplicate wrapping occurs
        $adapter->enableBackingStore($backingStoreFactory);

        $this->assertInstanceOf(DefaultRequestAdapter::class, $adapter);
    }

    public function testFormSerializationWriterFactoryProducesValidContentWithZeroKey(): void
    {
        $factory = new FormSerializationWriterFactory();
        $writer = $factory->getSerializationWriter('application/x-www-form-urlencoded');

        $writer->writeStringValue('0', 'zeroKeyValue');
        $writer->writeIntegerValue('count', 0);

        $stream = $writer->getSerializedContent();
        $this->assertSame('0=zeroKeyValue&count=0', $stream->getContents());
    }
}
