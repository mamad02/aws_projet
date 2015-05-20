<?php
require_once 'vendor/autoload.php';
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation as HTTP;
$app = new Application();
// Configuration
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(),
array('twig.path' => 'template'));
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(),
array('db.options' => array(
'driver' => 'pdo_mysql',
'host' => getenv('IP'),
'user' => substr(getenv('C9_USER'), 0, 16),
'password' => '',
'dbname' => 'c9'
)));

$app->match('/signup', function(Application $app, Request $req) {
    $erreur = '';
                
    if ($req->getMethod() == 'POST') {
        $login = htmlspecialchars($req->request->get('login'));
        $pass = htmlspecialchars($req->request->get('password'));
        
        if ($login && $pass) {
            //$sql = "SELECT id_user, login_user FROM user WHERE login_user = '" . $login . "';";
            //$results = $app['db']->executeQuery($sql)->fetch();
            $app['db']->fetchAssoc("SELECT id_user, login_user FROM user WHERE login_user = ? ;", array($login));
            
            if (!$results) {
                $q = $app['db']->prepare('INSERT INTO user VALUES (?, ?, ?, ?)');
                try {
                if ($q->execute(array('', $login, $pass, 0)) != 1) {
                    throw new Exception('Erreur insertion.');
                }
                return $app->redirect('/');
                }catch (Doctrine\DBAL\DBALException $e) {
                    throw $e;
                }
            }else{
                $erreur = "Utilisateur déjà existant.";
            }
        }/*else {
            $erreur = "Login ou mot de passe vide.";
        }*/
    }
    return $app['twig']->render('signup.twig', array('error' => $erreur, 'session' => $app['session']->get('login_user')));
});



$app->match('/', function(Application $app, Request $req) {
    $erreur = "";
    
    if ($req->getMethod() == 'POST') {
        $title = htmlspecialchars($req->request->get('titre'));
        $desc = htmlspecialchars($req->request->get('description'));
        $start = htmlspecialchars($req->request->get('date_debut'));
        $day = htmlspecialchars($req->request->get('jour'));
        $hour = htmlspecialchars($req->request->get('heure_fin'));
        $min = htmlspecialchars($req->request->get('min_fin'));
        $user =  htmlspecialchars($app['session']->get('id_user'));
        
        if ($hour < 10) {
            $end = $day . " 0" . $hour . ":" . $min . ":00";
        } else {
            $end = $day . " " . $hour . ":" . $min . ":00";
        }
        
        if ($start == $end) {
            
            $erreur = "Impossible de créer l'évenement : la date de début et la date de fin de l'évenement sont identiques";
            return $app['twig']->render('calendrier.twig', array('session' => $app['session']->get('login_user'), 'user' => $app['session']->get('id_user'), 'error' => $erreur));
            
        }
        
            if($title) {
                
                //$sql1 = "SELECT start_event, end_event FROM event WHERE start_event BETWEEN '" . $start . "' AND '" . $end . "' OR start_event BETWEEN '" . $start . "' AND '" . $end . "';";
                //$results1 = $app['db']->executeQuery($sql1)->fetch();
                //$sql2 = "SELECT start_event, end_event FROM event WHERE start_event > '" . $start . "' AND end_event < '" . $end . "';";
                //$results2 = $app['db']->executeQuery($sql2)->fetch();
                
                $results1 = $app['db']->fetchAll("SELECT start_event, end_event FROM event WHERE start_event BETWEEN ? AND ? OR start_event BETWEEN ? AND ? ;", array($start ,$end ,$start, $end));
                       
                $results2 = $app['db']->fetchAll("SELECT start_event, end_event FROM event WHERE start_event > ? AND end_event < ? ;", array($start, $end));
                
                if (!$results1 && !$results2) {
                
                    $q = $app['db']->prepare('INSERT INTO event VALUES (?,?,?,?,?,?)');
                    try {
                    if ($q->execute(array('', $title, $desc, $start, $end, $user)) != 1) {
                        throw new Exception('Erreur insertion.');
                    }
                    return $app->redirect('/');
                    }catch (Doctrine\DBAL\DBALException $e) {
                        throw $e;
                    }
                    
                } else {
                    
                    $erreur = 'Impossible de créer l\'évenement : chevauchement avec un autre évenement';
                    return $app['twig']->render('calendrier.twig', array('session' => $app['session']->get('login_user'), 'user' => $app['session']->get('id_user'), 'error' => $erreur));
                    
                }
                
            }
    }
    
    return $app['twig']->render('calendrier.twig', array('session' => $app['session']->get('login_user'), 'user' => $app['session']->get('id_user'), 'error' => $erreur));
});
   
   
   
$app->match('/login', function(Application $app, Request $req) {
    $erreur = '';
    if ($req->getMethod() == 'POST') {
        $login = htmlspecialchars($req->request->get('login'));
        $pass = htmlspecialchars($req->request->get('password'));
    
        if ($login && $pass) {

            //$sql = "SELECT id_user, login_user, password_user FROM user WHERE login_user = '" . $login . "' AND password_user = '" . $pass . "';";
            //$results = $app['db']->executeQuery($sql)->fetch();
            
            $results = $app['db']->fetchAssoc("SELECT id_user, login_user, password_user FROM user WHERE login_user = ? AND password_user = ? ;",array($login, $pass));

            if ($results) {
                $app['session']->set('login_user', $login);
                $app['session']->set('id_user', $results['id_user']);
                return $app->redirect('/');
            } else {
                $erreur = "Login ou mot de passe incorrect.";
            }
            } else {
            $erreur = "Login ou mot de passe vide.";
        }
    }
    return $app['twig']->render('login.twig', array('error' => $erreur, 'session' => $app['session']->get('login_user')));

}); 
   
   
    
$app->match('/logout', function(Application $app, Request $req) {
    $app['session']->clear();
    return $app->redirect('/');
});
  

$app->match("/events",function(Application $app,Request $req){
    $method = $req->getmethod();
    if($method=="GET") return $app->redirect('/');
        
    $date_start= htmlspecialchars($req->get('date_start'));
    $date_end= htmlspecialchars($req->get('date_end'));
        
    //$sql= "SELECT * FROM event, user WHERE event.id_user = user.id_user AND start_event BETWEEN '" . $date_start . "' AND '" . $date_end . "' ORDER BY start_event ASC";
    //$reponse=$app['db']->fetchAll($sql);
    $reponse = $app['db']->fetchAll("SELECT * FROM event, user WHERE event.id_user = user.id_user AND start_event BETWEEN ? AND ? ORDER BY start_event ASC;" , array($date_start, $date_end));
        
    return $app->json($reponse);

});

$app->match("/delete",function(Application $app,Request $req){
    $method = $req->getmethod();
    if($method=="GET") return $app->redirect('/');
        
    $id_event=htmlspecialchars($req->get('id_event'));
        
    $q = $app['db']->prepare('DELETE FROM event WHERE id_event = ?');
        
    $reponse = $q->execute(array($id_event));
        
    return $app->json($reponse);

});

$app->match("/list_events",function(Application $app) {
    $sql = "SELECT * FROM event";
    $rows = $app['db']->fetchAll($sql);
    return $app->json($rows);
});

$app->match('/update', function(Application $app, Request $req) {
    if ($req->getMethod() != 'POST') {
        return $app->redirect('/');
    }
    if ($req->request->get('doUpdate')) {
        $titre = htmlspecialchars($req->request->get('titre'));
        $desc = htmlspecialchars($req->request->get('description')); 
        $id_event = htmlspecialchars($req->request->get('id_event'));
        
        $q = $app['db']->prepare('UPDATE event SET title_event = ? , desc_event = ? WHERE id_event = ? ;');
        $q->execute(array($titre, $desc, $id_event));
        return $app->redirect('/');
    } 
    
    $erreur = '';
    $id_event = htmlspecialchars($req->request->get('id_event'));
    
    //$sql = 'SELECT * FROM event WHERE id_event = ' . $id_event . ";";
    //$result = $app['db']->executeQuery($sql)->fetch();
    $result = $app['db']->fetchAssoc("SELECT * FROM event WHERE id_event = ?", array($id_event));
    
    return $app['twig']->render('update.twig', array('error' => $erreur, 'session' => $app['session']->get('login_user'), 'event' => $result));

});

$app->run(); 