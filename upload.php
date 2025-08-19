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
                        $imageApproved = 0;
                        $folder = "./Noteimages/" . $filename;
                        $tempfolder = "./" . $filename;
                    
            
                        echo "tempfolder: $tempfolder \n, folder $folder \n,tempname $tempname \n, filename $filename";   
                        if (!move_uploaded_file($tempname, $tempfolder)) {echo "Image Error: Please contact us with a copy of the image uploaded";} 
                        $nsfw = new NSFW();
                        
                        $results = $nsfw->uploadFile($filename,true);
                        if(!is_array($results)) {exit("Image classification failied");}
                        
            
                        if ($results['classification'] != "neutral") {
                            unlink($filename);
                            exit("Image contains nsfw imagery");
                        }
        
                        $conn = mysqli_connect($serverName, $username, $password, $database, 3306);
                        if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);}

                        $sql = "INSERT INTO mainNotes (firstLast, location, dateFound, store, container, theme, filename, imageApproved) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssssssss", $firstLast, $location, $dateFound, $store, $container, $theme, $folder, $imageApproved);
                        if (!$stmt->execute()) {echo "Upload Error Please contact me with a copy of the answers provided";}

                        if (!rename($tempfolder, $folder)) {echo "Image Error: Please contact us with a copy of the image uploaded";} 
                        $conn->close();
                        echo "Note Created";
                        
                    }
                        

                        

                        class NSFW
                                    {
                                        private $key;

                                        function __construct($key = false)
                                        {
                                            $this->key = $key;
                                        }

                                        /**
                                        * Uploads a file and returns the results as array
                                        *
                                        * @param string $path Path to file to be uploaded
                                        * 
                                        * @return array if the results are in
                                        * @return false if there was an error
                                        */ 
                                        function uploadFile($path)
                                        {
                                            $request = curl_init('https://nsfw-categorize.it/api/upload');
                                            curl_setopt($request, CURLOPT_POST, true);
                                            curl_setopt(
                                                $request,
                                                CURLOPT_POSTFIELDS,
                                                array(
                                                    'image' => curl_file_create($path)
                                                )
                                            );

                                            if($this->key!==false)
                                                curl_setopt($request, CURLOPT_HTTPHEADER, array('NSFWKEY: '.$this->key));

                                            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                                            $answer = json_decode(curl_exec($request), true);

                                            curl_close($request);

                                            if ($answer['status'] == 'OK')
                                                return $answer['data'];
                                            else return false;
                                        }

                                        /**
                                        * Tells the API to download a remote file
                                        * and returns the results as array
                                        *
                                        * @param string $url The URL to the image to be analyzed
                                        * 
                                        * @return array if the results are in
                                        * @return false if there was an error
                                        */ 
                                        function uploadURL($url)
                                        {
                                            $request = curl_init('https://nsfw-categorize.it/api/upload?url='.$url);

                                            if($this->key!==false)
                                                curl_setopt($request, CURLOPT_HTTPHEADER, array('NSFWKEY: '.$this->key));

                                            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                                            $answer = json_decode(curl_exec($request), true);
                                            curl_close($request);

                                            if ($answer['status'] == 'OK')
                                                return $answer['data'];
                                            else return false;
                                        }

                                        /**
                                        * Checks if an image was already analyzed by cheking it's SHA1 hash
                                        * This can be used without prior uploading of a file, just to check
                                        * if a file you have on hand has been analyzed previously
                                        *
                                        * @param string $sha1 The SHA1 hash of a file
                                        * 
                                        * @return array if there are results
                                        * @return false if there was an error
                                        */ 
                                        function checkSHA1($sha1)
                                        {
                                            $request = curl_init('https://nsfw-categorize.it/api/hash/'.$sha1);
                                            if($this->key!==false)
                                                curl_setopt($request, CURLOPT_HTTPHEADER, array('NSFWKEY: '.$this->key));
                                            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                                            $answer = json_decode(curl_exec($request), true);
                                            curl_close($request);

                                            if ($answer['status'] == 'OK')
                                                return $answer['data'];
                                            else if($answer['status'] == 'PENDING')
                                                return $answer['data']['hash'];
                                            else return false;
                                        }
                                    }
                    ?>
                </h2>
            </div>
        </div>
    </body>



</html>

