<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
// Validators
use App\Validators\ResponseSetter;
use App\PropertyAccessor\ExceptionPropertyAccessor;

/**
 * ExceptionListener - overwriting response on Exception event to display pure json without htlm
 * @package  Spendings
 * @author   Piotr Rybinski
 */
class ExceptionListener
{
    /** @var ResponseSetter $responseSetter */
    private $responseSetter;
    /** @var ExceptionPropertyAccessor $propertyAccessor */
    private $propertyAccessor;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(ResponseSetter $responseSetter, ExceptionPropertyAccessor $propertyAccessor)
    {
        $this->responseSetter = $responseSetter;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * onKernelException
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // check if getStatusCode method exist, if not set 400
        if (is_callable([$exception, 'getStatusCode'])) {
            $prop_accessor = $this->propertyAccessor->accessExceptionProperties($exception);
            $status_code = $prop_accessor['statusCode'];
        } else {
            $status_code = 400;
        }

        // Set new Response
        $response = new JsonResponse();
        $content = $this->responseSetter->setResponse($status_code, $exception->getMessage());
        $response->setStatusCode($status_code);
        $response->setContent(json_encode($content));
        $event->setResponse($response);
    }
}
