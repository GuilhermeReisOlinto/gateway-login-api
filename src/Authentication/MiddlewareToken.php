<?php

namespace App\Authentication;

use App\Authentication\HandlerTokenJwt as AuthenticationHandlerTokenJwt;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;


class MiddlewareToken implements MiddlewareInterface
{
    private $jwtHandler;

    public function __construct(AuthenticationHandlerTokenJwt $jwtHandler)
    {
        $this->jwtHandler = $jwtHandler;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return $this->unauthorizedResponse('Token not found.');
        }

        try {

            $decoded = $this->jwtHandler->decodificateToken($token);

            $request = $request->withAttribute('jwt', $decoded);
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token.');
        };

        return $handler->handle($request);
    }

    private function extractToken(Request $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}