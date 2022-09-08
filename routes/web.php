<?php

use App\Http\Controllers\KusonimeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
// use facade request
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return [
        'index' => true,
        'message' => 'Welcome to Azusa API',
        'author' => 'Azusa ID',
    ];
});

Route::get('/kusonime', [KusonimeController::class, 'index']);
Route::get('/instagram', function () {
    try {
        $url = request()->query('url');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url . '?__a=1&__d=dis', ['proxy' => 'http://dbb39cd56b8640d58d46f6847c65c34ab3aaf3ab425@proxy.scrape.do:8080', 'verify' => false]) or die('Error');
        $json = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_last_error();
        } else {
            $data = $json->graphql->shortcode_media;
            $data = json_decode(json_encode($data), true);
            $type = $data['__typename'];
            switch ($type) {
                case 'GraphImage':
                    $result = [
                        'status' => true,
                        'code' => 200,
                        'type' => 'image',
                        'data' => [
                            'image' => $data['display_resources'],
                            'high_resolution' => collect($data['display_resources'])->last(),
                            'caption' => $data['edge_media_to_caption']['edges'][0]['node']['text'],
                            'shortcode' => $data['shortcode'],
                            'taken_at_timestamp' => $data['taken_at_timestamp'],
                            'owner' => [
                                'id' => $data['owner']['id'],
                                'username' => $data['owner']['username'],
                                'full_name' => $data['owner']['full_name'],
                                'profile_pic_url' => $data['owner']['profile_pic_url'],
                                'is_private' => $data['owner']['is_private'],
                                'is_verified' => $data['owner']['is_verified'],
                            ],
                        ]
                    ];
                    return $result;
                    break;
                case 'GraphVideo':
                    $result = [
                        'status' => true,
                        'code' => 200,
                        'type' => 'video',
                        'data' => [
                            'video' => $data['video_url'],
                            'caption' => $data['edge_media_to_caption']['edges'][0]['node']['text'],
                            'shortcode' => $data['shortcode'],
                            'video_duration' => $data['video_duration'],
                            'taken_at_timestamp' => $data['taken_at_timestamp'],
                            'owner' => [
                                'id' => $data['owner']['id'],
                                'username' => $data['owner']['username'],
                                'full_name' => $data['owner']['full_name'],
                                'profile_pic_url' => $data['owner']['profile_pic_url'],
                                'is_private' => $data['owner']['is_private'],
                                'is_verified' => $data['owner']['is_verified'],
                            ],
                        ]
                    ];
                    return $result;
                    break;
                case 'GraphSidecar':
                    $arr_node = [];
                    foreach ($data['edge_sidecar_to_children']['edges'] as $key => $value) {
                        $arr_node[] = $value['node'];
                    }
                    $result = [
                        'status' => true,
                        'code' => 200,
                        'type' => 'sidecar',
                        'data' => [
                            'sidecar' => $arr_node,
                            'caption' => $data['edge_media_to_caption']['edges'][0]['node']['text'],
                            'shortcode' => $data['shortcode'],
                            'slide_count' => count($data['edge_sidecar_to_children']['edges']),
                            'taken_at_timestamp' => $data['taken_at_timestamp'],
                            'owner' => [
                                'id' => $data['owner']['id'],
                                'username' => $data['owner']['username'],
                                'full_name' => $data['owner']['full_name'],
                                'profile_pic_url' => $data['owner']['profile_pic_url'],
                                'is_private' => $data['owner']['is_private'],
                                'is_verified' => $data['owner']['is_verified'],
                            ],
                        ]
                    ];
                    return $result;
                    break;
                default:
                    $result = [
                        'status' => false,
                        'code' => 404,
                        'message' => 'Not Found'
                    ];
                    return $result;
                    break;
            }
        }
    } catch (\Throwable $th) {
        if ($th->getCode() == 404) {
            return [
                'error' => true,
                'code' => $th->getCode(),
                'message' => 'Post Not Found!'
            ];
        } else {
            return [
                'error' => true,
                'code' => $th->getCode(),
                'message' => 'Something went wrong'
            ];
        }
    }
});
