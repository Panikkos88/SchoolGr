<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$pageTitle = $LANG['dashboard'];

require_once '../includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
$stmt = $pdo->query("
SELECT *
FROM school_settings
WHERE id=1
");
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_parents = $pdo->query("SELECT COUNT(*) FROM parents")->fetchColumn();
$total_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$total_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$total_trips = $pdo->query("SELECT COUNT(*) FROM trips")->fetchColumn();
$total_announcements = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();

$total_buses = $pdo->query("
SELECT COUNT(*)
FROM buses
")->fetchColumn();

$health_alerts = $pdo->query("
SELECT COUNT(*)
FROM student_health_cards
WHERE
medical_conditions<>'' OR
allergies<>'' OR
doctor_restrictions<>''
")->fetchColumn();
$pending_signatures = $pdo->query("
SELECT COUNT(*)
FROM trip_signatures
WHERE status='pending'
")->fetchColumn();

$pending_trip_payments = $pdo->query("
SELECT COUNT(*)
FROM trip_payments
WHERE payment_status='pending'
")->fetchColumn();

$today_birthdays = $pdo->query("
SELECT COUNT(*)
FROM students
WHERE
DAY(birth_date)=DAY(CURDATE())
AND
MONTH(birth_date)=MONTH(CURDATE())
")->fetchColumn();
?>

<div class="container py-4">

<div class="text-center mb-4">

<?php if(!empty($school['school_logo'])): ?>

<img
src="../uploads/logo/<?= htmlspecialchars($school['school_logo']); ?>"
style="max-height:120px;"
class="mb-2">

<?php endif; ?>

<h2 class="fw-bold">

<?= htmlspecialchars($school['school_name'] ?? 'SchoolMedia'); ?>

</h2>

<p class="text-muted">
<?= $LANG['school_platform']; ?>
	</p>
	<div class="row g-3 mb-4">

<div class="col-md-4">

<div class="card border-warning shadow-sm">

<div class="card-header bg-warning">

⚠ Εκκρεμότητες

</div>

<div class="card-body">

<p class="mb-2">

📝 Υπογραφές Εκδρομών:
<strong><?= $pending_signatures ?></strong>

</p>

<p class="mb-0">

💳 Πληρωμές Εκδρομών:
<strong><?= $pending_trip_payments ?></strong>

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-danger shadow-sm">

<div class="card-header bg-danger text-white">

❤️ Κάρτες Υγείας

</div>

<div class="card-body text-center">

<h2>

<?= $health_alerts ?>

</h2>

<p>

Μαθητές με ιατρικές πληροφορίες

</p>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-success shadow-sm">

<div class="card-header bg-success text-white">

🎂 Σημερινά Γενέθλια

</div>

<div class="card-body text-center">

<h2>

<?= $today_birthdays ?>

</h2>

<p>

Μαθητές

</p>

</div>

</div>

</div>

	</div>

<div class="row g-3">

<?= dashboard_card(
    'students',
    $LANG['students'],
    (string)$total_students,
    'students.php',
    'primary'
); ?>

<?= dashboard_card(
    'parents',
    $LANG['parents'],
    (string)$total_parents,
    'parents.php',
    'success'
); ?>

<?= dashboard_card(
    'teachers',
    $LANG['teachers'],
    (string)$total_teachers,
    'teachers.php',
    'danger'
); ?>

<?= dashboard_card(
    'classes',
    $LANG['classes'],
    (string)$total_classes,
    'classes.php',
    'warning'
); ?>

<?= dashboard_card(
    'trips',
    $LANG['trips'],
    (string)$total_trips,
    'trips.php',
    'primary'
); ?>

<?= dashboard_card(
    'announcements',
    $LANG['announcements'],
    (string)$total_announcements,
    'announcements.php',
    'info'
); ?>

<?= dashboard_card(
    'finance',
    $LANG['finance'],
    '',
    'finance.php',
    'success'
); ?>

<?= dashboard_card(
    'bus',
    $LANG['buses'],
    (string)$total_buses,
    'buses.php',
    'warning'
); ?>

<?= dashboard_card(
    'settings',
    $LANG['settings'],
    '',
    'settings.php',
    'secondary'
); ?>

<?= dashboard_card(
    'logout',
    $LANG['logout'],
    '',
    '../logout.php',
    'danger'
); ?>
</div>

</div>
<?php require_once '../includes/footer.php'; ?>
