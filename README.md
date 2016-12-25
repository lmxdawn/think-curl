

## 安装
> composer require lmxdawn/think-curl

## 使用简单示例

	$curl = lmxdawn\curl\Curl::getInstance();

	$html_data = $curl->send_http('https://segmentfault.com/','get');

