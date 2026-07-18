<?php

$home="../index.php";

if(isset($_SESSION['role'])){

    switch($_SESSION['role']){

        case 'admin':
            $home="../admin/index.php";
        break;

        case 'director':
            $home="../director/index.php";
        break;

        case 'teacher':
            $home="../teacher_portal/index.php";
        break;

        case 'student':
            $home="../student_portal/index.php";
        break;

        case 'parent':
            $home="../parent_portal/index.php";
        break;

        case 'driver':
            $home="../driver_portal/index.php";
        break;

        case 'secretary':
            $home="../secretary/index.php";
        break;

    }

}
?>

<!-- Floating Buttons -->

<div
style="
position:fixed;
bottom:20px;
right:20px;
display:flex;
flex-direction:column;
gap:12px;
z-index:99999;
">

<button
onclick="window.location.href='<?= $home ?>';"
class="btn btn-success rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:28px;
">

🏠

</button>

<button
onclick="history.back();"
class="btn btn-primary rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:28px;
">

←

</button>

<button
onclick="window.scrollTo({
top:0,
behavior:'smooth'
});"

class="btn btn-dark rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
">

⬆️

</button>

</div>