<?php

namespace Microsoft\Kiota\Bundle;

use GuzzleHttp\ClientInterface;
use Microsoft\Kiota\Abstractions\Authentication\AnonymousAuthenticationProvider;
use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Http\Middleware\Options\ObservabilityOption;

/**
 * Builder for DefaultRequestAdapter
 */
class DefaultRequestAdapterBuilder
{
    /**
     * Authentication provider to be used by the adapter
     *
     * @var AuthenticationProvider
     */
    private AuthenticationProvider $authenticationProvider;
    /**
     * Parse node factory to be used by the adapter
     *
     * @var ParseNodeFactory|null
     */
    private ?ParseNodeFactory $parseNodeFactory = null;
    /**
     * Serialization writer factory to be used by the adapter
     *
     * @var SerializationWriterFactory|null
     */
    private ?SerializationWriterFactory $serializationWriterFactory = null;
    /**
     * Guzzle client to be used by the adapter
     *
     * @var ClientInterface|null
     */
    private ?ClientInterface $guzzleClient = null;
    /**
     * Observability option to be used by the adapter
     *
     * @var ObservabilityOption|null
     */
    private ?ObservabilityOption $observabilityOption = null;

    /**
     * Instantiates a DefaultRequestAdapterBuilder
     */
    public function __construct()
    {
        $this->authenticationProvider = new AnonymousAuthenticationProvider();
    }

    /**
     * Sets the authentication provider to be used by the adapter
     *
     * @param AuthenticationProvider $authenticationProvider
     * @return DefaultRequestAdapterBuilder
     */
    public function withAuthenticationProvider(AuthenticationProvider $authenticationProvider): DefaultRequestAdapterBuilder
    {
        $this->authenticationProvider = $authenticationProvider;
        return $this;
    }

    /**
     * Sets the parse node factory to be used by the adapter
     *
     * @param ParseNodeFactory $parseNodeFactory
     * @return DefaultRequestAdapterBuilder
     */
    public function withParseNodeFactory(ParseNodeFactory $parseNodeFactory): DefaultRequestAdapterBuilder
    {
        $this->parseNodeFactory = $parseNodeFactory;
        return $this;
    }

    /**
     * Sets the serialization writer factory to be used by the adapter
     *
     * @param SerializationWriterFactory $serializationWriterFactory
     * @return DefaultRequestAdapterBuilder
     */
    public function withSerializationWriterFactory(SerializationWriterFactory $serializationWriterFactory): DefaultRequestAdapterBuilder
    {
        $this->serializationWriterFactory = $serializationWriterFactory;
        return $this;
    }

    /**
     * Sets the Guzzle client to be used by the adapter
     *
     * @param ClientInterface $guzzleClient
     * @return DefaultRequestAdapterBuilder
     */
    public function withGuzzleClient(ClientInterface $guzzleClient): DefaultRequestAdapterBuilder
    {
        $this->guzzleClient = $guzzleClient;
        return $this;
    }

    /**
     * Sets the observability option to be used by the adapter
     *
     * @param ObservabilityOption $observabilityOption
     * @return DefaultRequestAdapterBuilder
     */
    public function withObservabilityOption(ObservabilityOption $observabilityOption): DefaultRequestAdapterBuilder
    {
        $this->observabilityOption = $observabilityOption;
        return $this;
    }

    /**
     * Builds a DefaultRequestAdapter
     *
     * @return DefaultRequestAdapter
     */
    public function build(): DefaultRequestAdapter
    {
        return new DefaultRequestAdapter($this->authenticationProvider, $this->parseNodeFactory, $this->serializationWriterFactory, $this->guzzleClient, $this->observabilityOption);
    }
}
