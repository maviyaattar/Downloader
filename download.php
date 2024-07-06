<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = $_POST['url'];

    // Validate URL (for YouTube video)
    if (isValidYouTubeUrl($url)) {
        $videoID = getYouTubeVideoID($url);
        $downloadUrl = "https://www.youtube.com/watch?v={$videoID}";
        
        // Set headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $videoID . '.mp4"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($downloadUrl));

        // Read and output the file
        readfile($downloadUrl);
        exit;
    } else {
        http_response_code(400);
        echo "Invalid YouTube URL";
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}

function isValidYouTubeUrl($url) {
    return preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})$/', $url);
}

function getYouTubeVideoID($url) {
    $urlParams = parse_url($url);
    if (isset($urlParams['query'])) {
        parse_str($urlParams['query'], $params);
        if (isset($params['v'])) {
            return $params['v'];
        }
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $path = explode('/', $urlParams['path']);
        return end($path);
    }
    return null;
}
?>