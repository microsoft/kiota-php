<?php

namespace Microsoft\Kiota\Bundle\Tests;

use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactoryRegistry;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactoryRegistry;
use Microsoft\Kiota\Bundle\DefaultRequestAdapter;
use Microsoft\Kiota\Serialization\Form\FormParseNodeFactory;
use Microsoft\Kiota\Serialization\Form\FormSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Json\JsonParseNodeFactory;
use Microsoft\Kiota\Serialization\Json\JsonSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Multipart\MultipartSerializationWriterFactory;
use Microsoft\Kiota\Serialization\Text\TextParseNodeFactory;
use Microsoft\Kiota\Serialization\Text\TextSerializationWriterFactory;

class DefaultRequestAdapterTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultRequestAdapterBuilder()
    {
        $builder = DefaultRequestAdapter::builder();
        $this->assertInstanceOf(\Microsoft\Kiota\Bundle\DefaultRequestAdapterBuilder::class, $builder);
    }

    public function testSerializersAreRegisteredAsExpected()
    {
        $adapter = DefaultRequestAdapter::builder()->build();

        $this->assertEquals(4, count(SerializationWriterFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories));
        $this->assertInstanceOf(JsonSerializationWriterFactory::class,
                SerializationWriterFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["application/json"]);
        $this->assertInstanceOf(TextSerializationWriterFactory::class,
                SerializationWriterFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["text/plain"]);
        $this->assertInstanceOf(FormSerializationWriterFactory::class,
                SerializationWriterFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["application/x-www-form-urlencoded"]);
        $this->assertInstanceOf(MultipartSerializationWriterFactory::class,
                SerializationWriterFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["multipart/form-data"]);

        $this->assertEquals(3, count(ParseNodeFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories));
        $this->assertInstanceOf(JsonParseNodeFactory::class,
                ParseNodeFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["application/json"]);
        $this->assertInstanceOf(TextParseNodeFactory::class,
                ParseNodeFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["text/plain"]);
        $this->assertInstanceOf(FormParseNodeFactory::class,
                ParseNodeFactoryRegistry::getDefaultInstance()->contentTypeAssociatedFactories["application/x-www-form-urlencoded"]);
    }
}
