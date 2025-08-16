

<?php
//Credits to nsfw-catagorize for provding this service and code below

$nsfw = new NSFW();

// Example 1: upload images to check later
//
$results = $nsfw->uploadFile('image1.jpg',true);
if(is_array($results))
    echo "Classification done. Image is {$results['classification']}".PHP_EOL;


// Example 2: Check local image without uploading it
//
$results = $nsfw->checkSHA1(sha1_file('image2.jpg'));
if(is_array($results))
    echo "Image is {$results['classification']}".PHP_EOL;
else if($results===false)
    echo "This image has not been anaylzed yet".PHP_EOL;





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