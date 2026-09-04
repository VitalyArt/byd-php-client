<?php

declare(strict_types=1);

namespace Byd\ApiClient\Transport;

use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Contract\WatchTransportInterface;
use Byd\ApiClient\Enum\WatchEndpoint;
use Byd\ApiClient\Exception\ProtocolException;
use Byd\ApiClient\Exception\TransportException;
use Byd\ApiClient\Serialization\DtoSerializer;

use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function trim;

final readonly class PsrWatchTransport implements WatchTransportInterface
{
    public function __construct(
        private WatchClientConfig $config,
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private DtoSerializer $serializer,
        private LoggerInterface $logger,
    ) {
    }

    public function send(WatchEndpoint $endpoint, object $request): array
    {
        try {
            $body = json_encode($this->serializer->normalize($request), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ProtocolException('Unable to encode watch request.', $exception->getCode(), previous: $exception);
        }

        $message = $this->requestFactory->createRequest('POST', $this->config->resolvedBaseUrl().$endpoint->value)
            ->withHeader('accept-encoding', 'identity')
            ->withHeader('content-type', 'application/json; charset=utf-8')
            ->withHeader('user-agent', 'okhttp/4.12.0')
            ->withBody($this->streamFactory->createStream($body));
        $this->logger->debug('Sending BYD watch request.', ['endpoint' => $endpoint->name]);

        try {
            $response = $this->httpClient->sendRequest($message);
        } catch (ClientExceptionInterface $exception) {
            throw new TransportException("Watch request to {$endpoint->name} failed.", $exception->getCode(), previous: $exception);
        }

        if ($response->getStatusCode() !== 200) {
            throw new TransportException("BYD returned HTTP {$response->getStatusCode()} for {$endpoint->name}.", $response->getStatusCode());
        }

        try {
            $payload = json_decode((string) $response->getBody(), true, flags: JSON_THROW_ON_ERROR);
            if (is_array($payload) && is_string($payload['response'] ?? null)) {
                $payload = json_decode(trim($payload['response']), true, flags: JSON_THROW_ON_ERROR);
            }
        } catch (Throwable $exception) {
            throw new ProtocolException("Invalid watch response from {$endpoint->name}.", $exception->getCode(), previous: $exception);
        }

        if (!is_array($payload)) {
            throw new ProtocolException('Watch response is not a JSON object.');
        }

        $result = [];
        foreach ($payload as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
