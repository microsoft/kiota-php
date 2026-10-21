<?php

namespace Microsoft\Kiota\Http\Test\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Microsoft\Kiota\Http\Middleware\KiotaMiddleware;
use Microsoft\Kiota\Http\Middleware\Options\BodyInspectionOption;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class BodyInspectionHandlerTest extends TestCase
{
    private static $requestBody = 'hello world request';
    private static $responseBody = 'hello world response';

    public function testBodyInspectionDisabledByDefault(): void
    {
        $option = new BodyInspectionOption();
        $mockResponse = [
            new Response(200, [], self::$responseBody)
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        
        $this->assertNull($option->getRequestBody());
        $this->assertNull($option->getResponseBody());
        // Original stream should be untouched and readable
        $this->assertEquals(self::$responseBody, $response->getBody()->getContents());
    }

    public function testRequestBodyCaptured(): void
    {
        $option = new BodyInspectionOption(true, false);
        $mockResponse = [
            new Response(200, [], self::$responseBody)
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        
        $this->assertNotNull($option->getRequestBody());
        $this->assertEquals(self::$requestBody, $option->getRequestBody()->getContents());
        $this->assertNull($option->getResponseBody());
    }

    public function testResponseBodyCaptured(): void
    {
        $option = new BodyInspectionOption(false, true);
        $mockResponse = [
            new Response(200, [], self::$responseBody)
        ];
        $response = $this->executeMockRequest($mockResponse, $option);
        
        $this->assertNull($option->getRequestBody());
        $this->assertNotNull($option->getResponseBody());
        $this->assertEquals(self::$responseBody, $option->getResponseBody()->getContents());
        
        // Ensure original stream is rewinded and readable
        $this->assertEquals(self::$responseBody, $response->getBody()->getContents());
    }

    public function testBodyInspectionHandlesNonSeekableStream(): void
    {
        $option = new BodyInspectionOption(true, true);
        
        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $stream->method('isSeekable')->willReturn(false);
        $stream->method('getContents')->willReturn(self::$responseBody);

        $mockResponse = [
            new Response(200, [], $stream)
        ];
        
        $this->executeMockRequest($mockResponse, $option);
        
        // Since the response body is not seekable, it shouldn't be captured to avoid exhausting it
        $this->assertNull($option->getResponseBody());
    }

    public function testBodyInspectionOptionOverriddenPerRequest(): void
    {
        $globalOption = new BodyInspectionOption(false, false);
        $requestOption = new BodyInspectionOption(true, true);
        
        $mockResponse = [
            new Response(200, [], self::$responseBody)
        ];
        
        $response = $this->executeMockRequest($mockResponse, $globalOption, [
            BodyInspectionOption::class => $requestOption
        ]);
        
        // The request-level option should be populated instead of the global one
        $this->assertNotNull($requestOption->getRequestBody());
        $this->assertNotNull($requestOption->getResponseBody());
        
        $this->assertNull($globalOption->getRequestBody());
        $this->assertNull($globalOption->getResponseBody());
    }

    private function executeMockRequest(array $mockResponses, ?BodyInspectionOption $bodyInspectionOption = null, array $requestOptions = []): ResponseInterface
    {
        $mockHandler = new MockHandler($mockResponses);
        $handlerStack = new HandlerStack($mockHandler);
        $handlerStack->push(KiotaMiddleware::bodyInspection($bodyInspectionOption));

        $guzzleClient = new Client(['handler' => $handlerStack, 'http_errors' => false]);
        $options = array_merge($requestOptions, [
            'body' => self::$requestBody
        ]);
        return $guzzleClient->request('POST', '/', $options);
    }
}
