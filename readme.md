# Laralog 

## 功能

- 提供 Laravel Http 中间件，捕捉 Http 请求生命周期内的必要信息
- Http 生命周期信息以 `JSON` 格式记录到日志文件中
- 提供了日志 `JSON` 化格式组件，可以无缝使用 `Filebeat` 采集日志到 `ES` 中，避免做格式优化
- 提供了过滤请求中敏感信息的配置，可以过滤到请求参数中的敏感信息，例如，密码等
## 安装 
1. 方式一  
    - 添加 `Leon19910505/laralog` 包声明到工程项目的 `composer.json` 文件中
    - 使用 `composer install` 命令安装
    
2. 方式二  
    - 使用如下命令直接安装依赖
        ```sh
        $ composer require Leon19910505/laralog
        ```
3. 配置文件发布
    - 在项目目录下面运行如下命令，发布 `config` 资源文件
        ```sh
        php artisan vendor:publish --provider="Leon19910505\Laralog\ServiceProvider"
        ```
## 配置
### 配置 `Laravel` `Http` 中间件

- 在 `app\Http\Kernel.php` 文件中，找到 `protected $middleware` 属性，添加如下声明。
    ```php
    $middleware = [
        ...
        \Leon19910505\Laralog\Http\Middleware\CaptureRequestLifecycle::class,
    ];
    ```

### 配置写日志组件
- 在 `.env` 配置中设置laralog专用的log channel `LOG_API` 

    ```dotenv
    LOG_API=api
    ```
    
- 在 `config/logging.php` 中，设置laralog日志频道 `channel` 为 `api` 的日志组件，添加如下配置声明 

    ```php
    'api' => [
         // Monolog 提供的 driver,保留不变
        'driver' => 'daily',
        // channel 名称，要与数组键名保持一致
        'name'   => 'daily',
        // 日志存储路径，及日志文件命名
        'path'   => env('DAILY_LARALOG_STORAGE_PATH', storage_path('logs/api.log')),
        // 指定使用的日志格式化组件类
        'tap'    => [\Leon19910505\Laralog\Formatter\LaralogFormatter::class],
        'level'  => 'info',
        // 日志文件保留天数
        'days'   => 7,
        // 在写日志文件前获得锁，避免并发写入造成的争抢，写错行
        'locking'=> true,
    ],
    ```
    
- 配置过滤敏感信息的键值，如需新增过滤的敏感信息，在 `config/laralog.php` 中 `except` 键对应的数组中，增加待过滤的请求参数键值

    ```php
    return [
        'except' => [
            'password',
            'password_information',
            'password_confirm',
            'something_to_except',
        ],
    ];
    ```
    
- 配置过滤敏感信息的键值，如需新增过滤的敏感信息，在 `config/laralog.php` 中 `except` 键对应的数组中，增加待过滤的请求参数键值

    ```php
    return [
    //except 是过滤request参数里不需要的字段
    'except'  => [
        'password',
        'password_information',
        'password_confirm',
    ],
    //exclude 是过滤返回结构里不要的字段
    'exclude' => [
        'os',
        'performance',
        'msg',
        'response',
        'extra',
        'headers',
        'hostname',
        'version',
        'platform',
        'end',
        'start',
        'tag',
    ],
    // extra 是添加额外的字段（业务定制），这里是写入了token的用户信息
    'extra'   => [
        'auth-student' => function () {
            return auth()->user()->id ?? '';
        },
        'auth-teacher' => function () {
            return auth('teacher')->user()->id ?? '';
        },
    ],

    ];

    ```
    
 - 配置日志存储路径，在 `.env` 文件中新增配置 `DAILY_LARALOG_STRORAGE_PATH=/path/to/laralog`


