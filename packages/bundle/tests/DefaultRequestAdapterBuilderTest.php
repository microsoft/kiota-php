<?php

namespace Microsoft\Kiota\Bundle\Tests;

use GuzzleHttp\Client;
use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Bundle\DefaultRequestAdapterBuilder;
use Microsoft\Kiota\Http\Middleware\Options\ObservabilityOption;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DefaultRequestAdapterBuilderTest extends TestCase
{
    public function testBuilderMethods()
    {
        $builder = new DefaultRequestAdapterBuilder();
        /** @var AuthenticationProvider $authenticationProvider */
        $authenticationProvider = $this->createMock(AuthenticationProvider::class);
        $this->assertInstanceOf(DefaultRequestAdapterBuilder::class, $builder->withAuthenticationProvider($authenticationProvider));
        /** @var ParseNodeFactory $mockParseNodeFactory */
        $mockParseNodeFactory = $this->createMock(ParseNodeFactory::class);
        $this->assertInstanceOf(DefaultRequestAdapterBuilder::class, $builder->withParseNodeFactory($mockParseNodeFactory));
        /** @var SerializationWriterFactory $mockSerializationWriterFactory */
        $mockSerializationWriterFactory = $this->createMock(SerializationWriterFactory::class);
        $this->assertInstanceOf(DefaultRequestAdapterBuilder::class, $builder->withSerializationWriterFactory($mockSerializationWriterFactory));
        /** @var Client $mockClient */
        $mockClient = $this->createMock(Client::class);
        $this->assertInstanceOf(DefaultRequestAdapterBuilder::class, $builder->withGuzzleClient($mockClient));
        /** @var ObservabilityOption $observabilityOption */
        $observabilityOption = $this->createMock(ObservabilityOption::class);
        $this->assertInstanceOf(DefaultRequestAdapterBuilder::class, $builder->withObservabilityOption($observabilityOption));

        $adapter = $builder->build();

        $reflectionObject = new ReflectionClass($adapter);
        $properties = $reflectionObject->getParentClass()->getProperties();
        foreach ($properties as $property) {
            switch ($property->getDeclaringClass()->getName()) {
                case AuthenticationProvider::class:
                    $property->setAccessible(true);
                    $this->assertEquals($authenticationProvider, $property->getValue($adapter));
                    break;
                case ParseNodeFactory::class:
                    $property->setAccessible(true);
                    $this->assertEquals($mockParseNodeFactory, $property->getValue($adapter));
                    break;
                case SerializationWriterFactory::class:
                    $property->setAccessible(true);
                    $this->assertEquals($mockSerializationWriterFactory, $property->getValue($adapter));
                    break;
                case Client::class:
                    $property->setAccessible(true);
                    $this->assertEquals($mockClient, $property->getValue($adapter));
                    break;
                case ObservabilityOption::class:
                    $property->setAccessible(true);
                    $this->assertEquals($observabilityOption, $property->getValue($adapter));
                    break;
            }
        }
    }
}
