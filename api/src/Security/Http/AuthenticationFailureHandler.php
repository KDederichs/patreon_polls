<?php

namespace App\Security\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            '@context' => '/context/Error',
            '@type' => 'Error',
            'title' => 'An authentication error has occurred.',
            'description' => $exception->getMessage(),
        ], self::mapExceptionCodeToStatusCode($exception->getCode()));
    }

    private static function mapExceptionCodeToStatusCode($exceptionCode): int
    {
        $canMapToStatusCode = is_int($exceptionCode)
            && $exceptionCode >= 400
            && $exceptionCode < 500;

        return $canMapToStatusCode
            ? $exceptionCode
            : Response::HTTP_UNAUTHORIZED;
    }
}
