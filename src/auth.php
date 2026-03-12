<?php
// src/auth.php

session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function checkAdmin() {
    checkAuth();
    if ($_SESSION['role'] !== 'admin') {
        echo "Acesso negado. Apenas administradores podem acessar esta página.";
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
