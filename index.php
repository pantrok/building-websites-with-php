<?php 

require "vendor/autoload.php";
date_default_timezone_set('America/Mexico_City');

use Slim\Slim;
use Slim\Views\Twig;
use \Slim\Views\TwigExtension;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//$log = new Logger('name');
//$log->pushHandler(new StreamHandler('/synergy/logs/app.log', Logger::WARNING));
//
//$log->addWarning('Foo');

$app = new Slim(array(
    'view' => new Twig()
));
$view = $app->view();
$view->parserOptions = array(
    'debug' => true
);
$view->parserExtensions = array(
    new TwigExtension(),
);

$app->get('/', function() use($app) {
    $app->render('about.html.twig');
})->name('home');

$app->get('/contact', function() use($app) {
    $app->render('contact.html.twig');
})->name('contact');

$app->post('/contact', function() use($app) {
    $name = $app->request()->post('name');
    $email = $app->request()->post('emial');
    $message = $app->request()->post('msg');
    if (!empty($name) && !empty($email) && !empty($message)) {
        $cleanName = filter_var($name, FILTER_SANITIZE_STRING);
        $cleanEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        $cleanMessage = filter_var($message, FILTER_SANITIZE_STRING);
    } else {
        $app->redirect('/building-websites-with-php/contact');
    }
    
    $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
    $mailer = Swift_Mailer::newInstance($transport);
    $message = Swift_Message::newInstance();
    $message->setSubject('Email from our website');
    $message->setFormat(array(
        $cleanEmail => $name
    ));
    $message->setTo(array(
        'pantrok@gmail.com' => 'daniel'
    ));
    $message->setBody($message);
    $result = $mailer->send($message);
    if ($result > 0) {
        $app->redirect('/building-websites-with-php');
    } else {
        $app->redirect('/building-websites-with-php/contact');
    }
    
});

$app->run();