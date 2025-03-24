<?php
require_once '../includes/auth.php';

// Oturumu sonlandır
session_destroy();

// Login sayfasına yönlendir
header('Location: ../login.php');
exit; 