<!DOCTYPE html>

<html lang="<?= $lang ?>">

<head>

	<title><?= htmlspecialchars($pageTitle) ?></title>

<?php require_once __DIR__.'/theme.php'; ?>

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">

<div class="container-fluid">

<a class="navbar-brand" href="dashboard.php">

<?= htmlspecialchars($school['school_name'] ?? 'SchoolMedia') ?>

</a>

<div
class="collapse navbar-collapse"
id="topmenu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">

<a class="nav-link" href="notifications.php">

	<i class="<?= sm_icon('messages'); ?>"></i>

</a>

</li>

<li class="nav-item">

<a class="nav-link" href="messages.php">

	<i class="bi bi-envelope-fill"></i>

</a>

</li>

<li class="nav-item dropdown">

<a
class="nav-link dropdown-toggle"
href="#"
data-bs-toggle="dropdown">

	<i class="bi bi-person-circle"></i>

<?= htmlspecialchars($_SESSION['name'] ?? 'User'); ?>

</a>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<a
class="dropdown-item"
href="profile.php">

<i class="bi bi-person"></i>
Το προφίλ μου

</a>

</li>

<li>

<a
class="dropdown-item"
href="settings.php">

<i class="<?= sm_icon('settings'); ?>"></i>
Ρυθμίσεις

</a>

</li>

<li><hr class="dropdown-divider"></li>

<li>

<a
class="dropdown-item text-danger"
href="../logout.php">

<i class="<?= sm_icon('logout'); ?>"></i>
Αποσύνδεση

</a>

</li>

</ul>

</li>

</ul>

</div>

</div>

</nav>