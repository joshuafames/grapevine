<?php
class Image {

        public static function uploadImage($formname, $query, $params) {
                set_time_limit(0);
                $image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));

                $options = array('http'=>array(
                        'method'=>"POST",
                        'header'=>"Authorization: Bearer 22534c7089622d5104cec242e1de2b1e7ade5060\n".
                        "Content-Type: application/x-www-form-urlencoded",
                        'content'=>$image
                ));

                $context = stream_context_create($options);

                $imgurURL = "https://api.imgur.com/3/image";

                if ($_FILES[$formname]['size'] > 10240000) {
                        die('Image too big, must be 10MB or less!');
                }

                $response = file_get_contents($imgurURL, false, $context);
                $response = json_decode($response);

                $preparams = array($formname=>$response->data->link);

                $params = $preparams + $params;

                DB::query($query, $params);

        }

}

class Photo
{
    public static function uploadImage($formname, $query, $params)
    {
        $client_id = 'YOUR CLIENT ID';
        $image = base64_encode(file_get_contents($_FILES[$formname]['tmp_name']));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.imgur.com/3/image.json',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer 22534c7089622d5104cec242e1de2b1e7ade5060\n" . $client_id
            ) ,
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POSTFIELDS => array(
                'image' => $image
            )
        ));
        $out = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($out);
        $preparams = array(
            $formname => $response->data->link
        );
        $params = $preparams + $params;
        DB::query($query, $params);
    }
}
?>
