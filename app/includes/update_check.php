<?php

if(!isset($_SESSION['user_id'])){
    return;
}

$stmt = $pdo->query("
SELECT *
FROM system_updates
ORDER BY id DESC
LIMIT 1
");

$latest = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$latest){
    return;
}

$user_stmt = $pdo->prepare("
SELECT last_seen_version
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if(
    $user &&
    $user['last_seen_version'] != $latest['version']
):
?>

<div class="modal fade show"
style="display:block;background:rgba(0,0,0,.6);">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5 class="modal-title">

🚀 Νέα Έκδοση

<?= htmlspecialchars($latest['version']); ?>

</h5>

</div>

<div class="modal-body">

<h5>

<?= htmlspecialchars($latest['title']); ?>

</h5>

<hr>

<?= nl2br(
htmlspecialchars($latest['description'])
); ?>

</div>

<div class="modal-footer">

<a
href="/schoolmedia/update_seen.php?version=<?= urlencode($latest['version']); ?>"
class="btn btn-primary">

Κατάλαβα

</a>

</div>

</div>

</div>

</div>

<?php endif; ?>