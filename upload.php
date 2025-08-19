<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="upload.css">
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

        <div class="main">
            <head>
                <h1>Upload</h1>
            </head>
            
            <div class ="container_about">
                <form action="upload.php" method="post" enctype="multipart/form-data">

                    <label for="firstLast">First and Last word of notes typed as firstlast</label>
                    <input type="text" id="firstLast" name="firstLast" placeholder="First and Last word..."></label>

                    <label for="location">Location of Note</label>
                    <input type="text" id="location" name="location" placeholder="The location..."></label>
                    
                    <label for="dateFound">Date Found yyyy/mm/dd</label>
                    <input type="text" id="dateFound" name="dateFound" placeholder="The Date the note was found..."></label>

                    <label for="Store">Store or statepark the note was found in if neither enter N/A</label>
                    <input type="text" id="store" name="store" placeholder="The store/statepark..."></label>

                    <label for="container">The container the note was found in.</label>
                    <input type="text" id="container" name="container" placeholder="Container..."></label>

                    <label for="Theme">Theme of Note (The bolded words at the beginning of the note)</label>
                    <input type="text" id="theme" name="theme" placeholder="The theme..."></label>
                    
                    <label for="uploadfile">Picture of Note perferably cropped</label>
                    <input type="file" id='uploadfile' name="uploadfile" value=""/>

                    <input type="submit" name="submit" value="Submit" id ="submit">
                </form>
            </div>

            <div>
                <h2>
                    <?php
                    if (isset($_POST["submit"])) { 
                        require("variables.php");
                        $firstLast = $_POST['firstLast'];
                        $location = $_POST['location'];
                        $dateFound = $_POST['dateFound'];
                        $store = $_POST['store'];
                        $container = $_POST['container'];
                        $theme = $_POST['theme'];
                        $filename = $_FILES["uploadfile"]["name"];
                        $tempname = $_FILES["uploadfile"]["tmp_name"];

                        $folder = "./Noteimages/" . $filename;
                        
                        $nsfw = new NSFW();

                        $results = $nsfw->uploadFile($tempname,true);
                        if(is_array($results))
                            echo "Classification done. Image is {$results['classification']}".PHP_EOL;


                        $conn = mysqli_connect($serverName, $username, $password, $database, 3306);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "INSERT INTO mainNotes (firstLast, location, dateFound, store, container, theme, filename) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sssssss", $firstLast, $location, $dateFound, $store, $container, $theme, $folder);
                        if ($stmt->execute()) { 
                            echo "New Note Created Succesfully";
                        }
                        else {
                            echo "Upload Error Please contact me with a copy of the answers provided";
                        }

                        if (move_uploaded_file($tempname, $folder)) {
                            echo "Image uploaded successfully!";
                        } else {
                            echo "Image Error: Please contact us with a copy of the image uploaded";
                        }
                        $conn->close();
                        }
                    ?>
                </h2>
            </div>
        </div>
    </body>



</html>