# 構成

- [はじめに](#introduction)
- [環境設定](#environment-configuration)
     - [環境変数の種類](#environment-variable-types)
     - [環境設定の取得](#環境設定の取得)
     - [現在の環境の決定](#現在の環境の決定)
     - [暗号化環境ファイル](#encrypting-environment-files)
- [構成値へのアクセス](#accessing-configuration-values)
- [構成キャッシング](#configuration-caching)
- [デバッグモード](#debug-mode)
- [メンテナンスモード](#メンテナンスモード)

<a name="introduction"></a>
## はじめに

Laravel フレームワークの設定ファイルはすべて `config` ディレクトリに保存されています。 各オプションは文書化されているので、ファイルに目を通して、使用可能なオプションをよく理解してください。

これらの構成ファイルを使用すると、データベース接続情報、メールサーバー情報、およびアプリケーションのタイムゾーンや暗号化キーなどのその他のさまざまなコア構成値などを構成できます。

<a name="application-overview"></a>
#### アプリケーションの概要

お急ぎですか？ 「about」アーティザン コマンドを使用して、アプリケーションの構成、ドライバー、および環境の概要をすばやく取得できます。

シェル
php職人について
```

アプリケーションの概要出力の特定のセクションのみに関心がある場合は、`--only` オプションを使用してそのセクションをフィルタリングできます。

シェル
php artisan about --only=environment
```

<a name="environment-configuration"></a>
## 環境設定

アプリケーションが実行されている環境に基づいて、異なる構成値を使用すると役立つことがよくあります。 たとえば、運用サーバーとは異なるキャッシュ ドライバーをローカルで使用したい場合があります。

これを簡単にするために、Laravel は [DotEnv](https://github.com/vlucas/phpdotenv) PHP ライブラリを利用します。 新規の Laravel インストールでは、アプリケーションのルート ディレクトリには、多くの一般的な環境変数を定義する `.env.example` ファイルが含まれます。 Laravel のインストール プロセス中に、このファイルは自動的に `.env` にコピーされます。

Laravel のデフォルトの `.env` ファイルには、アプリケーションがローカルで実行されているか、実動 Web サーバーで実行されているかによって異なる可能性がある、いくつかの一般的な構成値が含まれています。 これらの値は、Laravel の「env」関数を使用して、「config」ディレクトリ内のさまざまな Laravel 構成ファイルから取得されます。

チームで開発している場合は、引き続き `.env.example` ファイルをアプリケーションに含めることをお勧めします。 サンプル構成ファイルにプレースホルダー値を入れることで、チームの他の開発者は、アプリケーションの実行に必要な環境変数を明確に確認できます。

> **注**
> `.env` ファイル内の任意の変数は、サーバー レベルまたはシステム レベルの環境変数などの外部環境変数によって上書きできます。

<a name="environment-file-security"></a>
#### 環境ファイルのセキュリティ

アプリケーションを使用する各開発者/サーバーは異なる環境構成を必要とする可能性があるため、`.env` ファイルはアプリケーションのソース管理にコミットしないでください。 さらに、侵入者がソース管理リポジトリへのアクセスを取得した場合、機密性の高い資格情報が公開されるため、これはセキュリティ リスクになります。

ただし、Laravel の組み込みの [環境暗号化](#encrypting-environment-files) を使用して環境ファイルを暗号化することは可能です。 暗号化された環境ファイルは、ソース管理に安全に配置できます。

<a name="追加環境ファイル"></a>
#### 追加の環境ファイル

アプリケーションの環境変数をロードする前に、Laravel は `APP_ENV` 環境変数が外部から提供されているかどうか、または `--env` CLI 引数が指定されているかどうかを判断します。 その場合、Laravel は `.env.[APP_ENV]` ファイルが存在する場合はそれをロードしようとします。 存在しない場合は、デフォルトの `.env` ファイルがロードされます。

<a name="environment-variable-types"></a>
### 環境変数の種類

`.env` ファイル内のすべての変数は、通常、文字列として解析されるため、`env()` 関数からより広い範囲の型を返すことができるように、いくつかの予約値が作成されています。

| `.env` 値 | `env()` 値 |
|--------------|---------------|
| true         | (bool) true   |
| (true)       | (bool) true   |
| false        | (bool) false  |
| (false)      | (bool) false  |
| empty        | (string) ''   |
| (empty)      | (string) ''   |
| null         | (null) null   |
| (null)       | (null) null   |

スペースを含む値で環境変数を定義する必要がある場合は、値を二重引用符で囲んでください。

```ini
APP_NAME="My Application"
```

<a name="retrieving-environment-configuration"></a>
### Retrieving Environment Configuration

All of the variables listed in the `.env` file will be loaded into the `$_ENV` PHP super-global when your application receives a request. However, you may use the `env` function to retrieve values from these variables in your configuration files. In fact, if you review the Laravel configuration files, you will notice many of the options are already using this function:

    'debug' => env('APP_DEBUG', false),

The second value passed to the `env` function is the "default value". This value will be returned if no environment variable exists for the given key.

<a name="determining-the-current-environment"></a>
### Determining The Current Environment

The current application environment is determined via the `APP_ENV` variable from your `.env` file. You may access this value via the `environment` method on the `App` [facade](/docs/{{version}}/facades):

    use Illuminate\Support\Facades\App;

    $environment = App::environment();

You may also pass arguments to the `environment` method to determine if the environment matches a given value. The method will return `true` if the environment matches any of the given values:

    if (App::environment('local')) {
        // The environment is local
    }

    if (App::environment(['local', 'staging'])) {
        // The environment is either local OR staging...
    }

> **Note**  
> The current application environment detection can be overridden by defining a server-level `APP_ENV` environment variable.

<a name="encrypting-environment-files"></a>
### Encrypting Environment Files

Unencrypted environment files should never be stored in source control. However, Laravel allows you to encrypt your environment files so that they may safely be added to source control with the rest of your application.

<a name="encryption"></a>
#### Encryption

To encrypt an environment file, you may use the `env:encrypt` command:

```shell
php artisan env:encrypt
```

Running the `env:encrypt` command will encrypt your `.env` file and place the encrypted contents in an `.env.encrypted` file. The decryption key is presented in the output of the command and should be stored in a secure password manager. If you would like to provide your own encryption key you may use the `--key` option when invoking the command:

```shell
php artisan env:encrypt --key=3UVsEgGVK36XN82KKeyLFMhvosbZN1aF
```

> **Note**  
> The length of the key provided should match the key length required by the encryption cipher being used. By default, Laravel will use the `AES-256-CBC` cipher which requires a 32 character key. You are free to use any cipher supported by Laravel's [encrypter](/docs/{{version}}/encryption) by passing the `--cipher` option when invoking the command.

If your application has multiple environment files, such as `.env` and `.env.staging`, you may specify the environment file that should be encrypted by providing the environment name via the `--env` option:

```shell
php artisan env:encrypt --env=staging
```

<a name="decryption"></a>
#### Decryption

To decrypt an environment file, you may use the `env:decrypt` command. This command requires a decryption key, which Laravel will retrieve from the `LARAVEL_ENV_ENCRYPTION_KEY` environment variable:

```shell
php artisan env:decrypt
```

Or, the key may be provided directly to the command via the `--key` option:

```shell
php artisan env:decrypt --key=3UVsEgGVK36XN82KKeyLFMhvosbZN1aF
```

When the `env:decrypt` command is invoked, Laravel will decrypt the contents of the `.env.encrypted` file and place the decrypted contents in the `.env` file.

The `--cipher` option may be provided to the `env:decrypt` command in order to use a custom encryption cipher:

```shell
php artisan env:decrypt --key=qUWuNRdfuImXcKxZ --cipher=AES-128-CBC
```

If your application has multiple environment files, such as `.env` and `.env.staging`, you may specify the environment file that should be decrypted by providing the environment name via the `--env` option:

```shell
php artisan env:decrypt --env=staging
```

In order to overwrite an existing environment file, you may provide the `--force` option to the `env:decrypt` command:

```shell
php artisan env:decrypt --force
```

<a name="accessing-configuration-values"></a>
## Accessing Configuration Values

You may easily access your configuration values using the global `config` function from anywhere in your application. The configuration values may be accessed using "dot" syntax, which includes the name of the file and option you wish to access. A default value may also be specified and will be returned if the configuration option does not exist:

    $value = config('app.timezone');

    // Retrieve a default value if the configuration value does not exist...
    $value = config('app.timezone', 'Asia/Seoul');

To set configuration values at runtime, pass an array to the `config` function:

    config(['app.timezone' => 'America/Chicago']);

<a name="configuration-caching"></a>
## Configuration Caching

To give your application a speed boost, you should cache all of your configuration files into a single file using the `config:cache` Artisan command. This will combine all of the configuration options for your application into a single file which can be quickly loaded by the framework.

You should typically run the `php artisan config:cache` command as part of your production deployment process. The command should not be run during local development as configuration options will frequently need to be changed during the course of your application's development.

Once the configuration has been cached, your application's `.env` file will not be loaded by the framework during requests or Artisan commands; therefore, the `env` function will only return external, system level environment variables.

For this reason, you should ensure you are only calling the `env` function from within your application's configuration (`config`) files. You can see many examples of this by examining Laravel's default configuration files. Configuration values may be accessed from anywhere in your application using the `config` function [described above](#accessing-configuration-values).

> **Warning**  
> If you execute the `config:cache` command during your deployment process, you should be sure that you are only calling the `env` function from within your configuration files. Once the configuration has been cached, the `.env` file will not be loaded; therefore, the `env` function will only return external, system level environment variables.

<a name="debug-mode"></a>
## Debug Mode

The `debug` option in your `config/app.php` configuration file determines how much information about an error is actually displayed to the user. By default, this option is set to respect the value of the `APP_DEBUG` environment variable, which is stored in your `.env` file.

For local development, you should set the `APP_DEBUG` environment variable to `true`. **In your production environment, this value should always be `false`. If the variable is set to `true` in production, you risk exposing sensitive configuration values to your application's end users.**

<a name="maintenance-mode"></a>
## Maintenance Mode

When your application is in maintenance mode, a custom view will be displayed for all requests into your application. This makes it easy to "disable" your application while it is updating or when you are performing maintenance. A maintenance mode check is included in the default middleware stack for your application. If the application is in maintenance mode, a `Symfony\Component\HttpKernel\Exception\HttpException` instance will be thrown with a status code of 503.

To enable maintenance mode, execute the `down` Artisan command:

```shell
php artisan down
```

If you would like the `Refresh` HTTP header to be sent with all maintenance mode responses, you may provide the `refresh` option when invoking the `down` command. The `Refresh` header will instruct the browser to automatically refresh the page after the specified number of seconds:

```shell
php artisan down --refresh=15
```

You may also provide a `retry` option to the `down` command, which will be set as the `Retry-After` HTTP header's value, although browsers generally ignore this header:

```shell
php artisan down --retry=60
```

<a name="bypassing-maintenance-mode"></a>
#### Bypassing Maintenance Mode

To allow maintenance mode to be bypassed using a secret token, you may use the `secret` option to specify a maintenance mode bypass token:

```shell
php artisan down --secret="1630542a-246b-4b66-afa1-dd72a4c43515"
```

After placing the application in maintenance mode, you may navigate to the application URL matching this token and Laravel will issue a maintenance mode bypass cookie to your browser:

```shell
https://example.com/1630542a-246b-4b66-afa1-dd72a4c43515
```

When accessing this hidden route, you will then be redirected to the `/` route of the application. Once the cookie has been issued to your browser, you will be able to browse the application normally as if it was not in maintenance mode.

> **Note**  
> Your maintenance mode secret should typically consist of alpha-numeric characters and, optionally, dashes. You should avoid using characters that have special meaning in URLs such as `?`.

<a name="pre-rendering-the-maintenance-mode-view"></a>
#### Pre-Rendering The Maintenance Mode View

If you utilize the `php artisan down` command during deployment, your users may still occasionally encounter errors if they access the application while your Composer dependencies or other infrastructure components are updating. This occurs because a significant part of the Laravel framework must boot in order to determine your application is in maintenance mode and render the maintenance mode view using the templating engine.

For this reason, Laravel allows you to pre-render a maintenance mode view that will be returned at the very beginning of the request cycle. This view is rendered before any of your application's dependencies have loaded. You may pre-render a template of your choice using the `down` command's `render` option:

```shell
php artisan down --render="errors::503"
```

<a name="redirecting-maintenance-mode-requests"></a>
#### Redirecting Maintenance Mode Requests

While in maintenance mode, Laravel will display the maintenance mode view for all application URLs the user attempts to access. If you wish, you may instruct Laravel to redirect all requests to a specific URL. This may be accomplished using the `redirect` option. For example, you may wish to redirect all requests to the `/` URI:

```shell
php artisan down --redirect=/
```

<a name="disabling-maintenance-mode"></a>
#### Disabling Maintenance Mode

To disable maintenance mode, use the `up` command:

```shell
php artisan up
```

> **Note**  
> You may customize the default maintenance mode template by defining your own template at `resources/views/errors/503.blade.php`.

<a name="maintenance-mode-queues"></a>
#### Maintenance Mode & Queues

While your application is in maintenance mode, no [queued jobs](/docs/{{version}}/queues) will be handled. The jobs will continue to be handled as normal once the application is out of maintenance mode.

<a name="alternatives-to-maintenance-mode"></a>
#### Alternatives To Maintenance Mode

Since maintenance mode requires your application to have several seconds of downtime, consider alternatives like [Laravel Vapor](https://vapor.laravel.com) and [Envoyer](https://envoyer.io) to accomplish zero-downtime deployment with Laravel.
