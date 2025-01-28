<?php

namespace Microsoft\Kiota\Serialization\Multipart\Tests;

use InvalidArgumentException;
use Microsoft\Kiota\Serialization\Multipart\MultipartSerializationWriter;
use Microsoft\Kiota\Serialization\Multipart\MultipartSerializationWriterFactory;
use PHPUnit\Framework\TestCase;

class MultipartSerializationWriterFactoryTest extends TestCase
{

    public function testGetValidContentType(): void
    {
        $factory = new MultipartSerializationWriterFactory();
        $this->assertEquals("multipart/form-data", $factory->getValidContentType());
    }

    public function testGetSerializationWriter(): void
    {
        $factory = new MultipartSerializationWriterFactory();
        $writer = $factory->getSerializationWriter('multipart/form-data');
        $this->assertInstanceOf(MultipartSerializationWriter::class, $writer);
    }

    public function testThrowsExceptionOnInvalidContentType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new MultipartSerializationWriterFactory();
        $writer = $factory->getSerializationWriter('application/json');
    }
}
