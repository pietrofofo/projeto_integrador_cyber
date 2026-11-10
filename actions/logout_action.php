<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

logout_usuario();
redirect('/index.php');
