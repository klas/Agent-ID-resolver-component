<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\ApiException;
use App\Exceptions\Handler;
use App\Services\ErrorLogService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\CreatesApplication;

class HandlerTest extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var MockInterface|ErrorLogService
     */
    protected $errorLogService;

    /**
     * @var Handler
     */
    protected $handler;

    /**
     * @var Request
     */
    protected $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->errorLogService = Mockery::mock(ErrorLogService::class);
        $container = $this->app->make(Container::class);
        $this->handler = new Handler($this->errorLogService, $container);

        // Use a real request instead of a mock to avoid expectation issues
        $this->request = Request::create('/api/test', 'GET');
        // Set the 'Accept' header to 'application/json' to make expectsJson() return true
        $this->request->headers->set('Accept', 'application/json');
    }

    public function testRegisterAddsReportableCallback(): void
    {
        $exception = new \Exception('Test exception');

        $this->errorLogService->shouldReceive('logException')
            ->once()
            ->with($exception);

        // Manually invoke reportable callback
        $this->handler->report($exception);
    }

    public function testApiExceptionRendering(): void
    {
        $apiException = Mockery::mock(ApiException::class);
        $expectedResponse = new JsonResponse(['message' => 'API error'], 400);

        $apiException->shouldReceive('render')
            ->once()
            ->andReturn($expectedResponse);

        $result = $this->handler->render($this->request, $apiException);

        $this->assertSame($expectedResponse, $result);
    }

    public function testValidationExceptionRendering(): void
    {
        // Create a real validation exception
        $validator = $this->app->make('validator')->make(
            ['email' => ''], // data
            ['email' => 'required|email'] // rules
        );

        $validationException = new ValidationException($validator);

        $result = $this->handler->render($this->request, $validationException);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(422, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertCount(1, $responseData['errors']);
        $this->assertEquals('422', $responseData['errors'][0]['status']);
        $this->assertEquals('Validation Failed', $responseData['errors'][0]['title']);
        $this->assertStringContainsString('email', $responseData['errors'][0]['detail']);
        $this->assertEquals('/data/attributes/email', $responseData['errors'][0]['source']['pointer']);
    }

    public function testAuthenticationExceptionRendering(): void
    {
        Config::set('app.debug', false);

        $exception = new AuthenticationException('Unauthenticated');

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(401, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(401, $responseData['errors'][0]['status']);
        $this->assertEquals('Unauthenticated', $responseData['errors'][0]['title']);
        $this->assertEquals('You are not authenticated to perform this action.', $responseData['errors'][0]['detail']);
    }

    public function testModelNotFoundExceptionRendering(): void
    {
        Config::set('app.debug', false);

        $exception = new ModelNotFoundException;
        $exception->setModel('User', [1]);

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(404, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(404, $responseData['errors'][0]['status']);
        $this->assertEquals('Resource Not Found', $responseData['errors'][0]['title']);
        $this->assertEquals('The requested resource could not be found.', $responseData['errors'][0]['detail']);
    }

    public function testNotFoundHttpExceptionRendering(): void
    {
        Config::set('app.debug', false);

        $exception = new NotFoundHttpException('X Not Found');

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(404, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(404, $responseData['errors'][0]['status']);
        $this->assertEquals('X Not Found', $responseData['errors'][0]['title']);
        $this->assertEquals('The requested resource does not exist.', $responseData['errors'][0]['detail']);
    }

    public function testMethodNotAllowedHttpExceptionRendering(): void
    {
        Config::set('app.debug', false);

        $exception = new MethodNotAllowedHttpException(['GET'], 'Method not allowed');

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(405, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(405, $responseData['errors'][0]['status']);
        $this->assertEquals('Method Not Allowed', $responseData['errors'][0]['title']);
        $this->assertEquals('The HTTP method is not supported for this endpoint.', $responseData['errors'][0]['detail']);
    }

    public function testGenericExceptionRenderingWithDebugOn(): void
    {
        Config::set('app.debug', true);

        $exceptionMessage = 'Some server error';
        $exception = new \Exception($exceptionMessage);

        $this->errorLogService->shouldReceive('logException')
            ->once()
            ->with($exception);

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(500, $responseData['errors'][0]['status']);
        $this->assertEquals('Internal Server Error', $responseData['errors'][0]['title']);
        $this->assertEquals($exceptionMessage, $responseData['errors'][0]['detail']);
    }

    public function testGenericExceptionRenderingWithDebugOff(): void
    {
        Config::set('app.debug', false);

        $exception = new \Exception('Some server error that should not be exposed');

        $this->errorLogService->shouldReceive('logException')
            ->once()
            ->with($exception);

        $result = $this->handler->render($this->request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(500, $result->getStatusCode());

        $responseData = json_decode($result->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertEquals(500, $responseData['errors'][0]['status']);
        $this->assertEquals('Internal Server Error', $responseData['errors'][0]['title']);
        $this->assertEquals('An unexpected error occurred.', $responseData['errors'][0]['detail']);
    }

    public function testNonJsonRequestsPassthrough(): void
    {
        // Create a request that doesn't expect JSON
        $nonJsonRequest = Request::create('/test', 'GET');
        $nonJsonRequest->headers->set('Accept', 'text/html');

        $exception = new \Exception('Regular exception');

        $result = $this->handler->render($nonJsonRequest, $exception);

        // Should return null to let the parent handler manage non-JSON responses
        $this->assertInstanceOf(Response::class, $result);
    }
}
