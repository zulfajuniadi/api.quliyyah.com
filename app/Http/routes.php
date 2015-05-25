<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() {
    return app('elasticsearch')->search([
        'index' => 'youdio',
        'type'  => 'videos'
    ]);
});

$app->get('/status', function() {
    return view('status');
});

$app->get('/sync', function(Illuminate\Http\Request $request) {

    $newLastSync = date('c');

    $lastSync = $request->input('lastSync');

    $searchParams = [
        'index' => 'youdio',
        'type'  => 'videos',
        'size'  => 100000,
        'sort'  => [
            'publishedAt:desc'
        ],
        'body' => [
            'query' => [
                'filtered' => [
                    'filter' => [
                        'or' => [
                            [
                                'range' => [
                                    'deletedAt' => [
                                        'gt' => $lastSync
                                    ]
                                ]
                            ],
                            [
                                'range' => [
                                    'updatedAt' => [
                                        'gt' => $lastSync
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    // $searchParams['body']['query']['range']['publishedAt'] = ;

    $videos = app('elasticsearch')->search($searchParams);

    foreach ($videos as $key => $value) {
        # code...
    }

    return response()->json([
        'lastSync' => $newLastSync,
        'videos'   => array_map(function($video){
            return $video['_source'];
        }, $videos['hits']['hits'])
    ]);
});
