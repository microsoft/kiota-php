<?php

namespace Microsoft\Kiota\Bundle;

use GuzzleHttp\ClientInterface;
use Microsoft\Kiota\Abstractions\Authentication\AnonymousAuthenticationProvider;
use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Http\Middleware\Options\ObservabilityOption;

class DefaultRequestAdapterBuilder
{
    private AuthenticationProvider $authenticationProvider;
    private ?ParseNodeFactory $parseNodeFactory = null;
    private ?SerializationWriterFactory $serializationWriterFactory = null;
    private ?ClientInterface $guzzleClient = null;
    private ?ObservabilityOption $observabilityOption = null;

    public function __construct()
    {
        $this->authenticationProvider = new AnonymousAuthenticationProvider();
    }

    public function withAuthenticationProvider(AuthenticationProvider $authenticationProvider): DefaultRequestAdapterBuilder
    {
        $this->authenticationProvider = $authenticationProvider;
        return $this;
    }

    public function withParseNodeFactory(ParseNodeFactory $parseNodeFactory): DefaultRequestAdapterBuilder
    {
        $this->parseNodeFactory = $parseNodeFactory;
        return $this;
    }

    public function withSerializationWriterFactory(SerializationWriterFactory $serializationWriterFactory): DefaultRequestAdapterBuilder
    {
        $this->serializationWriterFactory = $serializationWriterFactory;
        return $this;
    }

    public function withGuzzleClient(ClientInterface $guzzleClient): DefaultRequestAdapterBuilder
    {
        $this->guzzleClient = $guzzleClient;
        return $this;
    }

    public function withObservabilityOption(ObservabilityOption $observabilityOption): DefaultRequestAdapterBuilder
    {
        $this->observabilityOption = $observabilityOption;
        return $this;
    }

    public function build(): DefaultRequestAdapter
    {
        return new DefaultRequestAdapter($this->authenticationProvider, $this->parseNodeFactory, $this->serializationWriterFactory, $this->guzzleClient, $this->observabilityOption);
    }
}
