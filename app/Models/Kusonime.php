<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use request
use Illuminate\Http\Request;
// use goutte
use Goutte\Client;

class Kusonime extends Model
{
    use HasFactory;

    public function getAnime(Request $request)
    {
        $url = $request->input('url');
        // validate if $url is empty
        if (empty($url)) {
            return response()->json([
                'status' => 'error',
                'message' => 'url is empty'
            ]);
        }
        // scrape kusonime.com using goutte
        $client = new Client();
        $crawler = $client->request('GET', $url);
        // get page title
        $title = $crawler->filter('title')->text();
        $smokeddl = [];
        $download = $crawler->filter('.dlbod')->first()->filter('.smokeddl')->each(function ($node) use (&$smokeddl) {
            $smokettl = [];
            $node->filter('.smokettl')->each(function ($node) use (&$smokettl) {
                $smokettl = $node->text();
            });
            $smokeurl = [];
            $node->filter('.smokeurl')->each(function ($node) use (&$smokeurl) {
                $strong = $node->filter('strong')->text();
                $smokeurl[] = [
                    'title' => $strong,
                    'data' => array()
                ];
                $node->filter('a')->each(function ($node) use (&$smokeurl) {
                    $smokeurl[count($smokeurl) - 1]['data'][] = [
                        'host' => $node->text(),
                        'data' => $node->attr('href')
                    ];
                });
            });
            $smokeddl[] = [
                'download_title' => $smokettl,
                'download_data' => $smokeurl
            ];
        });

        return ['page_title' => $title, 'data' => $smokeddl];
    }
}
