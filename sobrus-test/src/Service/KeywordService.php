<?php

namespace App\Service;

class KeywordService
{
    public const BANNED_WORDS = ['the', 'a', 'an'];

    public function findMostFrequentWords(string $content): ?array
    {
        $text = strtolower($content);
        $text = preg_replace("/[^\w\s]/", "", $text);
        $words = explode(" ", $text);

        $wordCounts = [];
        foreach ($words as $word) {
            if (!in_array($word, self::BANNED_WORDS)) {
                if (isset($wordCounts[$word])) {
                    $wordCounts[$word]++;
                } else {
                    $wordCounts[$word] = 1;
                }
            } else {
                return null;
            }
        }

        arsort($wordCounts);
        return array_slice(array_keys($wordCounts), 0, 3); // get only 3 first words which are the keys of the array
    }
}
