<?php
$task = isset($_GET['task']) ? trim($_GET['task']) : "";
$term = isset($_GET['term']) ? trim($_GET['term']) : "Ender%27s+Game";
define("API_BASE","https://www.goodreads.com/");
define("API_KEY","YOU-API-KEY");
$response = array("status"=>false,"message"=>"","data"=>array());
switch($task) {
    default:
    echo json_encode(array("status"=>200));
    break;
    case "search_by_term":
        $term = str_replace(" ","+",$term);        
        $url = API_BASE."search/index.xml?key=".API_KEY."&q=".$term;
        // echo $url;
        try
        {
        $xml=simplexml_load_string(file_get_contents($url)) or die("Error: Cannot create object");
        // echo "<pre>".print_r($xml,true)."</pre>";
        $data=$xml->search->results->work;
        $index=0; $resp = array();
        foreach($data as $thisData) {
            $resp[$index]["average_rating"] =$thisData->average_rating;
            $resp[$index]["detail"] = (array)$thisData->best_book;
            $index++;
        }
        $response["status"]=true;
        $response["data"]=$resp;
    }catch(Exception $e) {
        $response["message"]="Internal server error.".$e->getMessage();
    }
    echo json_encode($response);exit;
    break;
    case "show_default_books":

    $authors = array("Charles C. Mann","Peeter Volkonski","Roger Fouts", "Stephen Tukel Mills","Jared Diamond","Robert M. Sapolsky","Thor Heyerdahl","Anne Fadiman","Daniel L. Everett","Marvin Harris","Bruce Chatwin","Yuval Noah Harari","Desmond Morris","Wade Davis","Colin M. Turnbull","C.W. Ceram","Napoleon A. Chagnon","Howard Carter","Eric R. Wolf","Tony Horwitz");
        $term=$authors[array_rand($authors)];
        $term = str_replace(" ", "%20", $term);
        $url = API_BASE."search/index.xml?key=".API_KEY."&q=".$term;
        // echo $url;
        try
        {
        $xml=simplexml_load_string(file_get_contents($url)) or die("Error: Cannot create object");
        // echo "<pre>".print_r($xml,true)."</pre>";
        $data=$xml->search->results->work;
        $index=0; $resp = array();
        foreach($data as $thisData) {
            $resp[$index]["average_rating"] =$thisData->average_rating;
            $resp[$index]["detail"] = (array)$thisData->best_book;
            $index++;
        }
        $response["status"]=true;
        $response["data"]=$resp;
    }catch(Exception $e) {
        $response["message"]="Internal server error.".$e->getMessage();
    }
    echo json_encode($response);exit;
    break;
    case "book_detail":
        $bookId = isset($_GET['bid']) ? trim($_GET['bid']) : 231804;
        $url="https://www.goodreads.com/book/show/".$bookId;
        $htmlString = file_get_contents($url);

        // echo "<pre>Response:".print_r($htmlString,true)."</pre>";
         
        
        $htmlDom = new DOMDocument;
         
        @$htmlDom->loadHTML($htmlString);

        // $description = $htmlDom->getElementsByTagName('div');
        // $description = $htmlDom->xpath('//div[@id="description"]/*');


        $xpath = new DOMXPath($htmlDom);
        
        // $cover = $xpath->query('//img[@id="coverImage"]');
        // echo "<pre>".print_r($cover,true)."</pre>";


        $tags = $xpath->query('//div[@id="description"]');
        $description = $tags[0]->textContent;
        
        //$reviews = $xpath->query('//div[@id="bookReviews"]');
        /*$reviews = $xpath->query('//div[@class="review"]');
         foreach ($reviews as $rv) {
            //echo "<pre>".print_r($rv->nodeValue,true)."</pre>";
             $user = $xpath->query('.//following::a[@class="left"]', $rv); 
             echo "<pre>".print_r($user,true)."</pre>";
            // echo $user[0]->nodeValue ."\n\n";
         }/*/


        //$elements = $xpath->query('//a[@class="user"], //div[@class="message"] //a, //div[@class="message"] //img');
                 // $elements = $xpath->query('//a[@class="user"]|//a[@class="reviewDate"]');
        $elements = $xpath->query("//span/a[contains(@class, 'user')]");
        $maxlimit=5;
        $data=array();
        //echo "<pre>Multile:".print_r($elements,true)."</pre>";
        $index = 0;
        foreach ($elements as $key => $ele) {
            // echo "<pre>User ".print_r($ele,true)."</pre>";
            if(!empty($ele->nodeValue)) { //echo $ele->nodeValue."<hr/>";
                $data[$index]["user"] =$ele->nodeValue;  
                $index++;
                
            }
            if($index >= $maxlimit) break; 
        }

        $elements = $xpath->query("//a[contains(@class, 'reviewDate')]");
        $index=0; 
        //echo "<pre>Multile:".print_r($elements,true)."</pre>";
        foreach ($elements as $key => $ele) { //echo $ele->nodeValue."<hr/>";
             // echo "<pre>Date ".print_r($ele,true)."</pre>";
            if(!empty($ele->nodeValue)) {
                $data[$index]["date"] =$ele->nodeValue;
                $index++;
            }
            if($index >= $maxlimit) break; 
        }

        $reviews = $xpath->query("//div/span[contains(@class, 'readable')]");
        $index=0; 
        //echo "<pre>Multile:".print_r($elements,true)."</pre>";
        foreach ($reviews as $key => $thisreview) {
            // echo "<pre>".print_r($ele,true)."</pre>";
            if(!empty($thisreview->nodeValue)) {
                $temp = str_replace("\n            \n", "", (substr($thisreview->nodeValue,0,150)));
                $temp = str_replace("â","",$temp);                
                $data[$index]["review"] =  $temp;
                $index++;        
            }
            if($index >= $maxlimit) break; 
        }//*/
         // echo "<pre>".print_r($data,true)."</pre>";
        $response["status"]=true;
        $response["data"] = $data;
        echo json_encode($response);
                 exit;








         /*$data = array();        
         $index=0;
         $reviewUser = $xpath->query('//a[@class="user"]');
         foreach ($reviewUser as $ruser) {
            // echo "<pre>".print_r($ruser,true)."</pre>";
            $data[$index]["user"]=$ruser->nodeValue;
            $index++;
         }
echo "<hr/>";


$index=0;
$reviewDate = $xpath->query('//a[@class="reviewDate"]'); echo "<pre>Review date:".print_r($reviewDate,true)."</pre>";
         foreach ($reviewDate as $rdate) {
            //  echo "<pre>".print_r($rtext,true)."</pre>";
            $data[$index]["review_date"]=$rdate->nodeValue;
            $index++;
         }
echo "<hr/>";
         $index=0;
         $reviewText = $xpath->query('//span[@class="readable"]');
         foreach ($reviewText as $rtext) {
            //  echo "<pre>".print_r($rtext,true)."</pre>";
            $data[$index]["review"]=$rtext->nodeValue;
            $index++;
         }
         
         $data["description"]=$description;//*/
         


         /*
        //Extract all img elements / tags from the HTML.
        $imageTags = $htmlDom->getElementsByTagName('img');
         
        //Create an array to add extracted images to.
        $extractedImages = array();
         
        //Loop through the image tags that DOMDocument found.
        foreach($imageTags as $imageTag){
         
            $imgId = $imageTag->getAttribute('id');
            if($imgId == "coverImage") {
            //Get the src attribute of the image.
            $imgSrc = $imageTag->getAttribute('src');
            $data["cover_image"]=$imgSrc;
            //Get the alt text of the image.
            $altText = $imageTag->getAttribute('alt');
         
            //Get the title text of the image, if it exists.
            $titleText = $imageTag->getAttribute('title');
         
            //Add the image details to our $extractedImages array.
            $extractedImages[] = array(
                'id'=>$imgId,
                'src' => $imgSrc,
                'alt' => $altText,
                'title' => $titleText
            );
            break;
        }
        }//*/
        // echo "<pre>Data:".print_r($data,true)."</pre>";
         
    break;
}
//https://www.goodreads.com/search/index.xml?key=TrvoViFewqhk0N1mG9R1w&q=Ender%27s+Game
// $url="https://www.goodreads.com/book/show/50.xml?key=TrvoViFewqhk0N1mG9R1w";
// echo "<pre>".print_r($response,true)."</pre>";
// echo json_encode($response);
?>
