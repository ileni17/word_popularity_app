<?php

namespace App\Tests\Controller;

use App\Controller\Api\V1\SearchTermControllerV1;
use App\Service\ScoreService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class SearchTermControllerV1Test extends TestCase
{
    const TEST_TERM = 'php';

    /**
     * @throws TransportExceptionInterface
     */
    public function testGitHubSearchApiFetch()
    {
        $client = HttpClient::create();
        $testQueryString = 'q=' . '"' . self::TEST_TERM . SearchTermControllerV1::NEGATIVE_SUFFIX . '"';

        $response = $client->request(Request::METHOD_GET, SearchTermControllerV1::BASE_URL . '?' . $testQueryString, [
            'headers' => [
                'Accept' => SearchTermControllerV1::ACCEPT_HEADER,
                'Content-Type' => 'application/vnd.api+json',
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseRaw = $response->getContent();
        $responseJson = json_decode($responseRaw, true);

        $this->assertTrue(isset($responseJson[ScoreService::RESPONSE_KEYWORD]), "'" . ScoreService::RESPONSE_KEYWORD . "'" . ' array key does not exist!');
    }
}
