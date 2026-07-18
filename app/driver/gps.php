<?php

require_once '../config/database.php';

$bus_id = (int)($_GET['bus_id'] ?? 0);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>GPS Driver</title>
</head>

<body>

<h2>GPS Λεωφορείου</h2>

<p id="status">Σύνδεση...</p>

<script>

function updateGPS(){

navigator.geolocation.getCurrentPosition(

function(position){

fetch(
'update_gps.php',
{
method:'POST',
headers:{
'Content-Type':'application/x-www-form-urlencoded'
},
body:
'bus_id=<?=$bus_id;?>'+
'&lat='+position.coords.latitude+
'&lng='+position.coords.longitude
}
);

document.getElementById('status').innerHTML =
'GPS ενημερώθηκε';

}

);

}

setInterval(updateGPS,10000);

updateGPS();

</script>

</body>
</html>