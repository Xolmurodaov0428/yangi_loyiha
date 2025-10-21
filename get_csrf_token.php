<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

preg_match('/name=\"_token\" value=\"([^\"]+)\"/', $result, $matches);
if (isset($matches[1])) {
    echo 'CSRF Token: ' . $matches[1];
} else {
    echo 'CSRF Token topilmadi';
}
