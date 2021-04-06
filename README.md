# Getting Started

## Demo Ⅰ
```php
$validator = new Validator();

$data = [
    'username' => 'kkzhuang',
    'password' => 'kkzhuang._-+',
];

$rules = [
    'username' => [
        'regex' => '/^[0-9A-Za-z\.\-_+]{6,64}$/',
    ],
    'password' => [
        'regex' => '/^[0-9A-Za-z\.\-_+]{6,64}$/',
    ],
];
if ($validator->validate($data, $rules)) {
    var_dump($validator->getProcessedData());
    echo 'pass';
} else {
    var_dump($validator->errors());
}
```

## Demo Ⅱ
```php
$validator = new Validator();

$data = [
    'username' => 'kkzhuang$',
    'password' => 'kkzhuang._-+',
];

$rules = [
    'username' => [
        'regex' => '/^[0-9A-Za-z\.\-_+]{6,64}$/',
    ],
];

$messages = [
    'username' => [
        'regex' => ':attribute 格式错误',
    ]
];

$alias = [
    'username' => '用户名',
];

if ($validator->validate($data, $rules, $messages, $alias)) {
    var_dump($validator->getProcessedData());
    echo 'pass';
} else {
    var_dump($validator->errors());
}
```
