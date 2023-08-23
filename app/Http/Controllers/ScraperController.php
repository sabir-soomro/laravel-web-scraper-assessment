<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Exception;
use Illuminate\Http\Request;
use Goutte\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

class ScraperController extends Controller
{
    public function scrape()
    {
        $client = new Client(HttpClient::create(['timeout' => 60]));

        try {
            $url = "https://www.imdb.com/chart/top";
            $crawler = $client->request('GET', $url);
        } catch (Exception $e) {
            return $e->getCode() . ' - ' . $e->getMessage();
        }

        $movies = [];

        // Get the top 10 movie details and add them to the $movies array
        $numberOfMovies = 10;
        $movieNodes = $crawler->filter('.ipc-metadata-list li')->slice(0, $numberOfMovies);

        $movieNodes->each(function ($node) use (&$movies) {
            $title = $node->filter('h3.ipc-title__text')->text();
            $year = $node->filter('.cli-title-metadata')->text();
            $rating = $node->filter('span.ipc-rating-star')->attr('aria-label');
            $url = $node->filter('a.ipc-title-link-wrapper')->attr('href');

            $fullUrl = 'https://www.imdb.com' . $url;

            $movies[] = [
                'title' => explode('. ', $title)[1],
                'year' => substr($year, 0, 4),
                'rating' => substr($rating, -3),
                'url' => $fullUrl,
            ];
        });

        $result = $this->saveMovies($movies);
        if ($result) {
            return 'The movie database has been successfully updated.';
        } else {
            return "Failed to database.";
        }
    }

    public function saveMovies($movies)
    {
        if (is_array($movies) && count($movies)) {

            foreach ($movies as $movie) {
                $newObject = Movie::firstOrNew(["url" => $movie['url']]);
                $newObject->title = $movie['title'];
                $newObject->year = $movie['year'];
                $newObject->rating = $movie['rating'];
                $newObject->save();
            }

            return true;
        } else {
            return false;
        }
    }
}
