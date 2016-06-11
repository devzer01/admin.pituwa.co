<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'login.twig.html', []);
});

$app->get('/dashboard', function ($request, $response, $args) {
    return $this->view->render($response, 'dashboard.twig.html', []);
});

$app->get('/queue', function ($request, $response, $args) {
    $links = [['date' => '2016-01-01', 'link' => 'http://www.google.com', 'id' => 1],['date' => '2016-01-01', 'link' => 'http://www.google.com', 'id' => 2]];
    return $this->view->render($response, 'queue.twig.html', ['links' => $links]);
});

$app->get('/pending', function ($request, $response, $args) {
    return $this->view->render($response, 'pending.twig.html', []);
});

$app->get('/completed', function ($request, $response, $args) {
    return $this->view->render($response, 'completed.twig.html', []);
});

$app->get('/claim/{id}', function ($request, $response, $args) {
    $id = $args['id'];

})->setName('claim');