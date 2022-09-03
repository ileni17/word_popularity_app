<?php

namespace App\Controller\Api\V1;

use App\Entity\SearchTerm;
use App\Service\RequestService;
use App\Service\ScoreService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\Service\Attribute\Required;
use OpenApi\Attributes as OA;

#[Route('/api/v1', name: 'api_v1')]
class SearchTermControllerV1 extends AbstractController
{
    const ROUTE_INDEX = '_score_index';
    const BASE_URL = 'https://api.github.com/search/issues';
    const ACCEPT_HEADER = 'application/vnd.github+json';
    const NEGATIVE_SUFFIX = ' sucks';
    const POSITIVE_SUFFIX = ' rocks';

    #[Required]
    public RequestService $requestService;

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public ScoreService $scoreService;

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/score', name: self::ROUTE_INDEX, methods: 'GET')]
    #[OA\Response(
        response: 200,
        description: 'Returns the score of the selected term.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: SearchTerm::class))
        )
    )]
    #[OA\Parameter(
        name: 'term',
        description: 'The field used to define search term.',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'score')]
    public function index(Request $request): JsonResponse
    {
        // Get search term from query
        $searchTerm = $request->query->get('term');
        $score = '0.00';

        if ($searchTerm) {

            // Check if the results already exist
            $existingEntry = $this->entityManager->getRepository(SearchTerm::class)->findOneBy(['name' => strtolower($searchTerm)]);

            if (!$existingEntry) {
                $headers = [
                    'Accept' => self::ACCEPT_HEADER,
                    'Content-Type' => 'application/vnd.api+json',
                ];

                // Create query strings for negative and positive results
                $queryStringNegative = 'q=' . '"' . $searchTerm . self::NEGATIVE_SUFFIX . '"';
                $queryStringPositive = 'q=' . '"' . $searchTerm . self::POSITIVE_SUFFIX . '"';

                // Get response for negative results
                $responseNegative = $this->requestService->getResponseData(Request::METHOD_GET, self::BASE_URL, $queryStringNegative, $headers);

                // Get response for positive results
                $responsePositive = $this->requestService->getResponseData(Request::METHOD_GET, self::BASE_URL, $queryStringPositive, $headers);

                // Get results
                $result = $this->scoreService->getScore($responseNegative, $responsePositive);

                $newSearchTerm = new SearchTerm();
                $newSearchTerm->setName(strtolower($searchTerm));
                $newSearchTerm->setNegativeResults($result['negative']);
                $newSearchTerm->setPositiveResults($result['positive']);
                $newSearchTerm->setScore($result['score']);
                $this->entityManager->persist($newSearchTerm);
                $this->entityManager->flush();
            } else {
                $score = $existingEntry->getScore();
            }

            $scoreResponse = [
                'data' => [
                    'type' => 'searchTerm',
                    'id' => strval($existingEntry?->getId() ?? $newSearchTerm?->getId()),
                    'attributes' => [
                        'term' => $searchTerm,
                        'score' => $score ?? $result['score'],
                    ],

                ]
            ];
        } else {
            $scoreResponse = [
                'errors' => [
                    'title' => 'Search term does not exist.'
                ]
            ];
        }

        return $this->json($scoreResponse);
    }
}


