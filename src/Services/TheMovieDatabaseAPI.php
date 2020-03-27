<?php
// src/Services/TheMovieDatabaseAPI.php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;

class TheMovieDatabaseAPI {
  public function __construct($apiKey) {
    $this->apiKey = $apiKey;
    $this->tmdbApiUrl = 'https://api.themoviedb.org/3';
    $this->client = HttpClient::create();
  }

  public function getRandomPopularMovie($range = 100, $pageSize = 20) {
    $lastPage = ceil($range / $pageSize);
    $randomPage = rand(1, $lastPage);

    $randomMovieIndex;

    if ($randomPage === $lastPage) {
      $randomMovieIndex = rand(0, $range % $pageSize);
    } else {
      $randomMovieIndex =  rand(0, $pageSize - 1);
    }

    $response = $this->client->request(
      'GET',
      "{$this->tmdbApiUrl}/movie/popular?page={$randomPage}",
      [
        'query' => [
          'api_key' => $this->apiKey
        ]
      ]
    );

    $randomMovie = $response->toArray()['results'][$randomMovieIndex];

    return $randomMovie;
  }

  public function getRandomPopularActor($range = 300, $pageSize = 20) {
    $lastPage = ceil($range / $pageSize);
    $randomPage = rand(1, $lastPage);

    $randomActorIndex;

    if ($randomPage === $lastPage) {
      $randomActorIndex = rand(0, $range % $pageSize);
    } else {
      $randomActorIndex = rand(0, $pageSize - 1);
    }

    $response = $this->client->request(
      'GET',
      "{$this->tmdbApiUrl}/person/popular?page={$randomPage}",
      [
        "query" => [
          "api_key" => $this->apiKey
        ]
      ]
    );

    $randomActor = $response->toArray()['results'][$randomActorIndex];

    return $randomActor;
  }

  public function getRandomActorOfMovie($movieId) {
    $actors = $this->getMovieCredits($movieId);

    // Get firsts because they are sorted by popularity
    $limit = count($actors) > 5 ? 5 : count($actors) - 1;
    $randomActor = $actors[rand(0, $limit)];

    return $randomActor;
  }

  public function getMovieCredits($movieId) {
    $response = $this->client->request(
      'GET',
      "{$this->tmdbApiUrl}/movie/{$movieId}/credits",
      [
        "query" => [
          "api_key" => $this->apiKey
        ]
      ]
    );

    $actors = $response->toArray()['cast'];

    return $actors;
  }
}