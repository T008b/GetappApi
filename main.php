<?php

// Verifica se a rota é "/"
if ($_SERVER['REQUEST_URI'] === '/') {
    echo 'Hello, World!';
    exit;
}

// Se não for "/", verifica o bundle ID e retorna o nome da faixa
if (isset($_GET['bundle'])) {
    $bundleId = urlencode($_GET['bundle']);
    $url = "https://itunes.apple.com/lookup?bundleId={$bundleId}";

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
    ]);
    $response = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($statusCode !== 200) {
        echo 'Failed to fetch data';
    } else {
        $data = json_decode($response, true);
        if ($data['resultCount'] == 0) {
            echo 'No results found';
        } else {
            $trackName = $data['results'][0]['trackCensoredName'];
            echo "Track name: $trackName";
        }
    }
} else {
    // Se nenhum bundle ID for fornecido, retorna um erro 400
    http_response_code(400);
    echo 'Missing bundle ID';
}
