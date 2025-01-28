<?php
namespace Microsoft\Kiota\Serialization\Multipart\Tests;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\MultiPartBody;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\Types\Date;
use Microsoft\Kiota\Abstractions\Types\Time;
use Microsoft\Kiota\Serialization\Multipart\Exceptions\NotImplementedException;
use Microsoft\Kiota\Serialization\Multipart\MultipartSerializationWriter;
use Microsoft\Kiota\Serialization\Multipart\Tests\Samples\Gender;
use Microsoft\Kiota\Serialization\Multipart\Tests\Samples\Person;
use PHPUnit\Framework\TestCase;

class MultipartSerializationWriterTest extends TestCase
{

    private RequestAdapter $adapter;
    public function testWriteStringValue(): void
    {
        $writer = new MultipartSerializationWriter();
        $writer->writeStringValue('name', 'Kenneth Omondi');
        $cont = $writer->getSerializedContent();
        $this->assertEquals("name: Kenneth Omondi\n", $cont->getContents());
    }

    public function setUp(): void
    {
        $this->adapter = $this->createMock(RequestAdapter::class);
    }

    public function testWriteBinaryContent(): void
    {
        $writer = new MultipartSerializationWriter();
        $person = new Person();
        $person->setName('John Doe');
        $person->setBio(Utils::streamFor("I am a young professional passionate about coding."));
        $person->serialize($writer);
        $cont = $writer->getSerializedContent()->getContents();
        $this->assertStringContainsString('name: John Doe', $cont);
        $this->assertStringContainsString('bio: I am a young professional passionate about coding.', $cont);
    }

    public function testWriteParsableNonMultipartBody(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $person = new Person();
        $person->setName('John Doe');
        $person->setBio(Utils::streamFor('I am grateful.'));

        $writer = new MultipartSerializationWriter();
        $writer->writeObjectValue('person', $person);
    }

    public function testWriteBooleanValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeBooleanValue("married", false);
    }

    public function testWriteFloatValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeFloatValue("height", 5.11);
    }

    public function testWriteNullValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeNullValue("nullKey");
    }

    public function testWriteAdditionalData(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeAdditionalData([]);
    }

    public function testWriteDateTimeValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeDateTimeValue("dateTime", new DateTime('now'));
    }

    public function testWriteDateIntervalValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeDateIntervalValue("interval", new DateInterval("PT12M2S"));
    }

    public function testWriteAnyValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeAnyValue('name', 'John Doe');
        $this->assertEquals("name: John Doe\n", (string)$writer->getSerializedContent());
        $writer->writeAnyValue("people", 10);
    }

    public function testWriteCollectionOfObjectValues(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeCollectionOfObjectValues("people", []);
    }

    public function testWriteEnumValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeEnumValue('gender', new Gender('male'));
    }

    public function testWriteCollectionOfEnumValues(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeCollectionOfEnumValues("formats", []);
    }

    /**
     * @throws Exception
     */
    public function testWriteDateValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeDateValue("date", new Date((new DateTime('now'))->format(DateTimeInterface::RFC3339)));
    }

    public function testWriteCollectionOfPrimitiveValues(): void
    {

        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeCollectionOfPrimitiveValues("numbers", [1,2,3,4]);
    }

    public function testWriteObjectValue(): void
    {
        $writer = new MultipartSerializationWriter();
        $multipartBody = new MultiPartBody();
        $person = new Person();
        $person->setName('John Doe');
        $person->setBio(Utils::streamFor("I am a young professional passionate about coding."));
        $multipartBody->setRequestAdapter($this->adapter);
        $multipartBody->addOrReplacePart('person', 'application/json', $person);

        $writer->writeObjectValue('person', $multipartBody);
        $this->assertStringContainsString('Content-Disposition: form-data; name="person"', (string)$writer->getSerializedContent());
    }

    /**
     * @throws Exception
     */
    public function testWriteTimeValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeTimeValue("time", new Time((new DateTime('now'))->format(DateTimeInterface::RFC3339)));
    }

    public function testGetSerializedContent(): void
    {
        $writer = new MultipartSerializationWriter();
        $multipartBody = new MultiPartBody();
        $person = new Person();
        $person->setName('John Doe');
        $person->setBio(Utils::streamFor("I am a young professional passionate about coding."));
        $multipartBody->setRequestAdapter($this->adapter);
        $multipartBody->addOrReplacePart('person', 'application/json', $person);

        $writer->writeAnyValue('person', $multipartBody);
        $this->assertStringContainsString("Content-Type: application/json\nContent-Disposition: form-data; name=\"person\"", (string)$writer->getSerializedContent());
        $this->assertStringContainsString('Content-Disposition: form-data; name="person"', (string)$writer->getSerializedContent());
    }

    public function testWriteIntegerValue(): void
    {
        $this->expectException(NotImplementedException::class);
        $writer = new MultipartSerializationWriter();
        $writer->writeIntegerValue("age", 100);
    }
}
