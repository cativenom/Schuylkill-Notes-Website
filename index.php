
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="main.css">
    
</head>
<body>
    <div class = "sidenav">
        <a href="about.html">About</a>
        <a href="index.php">Notes</a>
        <a href="contact.html">Contact</a>
        <a href="Map.html">Map of Notes</a>
        <a href="upload.php">Upload</a>
    </div>



    
    <div class = "main">
        <head>
            <h1>Schuylkill Notes Database</h1 >
        </head>

        
        <body>
        <?php 
        require ('variables.php');


        $conn = mysqli_connect($serverName, $username, $password, $database);
        $sql = 'SELECT firstLast, location, dateFound, store, container, theme, filename FROM mainnotes';
        $result = $conn->query($sql);
        $conn->close();
        $data = array();
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
          

        $jsonData = json_encode($data);
        ?>
            <script>
                var cardContent = <?php echo $jsonData;?>
            </script>
            <script src="cardgen.js"></script>

        </body>
    </div>
</body>
</html>

