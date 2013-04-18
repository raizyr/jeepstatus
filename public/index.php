<?php
require '../vendor/autoload.php';
require '../vots.php';

////////////////////////
// Setup
////////////////////////

//Instantiate Slim
$app = new \Slim\Slim(array(
    'templates.path' => '../views',
));

//Twig hates the config array above
$app->view(new \Slim\Extras\Views\Twig());

////////////////////////
// Routes
////////////////////////

//Redirect POST to get URL for bookmarking
$app->post('/status', function () use ($app) {

    $last = $_POST['last'];
    $vin  = $_POST['vin'];

    if (!$last || !$vin)
    {
        $data['error'] = "You didn't fill out both form fields!!??!?";
        $app->render('error.twig', $data);  
    }
    else
    {
        //Remove whitespace, take last 8 for people that can't read
        $last = preg_replace("/ /","+",trim($last));
        $vin  = substr(trim($vin), -8);

        //Do something
        $app->redirect("/status/$last/$vin/");      
    }


});

//Status 
$app->get('/status/:last/:vin/', function ($last, $vin) use ($app) {

    $jeep = new VOTSService($last, $vin);
    if ($jeep->isValid())
    {
        $data = array(
            'statusCode' => $jeep->getStatusCode(),
            'statusDesc' => $jeep->getStatusDesc(),
            'statusExplanation' => $jeep->getStatusExplanation()
        );

        //Do something
        $app->render('response.twig', $data);
    }
    else
    {
        $data = array(
            'error' => $jeep->getError()
        );
        $app->render('error.twig', $data);        
    }

});

// // Home
$app->get('/', function () use ($app) {

    //Do something
    $app->render('home.twig');

});

////////////////////////
// Go Go
////////////////////////

$app->run();