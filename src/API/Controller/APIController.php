<?php
// src/API/Controller/APIController.php

namespace App\API\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Contracts\Cache\ItemInterface;

class APIController extends FOSRestController {
  /**
   * @Get("/game/play")
   */
  public function getGamePlayAction() {
    $movie = $this->get('tmdbApi')->getRandomPopularMovie();

    if (rand(0, 1)) {
      $actor = $this->get('tmdbApi')->getRandomActorOfMovie($movie['id']);
    } else {
      $actor = $this->get('tmdbApi')->getRandomPopularActor();
    }

    unset($actor['known_for']);

    return $this->view([
      'movie' => $movie,
      'actor' => $actor,
    ]);
  }

  /**
   * @Post("/game/play") 
   * 
   * @RequestParam(name="movieId")
   * @RequestParam(name="actorId")
   * @RequestParam(name="answer")
   * 
   * @param string $movieId
   * @param string $actorId
   * @param string $answer
  */
  public function postGamePlayAction($movieId, $actorId, $answer) {
    $actors = $this->get('tmdbApi')->getMovieCredits($movieId);

    $isInMovie = in_array($actorId, array_column($actors, 'id'));

    return $this->view([
      'state' =>
        $isInMovie === (boolean) $answer ? 'success' : 'fail'
    ]);
  }
}
