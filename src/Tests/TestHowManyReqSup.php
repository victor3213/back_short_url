<?php
$url = 'https://regex-generator.olafneumann.org/?sampleText=djfhksd-12312&flags=i&onlyPatterns=true&matchWholeLine=false&selection=';
$arr = [
    'typeOfUrl' => 'simple',
    'longUrl' => $url,
    'action' => 'getShortUrl'

];

for ($i=0; $i < 800; $i++) { 
    $data = http_build_query($arr);
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://167.235.192.111:90/api?'. $data,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
    usleep(500);

}
