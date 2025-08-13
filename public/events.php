<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/smarty_config.php';
require_once __DIR__ . '/../models/EventModel.php';

$model = new EventModel($db);

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $model->createEvent($_POST);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $model->updateEvent($_POST['id'], $_POST);
    }
    header('Location: events.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $model->deleteEvent($_GET['delete']);
    header('Location: events.php');
    exit;
}

// Fetch events
$events = $model->getAllEvents();
// var_dump($events); // Check if data is retrieved
$smarty->assign('events', $events);

// Display template
$smarty->display('events_list.tpl');
