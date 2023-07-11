<?php declare(strict_types=1);

namespace App\Middleware;

use Api\library\Exception\AuthException;
use http\Exception\RuntimeException;
use Zend\Stdlib\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

class AuthMiddleware
{
    const FORBIDDEN_MESSAGE_EXCEPTION = 'error: Forbidden, not authorized.';

    public function __invoke(Request $request, Response $response, $next): ResponseInterface
    {
        $request = $this->getRequest();
        $jwtHeader = $request->getHeaderLine('Authorization');
        if (empty($jwtHeader) === true) {
            throw new \Api\library\Exception\AuthException('JWT Token required.', 400);
        }
        $jwt = explode('Bearer ', $jwtHeader);
        if (!isset($jwt[1])) {
            throw new \Api\library\Exception\AuthException('JWT Token invalid.', 400);
        }
        $decoded = $this->checkToken($jwt[1]);
        $object = $request->getParsedBody();
        $object['decoded'] = $decoded;

        return $next($request->withParsedBody($object), $response);
    }

    /**
     * @param string $token
     * @return mixed
     * @throws AuthException
     */
    public function checkToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, getenv('SECRET_KEY'), ['HS256']);
            if (is_object($decoded) && isset($decoded->sub)) {
                return $decoded;
            }
            throw new AuthException(self::FORBIDDEN_MESSAGE_EXCEPTION, 403);
        } catch (\UnexpectedValueException $e) {
            throw new AuthException(self::FORBIDDEN_MESSAGE_EXCEPTION, 403);
        } catch (\DomainException $e) {
            throw new AuthException(self::FORBIDDEN_MESSAGE_EXCEPTION, 403);
        }
    }

    public function getParsedBody()
    {
        if ($this->bodyParsed !== false) {
            return $this->bodyParsed;
        }

        if (!$this->body) {
            return null;
        }

        $mediaType = $this->getMediaType();

        // Check if this specific media type has a parser registered first
        if (!isset($this->bodyParsers[$mediaType])) {
            // If not, look for a media type with a structured syntax suffix (RFC 6839)
            $parts = explode('+', $mediaType);
            if (count($parts) >= 2) {
                $mediaType = 'application/' . $parts[count($parts)-1];
            }
        }

        if (isset($this->bodyParsers[$mediaType])) {
            $body = (string)$this->getBody();
            $parsed = $this->bodyParsers[$mediaType]($body);

            if (!is_null($parsed) && !is_object($parsed) && !is_array($parsed)) {
                throw new RuntimeException(
                    'Request body media type parser return value must be an array, an object, or null'
                );
            }
            $this->bodyParsed = $parsed;
            return $this->bodyParsed;
        }

        return null;
    }
}
