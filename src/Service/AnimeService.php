<?php

namespace App\Service;

use App\Entity\Anime;

class AnimeService
{
    public function mapAnimeObject(Anime $anime): array
    {
        return [
            'title' => $anime->getName(),
            'description' => $anime->getDescription(),
            'image' => $anime->getImage(),
            'studio' => $anime->getStudio(),
            'episodeAmount' => count($anime->getEpisodes()),
            'translateBy' => $anime->getTranslateBy(),
            'tags' => $anime->getTags(),
            'season' => $anime->getSeason()
        ];
    }
}