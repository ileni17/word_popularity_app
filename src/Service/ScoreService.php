<?php

namespace App\Service;

class ScoreService
{
    const RESPONSE_KEYWORD = 'total_count';

    public function getScore($negativeResultResponse, $positiveResultResponse): array
    {
        $countNegative = 0;
        $countPositive = 0;

        $responseNegativeRaw = $negativeResultResponse->getContent();
        $negativeResponse = json_decode($responseNegativeRaw, true);

        if (isset($negativeResponse[self::RESPONSE_KEYWORD])) {
            $countNegative = $negativeResponse[self::RESPONSE_KEYWORD];
        }

        $responsePositiveRaw = $positiveResultResponse->getContent();
        $positiveResponse = json_decode($responsePositiveRaw, true);

        if (isset($positiveResponse[self::RESPONSE_KEYWORD])) {
            $countPositive = $positiveResponse[self::RESPONSE_KEYWORD];
        }

        $totalCount = $countNegative + $countPositive;

        $initialShare = $countPositive / $totalCount;
        $adjustedShare = $initialShare * 10;

        return [
            'score' => number_format($adjustedShare, 2),
            'negative' => $countNegative,
            'positive' => $countPositive,
        ];

    }

}
