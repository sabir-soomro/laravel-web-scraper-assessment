<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
// use Symfony\Component\HttpClient\HttpClient;

class ScrapeMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Top10 Movies from IMDb';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $client->request('GET', route('scrape'));
        return Command::SUCCESS;
    }
}
