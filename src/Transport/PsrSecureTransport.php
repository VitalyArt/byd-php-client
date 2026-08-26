<?php

declare(strict_types=1);

namespace Byd\ApiClient\Transport;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Crypto\BangcleCodec;
use Byd\ApiClient\Dto\Protocol\BangcleRequest;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Exception\ProtocolException;
use Byd\ApiClient\Exception\TransportException;
use Byd\ApiClient\Serialization\DtoSerializer;

use function is_array;
use function is_string;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UnexpectedValueException;

final readonly class PsrSecureTransport implements SecureTransportInterface
{
    public function __construct(
        private ClientConfig $config,
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private BangcleCodec $codec,
        private DtoSerializer $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function send(Endpoint $endpoint, object $request): array
    {
        try {
            $outerJson = json_encode($this->serializer->normalize($request), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $bangcle = new BangcleRequest($this->codec->encodeEnvelope($outerJson));
            $body = json_encode($this->serializer->normalize($bangcle), JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ProtocolException('Unable to encode secure request.', $exception->getCode(), previous: $exception);
        }

        $url = rtrim($this->config->protocol->baseUrl, '/').$endpoint->value;
        $requestMessage = $this->requestFactory->createRequest('POST', $url)
            ->withHeader('accept-encoding', 'identity')
            ->withHeader('content-type', 'application/json; charset=UTF-8')
            ->withHeader('user-agent', 'okhttp/4.12.0')
            ->withBody($this->streamFactory->createStream($body));

        $this->logger->debug('Sending BYD request.', ['endpoint' => $endpoint->name]);

        try {
            $response = $this->httpClient->sendRequest($requestMessage);
        } catch (ClientExceptionInterface $exception) {
            throw new TransportException("Request to {$endpoint->name} failed.", $exception->getCode(), previous: $exception);
        }

        if ($response->getStatusCode() !== 200) {
            throw new TransportException("BYD returned HTTP {$response->getStatusCode()} for {$endpoint->name}.", $response->getStatusCode());
        }

        try {
            $wire = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
            if (!is_array($wire) || !is_string($wire['response'] ?? null)) {
                throw new UnexpectedValueException('Missing response envelope.');
            }

            $decoded = trim($this->codec->decodeEnvelope($wire['response']));
            if (str_starts_with($decoded, 'F{') || str_starts_with($decoded, 'F[')) {
                $decoded = substr($decoded, 1);
            }

            $payload = json_decode($decoded, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ProtocolException("Invalid secure response from {$endpoint->name}.", $exception->getCode(), previous: $exception);
        }

        if (!is_array($payload)) {
            throw new ProtocolException('Secure response is not a JSON object.');
        }

        $object = [];
        foreach ($payload as $key => $value) {
            if (is_string($key)) {
                $object[$key] = $value;
            }
        }

        return $object;
    }
}
