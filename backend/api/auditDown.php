<?php
$auditFile = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=MYYAv6w0wxNnhSCyjpekYychuReP9LHN2rDGGOoVJp26lI4xDesApz4b27q6jYIkLJJe1VgKEEWU8Fto6u8HqAF1er8X86iRje1pT1t_r_8PTRgAEAJNL&media_id=UUMMPevWD_yl4YYEEPT8KIIEKhHbLFqI3JI75RRRh9LroMV-zMiKbEdUeBfEdHDY';
curlDownContent($auditFile);


/**
 * 模拟模拟浏览器抓取内容
 * @param  string $url 需要抓取的URL
 * @param  string $filename 需要保存的文件（包括目录和文件后缀）
 * @return bool
 */
function curlDownContent($url, $filename) {
    $handle = fopen($filename, 'w+');  //说明：/downAmr为下载的音频文件存放目录；/amr为转换成功存放原始文件目录；/mp3为转换后供调用目录
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36");
    //返回 response_header, 该选项非常重要,如果不为 true, 只会获得响应的正文
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    fwrite($handle, curl_exec($ch));
    $responseHeader = curl_getinfo($ch);
    curl_close($ch);
    fclose($handle);

    if ($responseHeader['content_type'] =='audio/amr' ) {
        return true;
    } else {
        return false;
    }
}