<?php
    $url = 'https://test.icorp.uz/interview.php';
    $token = '91787c90-baa9-4c21-ba51-75ca9bf0c8b4';
    $webApi = "https://webhook.site/token/$token/requests?sorting=newest";
    $callback = "https://webhook.site/$token";

    $res = file_get_contents($url, false, stream_context_create([
        'http'=>[
            'method'=>'POST',
            'header'=>"Content-Type: application/json\r\n",
            'content'=>json_encode(['msg'=> 'accepted','url'=>$callback])
        ]
    ]));

        $first = json_decode($res,true);
        $part1 = $first['part1'] ?? '';
        print_r(['part1' => $part1]);

    $part2 = '';
    $start = microtime(true);

    while ((microtime(true) - $start) < 10) {
        $resp = @file_get_contents($webApi);
        if (!$resp) {
            usleep(300000);
            continue;
        }
        $items = json_decode($resp, true)['data'] ?? [];
        if (!empty($items[0])) {
            $firstItem = $items[0];
            $decoded = json_decode($firstItem['content'] ?? '', true);
            if (isset($decoded['part2'])) {
                $part2 = $decoded['part2'];
                break;
            }
    }
    usleep(300000);
    }
    print_r(['part2' => $part2]);
    if ($part1 && $part2) {
        $fullCode = $part1 . $part2;
        $response = @file_get_contents($url . '?code=' . urlencode($fullCode));
        $final = $response ? json_decode($response, true) : null;

        print_r($final);
        print_r(['code' => $fullCode, 'msg' => $final['msg']]);
    } else {
        echo "Error in data";
    }
?>