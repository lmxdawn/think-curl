
# curl 模拟请求类

## 安装
> composer require lmxdawn/think-curl

## 使用简单示例

```php
    $res = lmxdawn\curl\Curl::http('https://api.weibo.com/2/comments/show.json',['json' => '{"hh":123}'],'GET');
```


