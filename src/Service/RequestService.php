<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\Attribute\Required;

class RequestService
{
    #[Required]
    public LoggerInterface $logger;

    /**
     * @param $requestMethod
     * @param $baseUrl
     * @param $queryString
     * @param $headers
     * @return ResponseInterface|null
     * @throws TransportExceptionInterface
     */
    public function getResponseData($requestMethod, $baseUrl, $queryString, $headers): ?ResponseInterface
    {
        $client = HttpClient::create();
        $response = null;

        try {
            $response = $client->request($requestMethod, $baseUrl . '?' . $queryString, [
                'headers' => $headers,
            ]);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $response;
    }
}
