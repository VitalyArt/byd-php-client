<?php

declare(strict_types=1);

namespace Byd\ApiClient\Transport;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Crypto\BangcleCodec;
use Byd\ApiClient\Exceptions\BydTransportException;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\GuzzleException;

use function is_array;
use function is_string;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function sprintf;

/**
 * HTTP transport that handles Bangcle envelope encoding.
 */
class SecureTransport implements TransportInterface
{
    public function __construct(private BydConfig $config, private BangcleCodec $codec, private HttpClientInterface $httpClient, private ?LoggerInterface $logger = new NullLogger())
    {
    }

    /**
     * Send a signed request through the Bangcle envelope layer.
     *
     * @param array<string, mixed> $outerPayload
     *
     * @return array<string, mixed>
     *
     * @throws BydTransportException
     */
    public function postSecure(string $endpoint, array $outerPayload): array
    {
        // JSON-encode the outer payload
        $jsonPayload = json_encode($outerPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($jsonPayload === false) {
            throw new BydTransportException('Failed to encode payload to JSON', 0, $endpoint);
        }

        // Bangcle-encode it
        $encoded = $this->codec->encodeEnvelope($jsonPayload);

        $url = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');
        $body = json_encode(['request' => $encoded]);

        $this->logger->debug('HTTP POST ' . $url);

        try {
            $response = $this->httpClient->request('POST', $url, [
                'headers' => [
                    'accept-encoding' => 'identity',
                    'content-type' => 'application/json; charset=UTF-8',
                    'user-agent' => 'okhttp/4.12.0',
                ],
                'body' => $body,
            ]);

            $text = (string) $response->getBody();

            if ($response->getStatusCode() !== 200) {
                throw new BydTransportException(
                    sprintf('HTTP %d from %s: %s', $response->getStatusCode(), $endpoint, substr($text, 0, 200)),
                    $response->getStatusCode(),
                    $endpoint
                );
            }
        } catch (GuzzleException $e) {
            throw new BydTransportException(
                sprintf('Request to %s failed: %s', $endpoint, $e->getMessage()),
                0,
                $endpoint,
                $e
            );
        }

        // Parse response
        $bodyJson = json_decode($text, true);
        if (!is_array($bodyJson)) {
            throw new BydTransportException(
                sprintf('Invalid JSON from %s: %s', $endpoint, substr($text, 0, 200)),
                0,
                $endpoint
            );
        }

        if (!isset($bodyJson['response']) || !is_string($bodyJson['response'])) {
            throw new BydTransportException(
                sprintf('Missing or invalid "response" field from %s', $endpoint),
                0,
                $endpoint
            );
        }

        $responseStr = trim($bodyJson['response']);
        if ($responseStr === '') {
            throw new BydTransportException(
                sprintf('Empty response payload from %s', $endpoint),
                0,
                $endpoint
            );
        }

        // Decode Bangcle envelope
        $decodedText = $this->codec->decodeEnvelope($responseStr);

        // Handle stray F prefix on decoded JSON (observed in some responses)
        $decodedString = trim($decodedText);
        if (str_starts_with($decodedString, 'F{') || str_starts_with($decodedString, 'F[')) {
            $decodedString = substr($decodedString, 1);
        }

        $result = json_decode($decodedString, true);
        if (!is_array($result)) {
            throw new BydTransportException(
                sprintf('Bangcle response from %s is not JSON: %s', $endpoint, substr($decodedString, 0, 64)),
                0,
                $endpoint
            );
        }

        return $result;
    }
}
