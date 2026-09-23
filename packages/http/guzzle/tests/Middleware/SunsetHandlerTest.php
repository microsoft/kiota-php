<?php

namespace Microsoft\Kiota\Http\Test\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Microsoft\Kiota\Http\Middleware\KiotaMiddleware;
use Microsoft\Kiota\Http\Middleware\Options\SunsetOption;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class SunsetHandlerTest extends TestCase
{
    public function testSunsetHandlerEnabledByDefaultWithSunsetHeader(): void
    {
        $option = new SunsetOption();
        $this->assertTrue($option->getEnabled());
        
        $mockResponse = [
            new Response(200, ['Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT'])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertTrue($response->hasHeader('Sunset'));
    }

    public function testSunsetHandlerWithLinkHeader(): void
    {
        $option = new SunsetOption();
        $mockResponse = [
            new Response(200, [
                'Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT',
                'Link' => '<https://api.github.com/foo/bar>; rel="sunset"'
            ])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertTrue($response->hasHeader('Sunset'));
        $this->assertTrue($response->hasHeader('Link'));
    }

    public function testSunsetHandlerDisabled(): void
    {
        $option = new SunsetOption(false);
        $mockResponse = [
            new Response(200, ['Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT'])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertFalse($option->getEnabled());
        $this->assertTrue($response->hasHeader('Sunset'));
    }

    public function testSunsetHandlerOptionOverriddenPerRequest(): void
    {
        $globalOption = new SunsetOption(false);
        $requestOption = new SunsetOption(true);
        
        $mockResponse = [
            new Response(200, ['Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT'])
        ];
        $response = $this->executeMockRequest($mockResponse, $globalOption, [
            SunsetOption::class => $requestOption
        ]);
        
        // Global option shouldn't be touched, it remains false. The handler uses the request option.
        $this->assertFalse($globalOption->getEnabled());
        $this->assertTrue($response->hasHeader('Sunset'));
    }

    public function testSunsetHandlerWithMultipleLinkHeaders(): void
    {
        $option = new SunsetOption();
        $mockResponse = [
            new Response(200, [
                'Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT',
                'Link' => [
                    '<https://api.github.com/foo/bar/next>; rel="next"',
                    '<https://api.github.com/foo/bar/sunset>; rel="sunset"'
                ]
            ])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertTrue($response->hasHeader('Sunset'));
        $this->assertTrue($response->hasHeader('Link'));
    }

    public function testSunsetHandlerWithNoSunsetHeaderDoesNothing(): void
    {
        $option = new SunsetOption(true);
        $mockResponse = [
            new Response(200, ['X-Custom-Header' => 'Values'])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertFalse($response->hasHeader('Sunset'));
    }

    public function testSunsetHandlerParsesCommaSeparatedLinkValues(): void
    {
        $option = new SunsetOption();
        // RFC 8288: multiple link-values in a single header line, comma-separated
        $mockResponse = [
            new Response(200, [
                'Sunset' => 'Sun, 11 Nov 2024 08:49:37 GMT',
                'Link' => '<https://api.example.com/v2>; rel="successor-version", <https://developer.example.com/sunset>; rel="sunset"'
            ])
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        $this->assertTrue($response->hasHeader('Sunset'));
        $this->assertTrue($response->hasHeader('Link'));
    }

    private function executeMockRequest(array $mockResponses, ?SunsetOption $sunsetOption = null, array $requestOptions = []): ResponseInterface
    {
        $mockHandler = new MockHandler($mockResponses);
        $handlerStack = new HandlerStack($mockHandler);
        $handlerStack->push(KiotaMiddleware::sunset($sunsetOption));

        $guzzleClient = new Client(['handler' => $handlerStack, 'http_errors' => false]);
        return $guzzleClient->request('GET', '/', $requestOptions);
    }
}
