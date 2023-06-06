# HTTP リクエスト

- [はじめに](#introduction)
- [リクエストの操作](#interacting-with-the-request)
    - [リクエストへのアクセス](#accessing-the-request)
    - [リクエストのパス、ホスト、メソッド](#request-path-and-method)
    - [リクエストヘッダ](#request-headers)
    - [リクエスト IP アドレス](#request-ip-address)
    - [コンテンツネゴシエーション](#content-negotiation)
    - [PSR-7 リクエスト](#psr7-requests)
- [入力](#input)
    - [入力の取得](#retrieving-input)
    - [入力の存在判定](#determining-if-input-is-present)
    - [追加入力のマージ](#merging-additional-input)
    - [直前の入力](#old-input)
    - [クッキー](#cookies)
    - [入力のトリミングと正規化](#input-trimming-and-normalization)
- [ファイル](#files)
    - [アップロードされたファイルの取得](#retrieving-uploaded-files)
    - [アップロードされたファイルの保存](#storing-uploaded-files)
- [信頼するプロキシの設定](#cconfigured-trusted-proxies)
- [信頼するホストの設定](#cconfigured-trusted-hosts)

<a name="introduction"></a>
## はじめに

Laravel の `Illuminate\Http\Request` クラスは、アプリケーションで処理されている現在の HTTP リクエストを操作し、リクエストとともに送信された入力、クッキー、およびファイルを取得するオブジェクト指向の方法を提供します。

<a name="interacting-with-the-request"></a>
## リクエストの操作

<a name="accessing-the-request"></a>
### リクエストへのアクセス

現在の HTTP リクエストのインスタンスを取得するには、ルートクロージャ、またはコントローラメソッドで `Illuminate\Http\Request` クラスをタイプヒントする必要があります。受信したリクエストのインスタンスは、Laravel [サービスコンテナ](/docs/{{version}}/container) によって自動的に依存性注入されます。

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Store a new user.
         */
        public function store(Request $request): RedirectResponse
        {
            $name = $request->input('name');

            // Store the user...

            return redirect('/users');
        }
    }

前述したように、ルートクロージャで `Illuminate\Http\Request` クラスをタイプヒントで指定することもできます。サービス コンテナは、実行時に受信リクエストを自動的にクロージャに依存性注入します。

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

<a name="dependency-injection-route-parameters"></a>
#### 依存性注入とルートパラメータ

コントローラメソッドがルート パラメータからの入力も期待している場合は、他の依存関係の後にルートパラメータをリストする必要があります。たとえば、ルートが次のように定義されているとします。

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

以下のようにコントローラメソッドを定義することで、`Illuminate\Http\Request` をタイプヒントし、`id` ルートパラメータにアクセスできます。

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Update the specified user.
         */
        public function update(Request $request, string $id): RedirectResponse
        {
            // Update the user...

            return redirect('/users');
        }
    }

<a name="request-path-and-method"></a>
### リクエストのパス、ホスト、およびメソッド

`Illuminate\Http\Request` インスタンスは、`Symfony\Component\HttpFoundation\Request` クラスを拡張し、受信 HTTP リクエストを調査するためのさまざまなメソッドを提供します。以下では、最も重要な調査方法のいくつかを説明します。

<a name="retrieving-the-request-path"></a>
#### リクエストパスの取得

`path` メソッドはリクエストのパス情報を返します。したがって、受信リクエストが `http://example.com/foo/bar` をターゲットにしている場合、`path` メソッドは `foo/bar` を返します。

    $uri = $request->path();

<a name="inspecting-the-request-path"></a>
#### リクエストのパス / ルートの検査

`is` メソッドを使用すると、受信リクエストのパスが指定されたパターンと一致するかどうかを検証できます。このメソッドは、`*` 文字をワイルドカードとして使用できます。

    if ($request->is('admin/*')) {
        // ...
    }

`routeIs` メソッドでは、受信リクエストが [名前付きルート](/docs/{{version}}/routing#named-routes) と一致したかどうかを判定できます。

    if ($request->routeIs('admin.*')) {
        // ...
    }

<a name="retrieving-the-request-url"></a>
#### リクエスト URL の取得

受信リクエストの完全な URL を取得するには、`url` メソッドか `fullUrl` メソッドを使用します。`url` メソッドはクエリ文字列なしで URL を返しますが、`fullUrl` メソッドでは、クエリ文字列が含まれます。

    $url = $request->url();

    $urlWithQueryString = $request->fullUrl();

現在の URL にクエリ文字列データを追加したい場合は、`fullUrlWithQuery` メソッドを呼び出します。このメソッドは、指定されたクエリ文字列変数の配列を現在のクエリ文字列とマージします。

    $request->fullUrlWithQuery(['type' => 'phone']);

<a name="retrieving-the-request-host"></a>
#### リクエストホストの取得

`host`、`httpHost`、`schemeAndHttpHost` メソッドを使用して、受信リクエストの「ホスト」を取得できます。

    $request->host();
    $request->httpHost();
    $request->schemeAndHttpHost();

<a name="retrieving-the-request-method"></a>
#### リクエストメソッドの取得

`method` メソッドはリクエストの HTTP 動詞を返します。`isMethod` メソッドを使用すると、HTTP 動詞が指定された文字列と一致するかを判定できます。

    $method = $request->method();

    if ($request->isMethod('post')) {
        // ...
    }

<a name="request-headers"></a>
### リクエストヘッダ

`header` メソッドを使用して、`Illuminate\Http\Request` インスタンスからリクエストヘッダを取得できます。リクエストにヘッダが存在しない場合は、`null` が返されます。また `header` メソッドではオプションで、リクエストにヘッダが存在しない場合に返される値を  2 番目の引数で指定できます。

    $value = $request->header('X-Header-Name');

    $value = $request->header('X-Header-Name', 'default');

`hasHeader` メソッドは、リクエストに特定のヘッダが含まれているかどうかを判定するために使用できます。

    if ($request->hasHeader('X-Header-Name')) {
        // ...
    }

便宜上、`bearerToken` メソッドを使用して、`Authorization` ヘッダからベアラートークンを取得できます。そのようなヘッダーが存在しない場合は、空の文字列が返されます。

    $token = $request->bearerToken();

<a name="request-ip-address"></a>
### リクエスト IP アドレス

`ip` メソッドは、アプリケーションにリクエストしたクライアントの IP アドレスを取得できます。

    $ipAddress = $request->ip();

<a name="content-negotiation"></a>
### コンテンツネゴシエーション

Laravel は、`Accept` ヘッダを介して受信リクエストのリクエストされたコンテンツタイプを検査するためのメソッドをいくつか提供しています。まず、`getAcceptableContentTypes` メソッドは、リクエストによって受け入れられたすべてのコンテンツタイプを含む配列を返します。

    $contentTypes = $request->getAcceptableContentTypes();

`accepts` メソッドは、コンテンツタイプの配列を受け入れ、いずれかのコンテンツタイプがリクエストによって受け入れられた場合は `true` を返します。それ以外の場合は、`false` が返されます。

    if ($request->accepts(['text/html', 'application/json'])) {
        // ...
    }

`prefers` メソッドを使用すると、指定したコンテンツタイプの配列の中から、どのコンテンツタイプがリクエストで最も優先されるかを判断できます。提供されたコンテンツタイプがリクエスト内にない場合は、`null` が返されます。

    $preferred = $request->prefers(['text/html', 'application/json']);

多くのアプリケーションは HTML または JSON のみを提供するため、`expectsJson` メソッドを使用して、受信リクエストが JSON リクエストを期待しているかどうかを素早く判定できます。

    if ($request->expectsJson()) {
        // ...
    }

<a name="psr7-requests"></a>
### PSR-7 リクエスト

[PSR-7 標準](https://www.php-fig.org/psr/psr-7/) は、リクエストとレスポンスを含む HTTP メッセージのインターフェイスを指定します。Laravel リクエストではなく PSR-7 リクエストのインスタンスを取得したい場合は、まずいくつかのライブラリをインストールする必要があります。Laravel は *Symfony HTTP Message Bridge* コンポーネントを使用して、一般的な Laravel リクエストとレスポンスを PSR-7 互換の実装に変換します。

```shell
composer require symfony/psr-http-message-bridge
composer require nyholm/psr7
```

これらのライブラリをインストールしたら、ルートクロージャかコントローラメソッドでリクエストインターフェイスをタイプヒントすることで PSR-7 リクエストを取得できます。

    use Psr\Http\Message\ServerRequestInterface;

    Route::get('/', function (ServerRequestInterface $request) {
        // ...
    });

> **Note**  
> ルートまたはコントローラから PSR-7 レスポンスインスタンスを返すと、自動的に Laravel レスポンスインスタンスに変換され、フレームワークによって表示されます。

<a name="input"></a>
## 入力

<a name="retrieving-input"></a>
### 入力の取得

<a name="retrieving-input"></a>
#### 全入力データの取得

`all` メソッドを使用すると、受信リクエストのすべての入力データを `array` として取得できます。このメソッドは、受信リクエストが HTML フォームからのものであるか、XHR リクエストであるかに関係なく使用できます。

    $input = $request->all();

`collect` メソッドを使用すると、受信リクエストのすべての入力データを [コレクション](/docs/{{version}}/collections) として取得できます。

    $input = $request->collect();

加えて `collect` は受信リクエストの入力のサブセットをコレクションとして取得することもできます。

    $request->collect('users')->each(function (string $user) {
        // ...
    });

<a name="retrieving-an-input-value"></a>
#### 入力値の取得

いくつかの簡単な方法を使用すると、リクエストにどの HTTP 動詞が使用されたかを気にすることなく、`Illuminate\Http\Request` インスタンスからのすべてのユーザー入力にアクセスできます。HTTP 動詞に関係なく、`input` メソッドを使用してユーザー入力を取得できます。

    $name = $request->input('name');

`input` メソッドの 2 番目の引数にデフォルト値を渡すことができます。１番目に指定した入力値がリクエストに存在しない場合、この値が返されます。

    $name = $request->input('name', 'Sally');

配列入力を含むフォームを操作する場合は、「ドット」表記を使用して配列にアクセスします。

    $name = $request->input('products.0.name');

    $names = $request->input('products.*.name');

すべての入力値を連想配列として取得するには、引数なしで `input` メソッドを呼び出す方法があります。

    $input = $request->input();

<a name="retrieving-input-from-the-query-string"></a>
#### クエリ文字列から入力を取得

`input` メソッドはリクエストペイロード全体 (クエリ文字列を含む) から値を取得しますが、`query` メソッドはクエリ文字列からのみ値を取得します。

    $name = $request->query('name');

指定したクエリ文字列の値データが存在しない場合、このメソッドの 2 番目の引数が返されます。

    $name = $request->query('name', 'Helen');

すべてのクエリ文字列の値を連想配列として取得するには、引数なしで `query` メソッドを呼び出すことができます。

    $query = $request->query();

<a name="retrieving-json-input-values"></a>
#### JSON 入力値の取得

JSON リクエストをアプリケーションに送信する際は、リクエストの `Content-Type` ヘッダが `application/json` に適切に設定されている限り、`input` メソッド経由で JSON データにアクセスできます。「ドット」構文を使用して、JSON 配列 / オブジェクト内にネストされた値を取得することもできます。

    $name = $request->input('user.name');

<a name="retrieving-stringable-input-values"></a>
#### Stringable 入力値の取得

リクエストの入力データをプリミティブな `string` として取得する代わりに、`string` メソッドを使用してリクエスト データを [`Illuminate\Support\Stringable`](/docs/{{version}}/helpers#fluent-strings) のインスタンスとして取得することもできます。

    $name = $request->string('name')->trim();

<a name="retrieving-boolean-input-values"></a>
#### 論理入力値の取得

チェックボックスなどの HTML 要素を処理する場合、アプリケーションは文字列である「真」の値を受け取ることがあります。たとえば 「true」または「on」です。 便宜上、`boolean` メソッドを使用してこれらの値をブール値として取得できます。`boolean` メソッドは、1、"1"、true、"true"、"on"、"yes" の場合は `true` を返します。他のすべての値は `false` を返します。

    $archived = $request->boolean('archived');

<a name="retrieving-date-input-values"></a>
#### 日付入力値の取得

便宜上、日付や時刻を含む入力値は、`date` メソッドを使用して Carbon インスタンスとして取得できます。リクエストに指定された名前の入力値が含まれていない場合は、`null` が返されます。

    $birthday = $request->date('birthday');

`date` メソッドで受け取る２
番目と３番目の引数は、それぞれ日付の形式とタイムゾーンを指定するために使用できます。

    $elapsed = $request->date('elapsed', '!H:i', 'Europe/Madrid');

入力値が存在しても無効な形式の場合、`InvalidArgumentException` がスローされます。 したがって、`date` メソッドを呼び出す前に入力値を検証することをお勧めします。

<a name="retrieving-enum-input-values"></a>
#### 列挙型（Enum）入力値の取得

[PHP enums](https://www.php.net/manual/en/language.types.enumerations.php) に対応する入力値もリクエストから取得できます。リクエストに指定した名前の入力値が含まれていない場合、または列挙型に入力値と一致するバッキング値がない場合は、`null` が返されます。`enum` メソッドは、入力値の名前と enum クラスを１番目と２番目の引数として受け取ります。

    use App\Enums\Status;

    $status = $request->enum('status', Status::class);

<a name="retrieving-input-via-dynamic-properties"></a>
#### 動的プロパティを介した入力の取得

`Illuminate\Http\Request` インスタンスの動的プロパティを使用してユーザー入力にアクセスすることもできます。たとえば、アプリケーションのフォームの １つに `name` フィールドが含まれている場合、次のようにフィールドの値にアクセスできます。

    $name = $request->name;

動的プロパティを使用する場合、Laravel は最初にリクエストペイロード内のパラメータの値を探します。存在しない場合、Laravel は一致したルートのパラメータ内のフィールドを検索します。

<a name="retrieving-a-portion-of-the-input-data"></a>
#### 入力データの一部の取得

入力データのサブセットを取得する必要がある場合は、`only` メソッドと `except` メソッドを使用できます。これらのメソッドは両方とも、単一の「配列」または引数の動的なリストを受け取ります。

    $input = $request->only(['username', 'password']);

    $input = $request->only('username', 'password');

    $input = $request->except(['credit_card']);

    $input = $request->except('credit_card');

> **Warning**  
> `only` メソッドは、指定したすべてのキーと値のペアを返します。ただし、リクエストに存在しないキーと値のペアは返されません。

<a name="determining-if-input-is-present"></a>
### 入力値の存在を判定

`has` メソッドを使用して、リクエストに値が存在するかどうかを確認できます。値がリクエストに存在する場合、`has` メソッドは `true` を返します。

    if ($request->has('name')) {
        // ...
    }

`has` メソッドに配列を与え、その配列に指定された値がリクエストにすべて存在するかどうかを判定します。

    if ($request->has(['name', 'email'])) {
        // ...
    }

リクエストに指定した値が存在する場合、`whenHas` メソッドは指定されたクロージャを実行します。

    $request->whenHas('name', function (string $input) {
        // ...
    });

A second closure may be passed to the `whenHas` method that will be executed if the specified value is not present on the request:

    $request->whenHas('name', function (string $input) {
        // The "name" value is present...
    }, function () {
        // The "name" value is not present...
    });

The `hasAny` method returns `true` if any of the specified values are present:

    if ($request->hasAny(['name', 'email'])) {
        // ...
    }

If you would like to determine if a value is present on the request and is not an empty string, you may use the `filled` method:

    if ($request->filled('name')) {
        // ...
    }

The `whenFilled` method will execute the given closure if a value is present on the request and is not an empty string:

    $request->whenFilled('name', function (string $input) {
        // ...
    });

A second closure may be passed to the `whenFilled` method that will be executed if the specified value is not "filled":

    $request->whenFilled('name', function (string $input) {
        // The "name" value is filled...
    }, function () {
        // The "name" value is not filled...
    });

To determine if a given key is absent from the request, you may use the `missing` and `whenMissing` methods:

    if ($request->missing('name')) {
        // ...
    }

    $request->whenMissing('name', function (array $input) {
        // The "name" value is missing...
    }, function () {
        // The "name" value is present...
    });

<a name="merging-additional-input"></a>
### Merging Additional Input

Sometimes you may need to manually merge additional input into the request's existing input data. To accomplish this, you may use the `merge` method. If a given input key already exists on the request, it will be overwritten by the data provided to the `merge` method:

    $request->merge(['votes' => 0]);

The `mergeIfMissing` method may be used to merge input into the request if the corresponding keys do not already exist within the request's input data:

    $request->mergeIfMissing(['votes' => 0]);

<a name="old-input"></a>
### Old Input

Laravel allows you to keep input from one request during the next request. This feature is particularly useful for re-populating forms after detecting validation errors. However, if you are using Laravel's included [validation features](/docs/{{version}}/validation), it is possible that you will not need to manually use these session input flashing methods directly, as some of Laravel's built-in validation facilities will call them automatically.

<a name="flashing-input-to-the-session"></a>
#### Flashing Input To The Session

The `flash` method on the `Illuminate\Http\Request` class will flash the current input to the [session](/docs/{{version}}/session) so that it is available during the user's next request to the application:

    $request->flash();

You may also use the `flashOnly` and `flashExcept` methods to flash a subset of the request data to the session. These methods are useful for keeping sensitive information such as passwords out of the session:

    $request->flashOnly(['username', 'email']);

    $request->flashExcept('password');

<a name="flashing-input-then-redirecting"></a>
#### Flashing Input Then Redirecting

Since you often will want to flash input to the session and then redirect to the previous page, you may easily chain input flashing onto a redirect using the `withInput` method:

    return redirect('form')->withInput();

    return redirect()->route('user.create')->withInput();

    return redirect('form')->withInput(
        $request->except('password')
    );

<a name="retrieving-old-input"></a>
#### Retrieving Old Input

To retrieve flashed input from the previous request, invoke the `old` method on an instance of `Illuminate\Http\Request`. The `old` method will pull the previously flashed input data from the [session](/docs/{{version}}/session):

    $username = $request->old('username');

Laravel also provides a global `old` helper. If you are displaying old input within a [Blade template](/docs/{{version}}/blade), it is more convenient to use the `old` helper to repopulate the form. If no old input exists for the given field, `null` will be returned:

    <input type="text" name="username" value="{{ old('username') }}">

<a name="cookies"></a>
### Cookies

<a name="retrieving-cookies-from-requests"></a>
#### Retrieving Cookies From Requests

All cookies created by the Laravel framework are encrypted and signed with an authentication code, meaning they will be considered invalid if they have been changed by the client. To retrieve a cookie value from the request, use the `cookie` method on an `Illuminate\Http\Request` instance:

    $value = $request->cookie('name');

<a name="input-trimming-and-normalization"></a>
## Input Trimming & Normalization

By default, Laravel includes the `App\Http\Middleware\TrimStrings` and `Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull` middleware in your application's global middleware stack. These middleware are listed in the global middleware stack by the `App\Http\Kernel` class. These middleware will automatically trim all incoming string fields on the request, as well as convert any empty string fields to `null`. This allows you to not have to worry about these normalization concerns in your routes and controllers.

#### Disabling Input Normalization

If you would like to disable this behavior for all requests, you may remove the two middleware from your application's middleware stack by removing them from the `$middleware` property of your `App\Http\Kernel` class.

If you would like to disable string trimming and empty string conversion for a subset of requests to your application, you may use the `skipWhen` method offered by both middleware. This method accepts a closure which should return `true` or `false` to indicate if input normalization should be skipped. Typically, the `skipWhen` method should be invoked in the `boot` method of your application's `AppServiceProvider`.

```php
use App\Http\Middleware\TrimStrings;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    TrimStrings::skipWhen(function (Request $request) {
        return $request->is('admin/*');
    });

    ConvertEmptyStringsToNull::skipWhen(function (Request $request) {
        // ...
    });
}
```

<a name="files"></a>
## Files

<a name="retrieving-uploaded-files"></a>
### Retrieving Uploaded Files

You may retrieve uploaded files from an `Illuminate\Http\Request` instance using the `file` method or using dynamic properties. The `file` method returns an instance of the `Illuminate\Http\UploadedFile` class, which extends the PHP `SplFileInfo` class and provides a variety of methods for interacting with the file:

    $file = $request->file('photo');

    $file = $request->photo;

You may determine if a file is present on the request using the `hasFile` method:

    if ($request->hasFile('photo')) {
        // ...
    }

<a name="validating-successful-uploads"></a>
#### Validating Successful Uploads

In addition to checking if the file is present, you may verify that there were no problems uploading the file via the `isValid` method:

    if ($request->file('photo')->isValid()) {
        // ...
    }

<a name="file-paths-extensions"></a>
#### File Paths & Extensions

The `UploadedFile` class also contains methods for accessing the file's fully-qualified path and its extension. The `extension` method will attempt to guess the file's extension based on its contents. This extension may be different from the extension that was supplied by the client:

    $path = $request->photo->path();

    $extension = $request->photo->extension();

<a name="other-file-methods"></a>
#### Other File Methods

There are a variety of other methods available on `UploadedFile` instances. Check out the [API documentation for the class](https://github.com/symfony/symfony/blob/6.0/src/Symfony/Component/HttpFoundation/File/UploadedFile.php) for more information regarding these methods.

<a name="storing-uploaded-files"></a>
### Storing Uploaded Files

To store an uploaded file, you will typically use one of your configured [filesystems](/docs/{{version}}/filesystem). The `UploadedFile` class has a `store` method that will move an uploaded file to one of your disks, which may be a location on your local filesystem or a cloud storage location like Amazon S3.

The `store` method accepts the path where the file should be stored relative to the filesystem's configured root directory. This path should not contain a filename, since a unique ID will automatically be generated to serve as the filename.

The `store` method also accepts an optional second argument for the name of the disk that should be used to store the file. The method will return the path of the file relative to the disk's root:

    $path = $request->photo->store('images');

    $path = $request->photo->store('images', 's3');

If you do not want a filename to be automatically generated, you may use the `storeAs` method, which accepts the path, filename, and disk name as its arguments:

    $path = $request->photo->storeAs('images', 'filename.jpg');

    $path = $request->photo->storeAs('images', 'filename.jpg', 's3');

> **Note**  
> For more information about file storage in Laravel, check out the complete [file storage documentation](/docs/{{version}}/filesystem).

<a name="configuring-trusted-proxies"></a>
## Configuring Trusted Proxies

When running your applications behind a load balancer that terminates TLS / SSL certificates, you may notice your application sometimes does not generate HTTPS links when using the `url` helper. Typically this is because your application is being forwarded traffic from your load balancer on port 80 and does not know it should generate secure links.

To solve this, you may use the `App\Http\Middleware\TrustProxies` middleware that is included in your Laravel application, which allows you to quickly customize the load balancers or proxies that should be trusted by your application. Your trusted proxies should be listed as an array on the `$proxies` property of this middleware. In addition to configuring the trusted proxies, you may configure the proxy `$headers` that should be trusted:

    <?php

    namespace App\Http\Middleware;

    use Illuminate\Http\Middleware\TrustProxies as Middleware;
    use Illuminate\Http\Request;

    class TrustProxies extends Middleware
    {
        /**
         * The trusted proxies for this application.
         *
         * @var string|array
         */
        protected $proxies = [
            '192.168.1.1',
            '192.168.1.2',
        ];

        /**
         * The headers that should be used to detect proxies.
         *
         * @var int
         */
        protected $headers = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO;
    }

> **Note**  
> If you are using AWS Elastic Load Balancing, your `$headers` value should be `Request::HEADER_X_FORWARDED_AWS_ELB`. For more information on the constants that may be used in the `$headers` property, check out Symfony's documentation on [trusting proxies](https://symfony.com/doc/current/deployment/proxies.html).

<a name="trusting-all-proxies"></a>
#### Trusting All Proxies

If you are using Amazon AWS or another "cloud" load balancer provider, you may not know the IP addresses of your actual balancers. In this case, you may use `*` to trust all proxies:

    /**
     * The trusted proxies for this application.
     *
     * @var string|array
     */
    protected $proxies = '*';

<a name="configuring-trusted-hosts"></a>
## Configuring Trusted Hosts

By default, Laravel will respond to all requests it receives regardless of the content of the HTTP request's `Host` header. In addition, the `Host` header's value will be used when generating absolute URLs to your application during a web request.

Typically, you should configure your web server, such as Nginx or Apache, to only send requests to your application that match a given host name. However, if you do not have the ability to customize your web server directly and need to instruct Laravel to only respond to certain host names, you may do so by enabling the `App\Http\Middleware\TrustHosts` middleware for your application.

The `TrustHosts` middleware is already included in the `$middleware` stack of your application; however, you should uncomment it so that it becomes active. Within this middleware's `hosts` method, you may specify the host names that your application should respond to. Incoming requests with other `Host` value headers will be rejected:

    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string>
     */
    public function hosts(): array
    {
        return [
            'laravel.test',
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }

The `allSubdomainsOfApplicationUrl` helper method will return a regular expression matching all subdomains of your application's `app.url` configuration value. This helper method provides a convenient way to allow all of your application's subdomains when building an application that utilizes wildcard subdomains.
