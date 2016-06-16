<?php
// Routes

$authCheck = function ($request, $response, $next) {
    if (!isset($_SESSION['user'])) {
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    return $next($request, $response);
};

$app->get('/', function ($request, $response, $args) {
    return $this->view->render($response, 'login.twig.html', []);
});

$app->post('/login', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :username AND pass = PASSWORD(:password)");
    $parsedBody = $request->getParsedBody();
    $stmt->execute([':username' => $parsedBody['email'], ':password' => $parsedBody['pass']]);
    $result = $stmt->fetch();
    if (empty($result)) {
        return $this->view->render($response, 'login.twig.html', ['error' => 'incorrect password']);
    } else {
        $_SESSION['user'] = $result;
        if ($result['change_pass'] == 1) {
            return $response->withStatus(302)->withHeader('Location', '/changepass');
        }
        return $response->withStatus(302)->withHeader('Location', '/dashboard');
    }
});

$app->get('/dashboard', function ($request, $response, $args) {
    $args = ['activeDashboard' => 'active', 'activeQueue' => '', 'activePending' => '', 'activeCompleted' => ''];
    $userid = $_SESSION['user']['id'];
    $pdo = $this->pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM link WHERE assinged_user_id IS NULL");
    $stmt->execute();
    $status['queue'] = $stmt->fetch()['cnt'];

    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM link WHERE assinged_user_id = :userid AND link_status_id = 1 ");
    $stmt->execute([':userid' => $userid]);
    $status['pending'] = $stmt->fetch()['cnt'];

    $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM link WHERE assinged_user_id = :userid AND link_status_id = 2 ");
    $stmt->execute([':userid' => $userid]);
    $status['completed'] = $stmt->fetch()['cnt'];

    $args = array_merge($args, $status);

    return $this->view->render($response, 'dashboard.twig.html', $args);
})->add($authCheck);

$app->get('/queue', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT * FROM link WHERE assinged_user_id IS NULL");
    $stmt->execute();
    $links = $stmt->fetchAll();
    $args = ['activeDashboard' => '', 'activeQueue' => 'active', 'activePending' => '', 'activeCompleted' => ''];
    $args = array_merge(['links' => $links], $args);
    return $this->view->render($response, 'queue.twig.html', $args);
})->add($authCheck);

$app->get('/pending', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT * FROM link WHERE assinged_user_id = :userid AND link_status_id = 1 ");
    $userid = $_SESSION['user']['id'];
    $stmt->execute([':userid' => $userid]);
    $links = $stmt->fetchAll();
    $args = ['activeDashboard' => '', 'activeQueue' => '', 'activePending' => 'active', 'activeCompleted' => ''];
    $args = array_merge(['links' => $links], $args);
    return $this->view->render($response, 'pending.twig.html', $args);
})->add($authCheck);

$app->get('/completed', function ($request, $response, $args) {
    $userid = $_SESSION['user']['id'];
    $level_id = $_SESSION['user']['user_level_id'];
    if ($level_id  == 1) {
        $stmt = $this->pdo->prepare("SELECT link.*, article.article_status_id FROM link JOIN article ON article.link_id = link.id WHERE assinged_user_id = :userid AND link_status_id = 2 ");
        $stmt->execute([':userid' => $userid]);
    } else {
        $stmt = $this->pdo->prepare("SELECT link.* FROM link JOIN article ON article.link_id = link.id WHERE link_status_id = 2 AND article.article_status_id != 1");
        $stmt->execute([]);
    }
    $links = $stmt->fetchAll();
    $args = ['activeDashboard' => '', 'activeQueue' => '', 'activePending' => '', 'activeCompleted' => 'active'];
    $args = array_merge(['links' => $links], $args);
    return $this->view->render($response, 'completed.twig.html', $args);
})->add($authCheck);

$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    return $response->withStatus(302)->withHeader('Location', '/');
})->setName('logout')->add($authCheck);

$app->get('/claim/{id}', function ($request, $response, $args) {
    $id = $args['id'];
    $userid = $_SESSION['user']['id'];
    $stmt = $this->pdo->prepare("UPDATE link SET assinged_user_id = :userid, 
        assinged_datetime = NOW(), 
        expire_datetime =  NOW() + INTERVAL 24 HOUR,
        link_status_id = 1 WHERE id = :id ");
    $stmt->execute([ ':userid' => $userid, ':id' => $id]);
    return $response->withStatus(302)->withHeader('Location', '/queue');

})->setName('claim')->add($authCheck);

$app->get('/write/{id}', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT * FROM link WHERE id = :id");
    $id = $args['id'];
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch();
    $args = ['activeDashboard' => '', 'activeQueue' => '', 'activePending' => '', 'activeCompleted' => 'active'];
    $args = array_merge($args, $result);
    return $this->view->render($response, 'write.twig.html', $args);
})->setName('write')->add($authCheck);

$app->post('/write', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();
    $id = $parsedBody['id'];
    $userid = $_SESSION['user']['id'];
    $stmt = $this->pdo->prepare("UPDATE link SET completed_datetime = NOW(), link_status_id = 2 WHERE id = :id ");
    $stmt->execute([':id' => $id]);

    $stmt = $this->pdo->prepare("INSERT INTO article (link_id, author_user_id, content, created_datetime) 
        VALUES (:link, :userid, :content, NOW())");
    $stmt->execute([':link' => $id, ':userid' => $userid, ':content' => $parsedBody['content']]);

    return $response->withStatus(302)->withHeader('Location', '/completed');

})->setName('write_post')->add($authCheck);

$app->get('/read/{id}', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT * FROM article JOIN link ON link.id = article.link_id WHERE link_id = :id");
    $id = $args['id'];
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch();
    $args = ['activeDashboard' => '', 'activeQueue' => '', 'activePending' => '', 'activeCompleted' => 'active'];
    $args = array_merge($args, $result);
    return $this->view->render($response, 'read.twig.html', $args);
})->setName('read')->add($authCheck);

$app->get('/edit/{id}', function ($request, $response, $args) {
    $stmt = $this->pdo->prepare("SELECT link.id, article.id AS article_id, article.content, link.url FROM article JOIN link ON link.id = article.link_id WHERE link_id = :id");
    $id = $args['id'];
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch();
    $args = ['activeDashboard' => '', 'activeQueue' => '', 'activePending' => '', 'activeCompleted' => 'active'];
    $args = array_merge($args, $result);
    return $this->view->render($response, 'edit.twig.html', $args);
})->setName('read')->add($authCheck);

$app->post('/edit', function ($request, $response, $args) {

    $parsedBody = $request->getParsedBody();
    $userid = $_SESSION['user']['id'];

    $stmt = $this->pdo->prepare("UPDATE article SET content = :content, updated_datetime = NOW() WHERE id = :id AND author_user_id = :userid");
    $stmt->execute([':content' => $parsedBody['content'], ':id' => $parsedBody['article_id'], ':userid' => $userid]);

    return $response->withStatus(302)->withHeader('Location', '/completed');

});

$app->get('/addlink', function ($request, $response, $args) {
    return $this->view->render($response, 'linkadd.twig.html', $args);
})->setName('addlink')->add($authCheck);


$app->get('/changepass', function ($request, $response, $args) {
    return $this->view->render($response, 'changepass.twig.html', $args);
})->setName('changepass')->add($authCheck);

$app->post('/changepass', function ($request, $response, $args) {
    $pdo = $this->pdo;
    $parsedBody = $request->getParsedBody();
    $userid = $_SESSION['user']['id'];
    $stmt = $pdo->prepare("UPDATE user SET pass = PASSWORD(:pass), change_pass = 0 WHERE id = :id");
    $stmt->execute([":pass" => $parsedBody['pass'], ":id" => $userid]);

    return $response->withStatus(302)->withHeader('Location', '/dashboard');

})->setName('changepasspost')->add($authCheck);


$app->post('/addlink', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();
    $userid = $_SESSION['user']['id'];
    $stmt = $this->pdo->prepare("INSERT INTO link(url, added_user_id, added_datetime, link_status_id) 
        VALUES (:url, :userid, NOW(), 0)");
    $stmt->execute([':url' => $parsedBody['url'], ':userid' => $userid]);
    return $response->withStatus(302)->withHeader('Location', '/addlink');
})->setName('addlink')->add($authCheck);
