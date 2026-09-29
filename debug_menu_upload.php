<?php
$cookie = tempnam(sys_get_temp_dir(), 'ttcookie');

$ch = curl_init('http://127.0.0.1:8000/admin/login');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie,
    CURLOPT_COOKIEFILE => $cookie,
    CURLOPT_FOLLOWLOCATION => false,
]);
$html = curl_exec($ch);
if ($html === false) {
    echo "LOGIN_PAGE_ERROR: " . curl_error($ch) . PHP_EOL;
    exit(1);
}
if (!preg_match('/name="_token"[^>]*value="([^"]+)"/s', $html, $m)) {
    echo "NO_CSRF_LOGIN\n";
    echo substr($html, 0, 500); 
    exit(1);
}
$token = $m[1];

$ch2 = curl_init('http://127.0.0.1:8000/admin/login');
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie,
    CURLOPT_COOKIEFILE => $cookie,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $token,
        'username' => 'admin',
        'password' => 'tummytime123',
    ]),
]);
$loginRes = curl_exec($ch2);
$loginCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
if ($loginCode >= 300 && $loginCode < 400) {
    echo "LOGIN_REDIRECT:$loginCode\n";
}

echo "LOGIN_STATUS:$loginCode\n";

echo substr($loginRes, 0, 250) . "\n";

$menuPage = curl_init('http://127.0.0.1:8000/admin/menu');
curl_setopt_array($menuPage, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie,
    CURLOPT_COOKIEFILE => $cookie,
    CURLOPT_FOLLOWLOCATION => false,
]);
$menuHtml = curl_exec($menuPage);
if ($menuHtml === false) {
    echo "MENU_PAGE_ERROR: " . curl_error($menuPage) . PHP_EOL;
    exit(1);
}
$menuToken = '';
if (preg_match('/name="_token"[^>]*value="([^"]+)"/s', $menuHtml, $mm)) {
    $menuToken = $mm[1];
} elseif (preg_match('/window\.CSRF_TOKEN = "([^"]+)"/s', $menuHtml, $mm)) {
    $menuToken = $mm[1];
}
if ($menuToken === '') {
    echo "NO_CSRF_MENU\n";
    echo substr($menuHtml, 0, 500);
    exit(1);
}

echo "MENU_TOKEN:" . substr($menuToken, 0, 20) . "\n";

$tmp = tempnam(sys_get_temp_dir(), 'menuimg');
$img = imagecreatetruecolor(30, 30);
$bg = imagecolorallocate($img, 255, 0, 0);
imagefill($img, 0, 0, $bg);
imagepng($img, $tmp);
imagedestroy($img);

$fields = [
    '_token' => $menuToken,
    'id' => '',
    'category_id' => '1',
    'name' => 'Upload Test Menu',
    'description' => 'desc',
    'price' => '12000',
    'is_available' => '1',
    'stock' => '10',
    'image' => new CURLFile($tmp, 'image/png', 'menu-test.png'),
];

$upload = curl_init('http://127.0.0.1:8000/admin/menu/save');
curl_setopt_array($upload, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie,
    CURLOPT_COOKIEFILE => $cookie,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'X-CSRF-TOKEN: ' . $menuToken,
    ],
]);
$uploadRes = curl_exec($upload);
$uploadCode = curl_getinfo($upload, CURLINFO_HTTP_CODE);

echo "UPLOAD_STATUS:$uploadCode\n";
echo $uploadRes . "\n";

@unlink($tmp);
@unlink($cookie);
