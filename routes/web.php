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
        $response = $client->request('GET', $url . '?__a=1&__d=dis', ['proxy' => env('SCRAPEDO_PROXY'), 'verify' => false]) or die('Error');
        $json = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_last_error();
        } else {
            $data = $json->graphql->shortcode_media;
            $data = json_decode(json_encode($data), true);
            $type = (isset($data['__typename']) && !empty($data['__typename'])) ? $data['__typename'] : 'unknown';
            switch ($type) {
                case 'GraphImage':
                    $result = [
                        'status' => true,
                        'code' => 200,
                        'type' => 'image',
                        'data' => [
                            'image' => (isset($data['display_resources']) && !empty($data['display_resources'])) ? $data['display_resources'] : null,
                            // 'high_resolution' => collect($data['display_resources'])->last(),
                            'high_resolution' => (isset($data['display_resources']) && !empty($data['display_resources'])) ? collect($data['display_resources'])->last() : null,
                            'caption' => (isset($data['edge_media_to_caption']['edges'][0]['node']['text']) && !empty($data['edge_media_to_caption']['edges'][0]['node']['text'])) ? $data['edge_media_to_caption']['edges'][0]['node']['text'] : null,
                            'shortcode' => (isset($data['shortcode']) && !empty($data['shortcode'])) ? $data['shortcode'] : null,
                            'taken_at_timestamp' => (isset($data['taken_at_timestamp']) && !empty($data['taken_at_timestamp'])) ? $data['taken_at_timestamp'] : null,
                            'owner' => [
                                'id' => (isset($data['owner']['id']) && !empty($data['owner']['id']) ? $data['owner']['id'] : null),
                                'username' => (isset($data['owner']['username']) && !empty($data['owner']['username']) ? $data['owner']['username'] : null),
                                'full_name' => (isset($data['owner']['full_name']) && !empty($data['owner']['full_name']) ? $data['owner']['full_name'] : null),
                                'profile_pic_url' => (isset($data['owner']['profile_pic_url']) && !empty($data['owner']['profile_pic_url']) ? $data['owner']['profile_pic_url'] : null),
                                'is_private' => (isset($data['owner']['is_private']) && !empty($data['owner']['is_private']) ? $data['owner']['is_private'] : null),
                                'is_verified' => (isset($data['owner']['is_verified']) && !empty($data['owner']['is_verified']) ? $data['owner']['is_verified'] : null),
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
                            'caption' => (isset($data['edge_media_to_caption']['edges'][0]['node']['text']) && !empty($data['edge_media_to_caption']['edges'][0]['node']['text']) ? $data['edge_media_to_caption']['edges'][0]['node']['text'] : null),
                            'shortcode' => (isset($data['shortcode']) && !empty($data['shortcode']) ? $data['shortcode'] : null),
                            'video_duration' => (isset($data['video_duration']) && !empty($data['video_duration']) ? $data['video_duration'] : null),
                            'taken_at_timestamp' => (isset($data['taken_at_timestamp']) && !empty($data['taken_at_timestamp']) ? $data['taken_at_timestamp'] : null),
                            'owner' => [
                                'id' => (isset($data['owner']['id']) && !empty($data['owner']['id']) ? $data['owner']['id'] : null),
                                'username' => (isset($data['owner']['username']) && !empty($data['owner']['username']) ? $data['owner']['username'] : null),
                                'full_name' => (isset($data['owner']['full_name']) && !empty($data['owner']['full_name']) ? $data['owner']['full_name'] : null),
                                'profile_pic_url' => (isset($data['owner']['profile_pic_url']) && !empty($data['owner']['profile_pic_url']) ? $data['owner']['profile_pic_url'] : null),
                                'is_private' => (isset($data['owner']['is_private']) && !empty($data['owner']['is_private']) ? $data['owner']['is_private'] : null),
                                'is_verified' => (isset($data['owner']['is_verified']) && !empty($data['owner']['is_verified']) ? $data['owner']['is_verified'] : null),
                            ],
                        ]
                    ];
                    return $result;
                    break;
                case 'GraphSidecar':
                    $arr_node = [];
                    if (isset($data['edge_sidecar_to_children']['edges']) && !empty($data['edge_sidecar_to_children']['edges'])) {
                        foreach ($data['edge_sidecar_to_children']['edges'] as $key => $value) {
                            $arr_node[] = $value['node'];
                        }
                    }
                    $result = [
                        'status' => true,
                        'code' => 200,
                        'type' => 'sidecar',
                        'data' => [
                            'sidecar' => (isset($arr_node) && !empty($arr_node) ? $arr_node : null),
                            'caption' => (isset($data['edge_media_to_caption']['edges'][0]['node']['text']) && !empty($data['edge_media_to_caption']['edges'][0]['node']['text']) ? $data['edge_media_to_caption']['edges'][0]['node']['text'] : null),
                            'shortcode' => (isset($data['shortcode']) && !empty($data['shortcode']) ? $data['shortcode'] : null),
                            'slide_count' => (isset($data['edge_sidecar_to_children']['edges']) && !empty($data['edge_sidecar_to_children']['edges']) ? count($data['edge_sidecar_to_children']['edges']) : null),
                            'taken_at_timestamp' => (isset($data['taken_at_timestamp']) && !empty($data['taken_at_timestamp']) ? $data['taken_at_timestamp'] : null),
                            'owner' => [
                                'id' => (isset($data['owner']['id']) && !empty($data['owner']['id']) ? $data['owner']['id'] : null),
                                'username' => (isset($data['owner']['username']) && !empty($data['owner']['username']) ? $data['owner']['username'] : null),
                                'full_name' => (isset($data['owner']['full_name']) && !empty($data['owner']['full_name']) ? $data['owner']['full_name'] : null),
                                'profile_pic_url' => (isset($data['owner']['profile_pic_url']) && !empty($data['owner']['profile_pic_url']) ? $data['owner']['profile_pic_url'] : null),
                                'is_private' => (isset($data['owner']['is_private']) && !empty($data['owner']['is_private']) ? $data['owner']['is_private'] : null),
                                'is_verified' => (isset($data['owner']['is_verified']) && !empty($data['owner']['is_verified']) ? $data['owner']['is_verified'] : null),
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
                'message' => 'Something went wrong',
            ];
        }
    }
});
