<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use goutte
use Goutte\Client;

class Kusonime extends Model
{
    use HasFactory;

    public function getAnime()
    {
        // scrape kusonime.com using goutte
        $client = new Client();
        $crawler = $client->request('GET', 'https://kusonime.com/fairy-tail-subtitle-indonesia-7/');
        // get page title
        $title = $crawler->filter('title')->text();
        $smokeddl = [];
        $download = $crawler->filter('.dlbod')->first()->filter('.smokeddl')->each(function ($node) use (&$smokeddl) {
            $smokettl = [];
            $smokeurl = [];
            $node->filter('.smokettl')->each(function ($node) use (&$smokettl) {
                $smokettl = $node->text();
            });
            $node->filter('.smokeurl')->each(function ($node) use (&$smokeurl) {
                // get all url
                $smokeurl = $node->filter('a')->each(function ($node) {
                    return $node->attr('href');
                });
            });
            $smokeddl[] = [
                'smokettl' => $smokettl,
                'smokeurl' => $smokeurl
            ];
        });

        return ['title' => $title, 'smokeddl' => $smokeddl];
    }
}
