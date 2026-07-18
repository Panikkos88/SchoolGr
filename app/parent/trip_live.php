<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$trip_id=(int)($_GET['id'] ?? 0);

$stmt=$pdo->prepare("
SELECT *
FROM trips
WHERE id=?
");

$stmt->execute([$trip_id]);

$trip=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$trip){
    die("Η εκδρομή δεν βρέθηκε.");
}

$status='';

switch($trip['trip_status']){

case 'planned':
$status='🕒 Αναμένεται αναχώρηση';
break;

case 'departed':
$status='🚌 Το λεωφορείο αναχώρησε';
break;

case 'arrived':
$status='📍 Έφτασαν στην εκδρομή';
break;

case 'returning':
$status='↩️ Επιστρέφουν στο σχολείο';
break;

case 'finished':
$status='🏫 Έφτασαν στο σχολείο';
break;

default:
$status='-';

}
?>

<!DOCTYPE html>

<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Live Εκδρομή</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:#f4f6f9;

}

.card{

border-radius:20px;

box-shadow:0 2px 15px rgba(0,0,0,.1);

}

#map{

height:450px;

border-radius:15px;

}

</style>

</head>

<body>

<div class="container py-4">

<div class="card p-4">

<h2>

🚌 <?= htmlspecialchars($trip['title']) ?>

</h2>

<hr>

<h4>

<?= $status ?>

</h4>

<p>

📅 <?= htmlspecialchars($trip['trip_date']) ?>

</p>

<div id="map"></div>
	</div>

</div>

<script>

let map;
let marker;

function initMap(){

    const position={

        lat:<?= $trip['bus_lat'] ?: 37.9838 ?>,

        lng:<?= $trip['bus_lng'] ?: 23.7275 ?>

    };

    map=new google.maps.Map(

        document.getElementById("map"),

        {

            zoom:14,

            center:position

        }

    );

    marker=new google.maps.Marker({

        position:position,

        map:map,

        title:"Σχολικό Λεωφορείο"

    });

}

function updateBus(){

    fetch("trip_live_data.php?id=<?= $trip_id ?>")

    .then(r=>r.json())

    .then(data=>{

        if(data.lat!=null && data.lng!=null){

            const pos={

                lat:parseFloat(data.lat),

                lng:parseFloat(data.lng)

            };

            marker.setPosition(pos);

            map.panTo(pos);

        }

    });

}

setInterval(updateBus,10000);

</script>

<script
src="https://maps.googleapis.com/maps/api/js?key=ΒΑΛΕ_ΕΔΩ_ΤΟ_GOOGLE_MAPS_API_KEY&callback=initMap"
async
defer>
</script>

</body>

</html>