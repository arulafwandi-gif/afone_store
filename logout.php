<?php
require_once __DIR__ . '/includes/helpers.php';
session_destroy();
session_start();
flash('Kamu sudah logout.', 'success');
redirect('login.php');
