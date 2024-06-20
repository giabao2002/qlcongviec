<?php
function getFileInfo($filename, $directory) {
    $file_info = [];
    $filename = explode(",", $filename);
    $filename = array_filter($filename);
    foreach ($filename as $file) {
        $file_path = $directory . $file;
        if (file_exists($file_path)) {
            $file_data = base64_encode(file_get_contents($file_path));
            $file_info[] = [
                "name" => $file,
                "size" => filesize($file_path),
                "type" => mime_content_type($file_path),
                "data" => $file_data
            ];
        }
    }
    return json_encode($file_info);
}
?>