<?php
// Step 1: Connect to the MySQL Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rental services";

$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Step 2: Retrieve ID from URL
$id = isset($_GET['id']) ? ($_GET['id']) : 0;

// Step 3: Fetch location from 'products' table based on ID
$sql_products = "SELECT location FROM products WHERE id = $id";
$result_products = $con->query($sql_products);

if ($result_products->num_rows > 0) {
    $row_products = $result_products->fetch_assoc();
    $location = $row_products['location'];

    // Step 4: Retrieve data from 'markers' table based on location
    $sql_markers = "SELECT address, lat, lng FROM markers WHERE address = '$location'";
    $result_markers = $con->query($sql_markers);

    // Step 5: Fetch Data and Store in Array
    $properties = [];
    if ($result_markers->num_rows > 0) {
        while($row_markers = $result_markers->fetch_assoc()) {
            $properties[] = $row_markers;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Property Locations</title>
</head>
<body>
    <h1>Property Locations</h1>
    <div id="map" style="height: 600px;"></div> <!-- This div will contain the Google Map -->
    <ul>
    <?php
    // Loop through each property and display its information
    foreach ($properties as $property) {
        echo "<li>";
        echo "Address: " . $property['address'] . "<br>";
        echo "Latitude: " . $property['lat'] . "<br>";
        echo "Longitude: " . $property['lng'] . "<br>";
        echo "</li>";
    }
    ?>
    </ul>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwR0O_nqaS8dy6JnO8JRWqK1FqZrTlBfw&callback=initMap" async defer></script>
    <script>
        function initMap() {
            var latitudes = [];
            var longitudes = [];

        <?php  foreach ($properties as $property) { ?>
        latitudes.push(<?php echo $property['lat']; ?>);
        longitudes.push(<?php echo $property['lng']; ?>);
        <?php } ?>

        var center = {
            lat: average(latitudes),
            lng: average(longitudes)
    };
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: center // Adjust center coordinates as needed
            });

            <?php
            // Loop through each property and add a marker on the map
            foreach ($properties as $property) {
                echo "var marker = new google.maps.Marker({
                    position: {lat: {$property['lat']}, lng: {$property['lng']}},
                    map: map,
                    title: '{$property['address']}'
                });";
            }
            ?>
        }
        function average(arr) {
        var sum = 0;
        for (var i = 0; i < arr.length; i++) {
            sum += arr[i];
        }
        return sum / arr.length;
}
    </script>
</body>
</html>