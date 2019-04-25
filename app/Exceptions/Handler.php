<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $rendered = parent::render($request, $exception);
        $statusCode = $rendered->getStatusCode();

        if ($exception instanceof Missing404Exception) {
            try {
                Artisan::call('elastic:create-index', [
                    'index-configurator' => 'App\\ES\\ArticleIndexConfigurator',
                ]);
                Artisan::call('refresh-scout:import', ['model'=>'App\\Article']);
                info('重新创建索引成功');
            } catch (\Exception $e) {
                info('重新创建索引失败');
            }
        }

        if ($exception instanceof HttpException) {
            return $this->formatHttpException($exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->invalidJson($request, $exception);
        }

        return response()->json([
            'error' => [
                'code'    => $statusCode,
                'message' => $exception->getMessage(),
            ],
        ], $statusCode);
    }

    /**
     * 重写返回格式
     *
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $result = $this->formatErrors($exception);

        return response()->json([
            'error' => [
                'code'    => $exception->status,
                'message' => $exception->getMessage(),
                'errors'  => $result,
            ],
        ], $exception->status);
    }

    /**
     * 重写返回格式
     *
     * @param ValidationException $exception
     * @return array
     */
    protected function formatErrors(ValidationException $exception): array
    {
        $result = [];
        $messages = $exception->errors();
        if ($messages) {
            foreach ($messages as $field => $errors) {
                foreach ($errors as $error) {
                    $result[] = [
                        'field'   => $field,
                        'message' => $error,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param HttpException $exception
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author duc <1025434218@qq.com>
     */
    protected function formatHttpException(HttpException $exception)
    {
        return response()->json([
            'error' => [
                'code'    => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ],
        ], $exception->getStatusCode());
    }
}
