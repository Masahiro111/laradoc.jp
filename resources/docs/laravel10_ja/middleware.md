# ミドルウェア

- [はじめに](#introduction)
- [ミドルウェアの定義](#defining-middleware)
- [ミドルウェアの登録](#registering-middleware)
    - [グローバルミドルウェア](#global-middleware)
    - [ルートへのミドルウェアの割り当て](#assigning-middleware-to-routes)
    - [ミドルウェアグループ](#middleware-groups)
    - [ミドルウェアの並び替え](#sorting-middleware)
- [ミドルウェアのパラメータ](#middleware-parameters)
- [ミドルウェアの修了処理](#terminable-middleware)

<a name="introduction"></a>
##  はじめに

ミドルウェアは、アプリケーションに入る HTTP リクエストを検査およびフィルタリングするための便利なメカニズムを提供します。たとえば、Laravel には、アプリケーションのユーザーが認証されていることを確認するミドルウェアが含まれています。ユーザーが認証されていない場合、ミドルウェアはユーザーをアプリケーションのログイン画面にリダイレクトします。ただし、ユーザーが認証されている場合、ミドルウェアはリクエストがアプリケーション内にさらに進むことを許可します。

追加のミドルウェアを作成して、認証以外のさまざまなタスクを実行できます。たとえば、ログミドルウェアは、アプリケーションが受信したすべてのリクエストをログに記録できるようになります。Laravel フレームワークには、認証や CSRF 保護用のミドルウェアなど、いくつかのミドルウェアが含まれています。これらのミドルウェアはすべて `app/Http/Middleware` ディレクトリにあります。

<a name="defining-middleware"></a>
## ミドルウェアの定義

新しいミドルウェアを作成するには、`make:middleware` Artisan コマンドを使用します。

```shell
php artisan make:middleware EnsureTokenIsValid
```

このコマンドは、新しい `EnsureTokenIsValid` クラスを `app/Http/Middleware` ディレクトリ内に配置します。このミドルウェアでは、指定された `token` 入力が指定された値と一致する場合にのみ、ルートへのアクセスを許可します。 それ以外の場合は、ユーザーを `home` URI にリダイレクトさせます。

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class EnsureTokenIsValid
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            if ($request->input('token') !== 'my-secret-token') {
                return redirect('home');
            }

            return $next($request);
        }
    }

ご覧のとおり、指定された `token` がシークレットトークンと一致しない場合、ミドルウェアはクライアントに HTTP リダイレクトを返します。それ以外の場合、リクエストはさらにアプリケーションに渡されます。リクエストをアプリケーションのさらに奥深くに渡すには (ミドルウェアが「渡す」ことができるように)、`$request` を使用して `$next` コールバックを呼び出す必要があります。

ミドルウェアは、HTTP リクエストがアプリケーションに到達する前に通過する必要がある一連の「レイヤー」であると考えるのが最善です。各層はリクエストを検査したり、完全に拒否したりすることもできます。

> **Note**  
> すべてのミドルウェアは [サービスコンテナ](/docs/{{version}}/container) を介して解決されるため、ミドルウェアのコンストラクタ内で必要な依存関係をタイプヒントで指定できます。

<a name="before-after-middleware"></a>
<a name="middleware-and-responses"></a>
#### ミドルウェアとレスポンス

ミドルウェアは、リクエストをアプリケーションの奥深くに渡す前、または後にタスクを実行できます。たとえば、次のミドルウェアは、アプリケーションによってリクエストが処理される **前に** 何らかのタスクを実行します。

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class BeforeMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            // Perform action

            return $next($request);
        }
    }

一方で、以下のミドルウェアは、リクエストがアプリケーションによって処理された **後** にタスクを実行します。

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class AfterMiddleware
    {
        public function handle(Request $request, Closure $next): Response
        {
            $response = $next($request);

            // Perform action

            return $response;
        }
    }

<a name="registering-middleware"></a>
## ミドルウェアの登録

<a name="global-middleware"></a>
### グローバルミドルウェア

アプリケーションへのすべての HTTP リクエスト中にミドルウェアを実行したい場合は、`app/Http/Kernel.php` クラスの `$middleware` プロパティにミドルウェアクラスを登録します。

<a name="assigning-middleware-to-routes"></a>
### ルートへのミドルウェアの割り当て

ミドルウェアを特定のルートに割り当てたい場合は、ルートを定義するときに `middleware` メソッドを呼び出してください。

    use App\Http\Middleware\Authenticate;

    Route::get('/profile', function () {
        // ...
    })->middleware(Authenticate::class);

ミドルウェア名の配列を `middleware` メソッドに渡すことで、複数のミドルウェアをルートに割り当てることができます。

    Route::get('/', function () {
        // ...
    })->middleware([First::class, Second::class]);

便宜上、アプリケーションの `app/Http/Kernel.php` ファイル内のミドルウェアにエイリアスを割り当てることができます。デフォルトでは、このクラスの `$middlewareAliases` プロパティには、Laravel に含まれるミドルウェアのエントリが含まれています。独自のミドルウェアをこのリストに追加し、好きな別名を割り当てることができます。

    // Within App\Http\Kernel class...

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

Once the middleware alias has been defined in the HTTP kernel, you may use the alias when assigning middleware to routes:

    Route::get('/profile', function () {
        // ...
    })->middleware('auth');

<a name="excluding-middleware"></a>
#### Excluding Middleware

When assigning middleware to a group of routes, you may occasionally need to prevent the middleware from being applied to an individual route within the group. You may accomplish this using the `withoutMiddleware` method:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::middleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/', function () {
            // ...
        });

        Route::get('/profile', function () {
            // ...
        })->withoutMiddleware([EnsureTokenIsValid::class]);
    });

You may also exclude a given set of middleware from an entire [group](/docs/{{version}}/routing#route-groups) of route definitions:

    use App\Http\Middleware\EnsureTokenIsValid;

    Route::withoutMiddleware([EnsureTokenIsValid::class])->group(function () {
        Route::get('/profile', function () {
            // ...
        });
    });

The `withoutMiddleware` method can only remove route middleware and does not apply to [global middleware](#global-middleware).

<a name="middleware-groups"></a>
### Middleware Groups

Sometimes you may want to group several middleware under a single key to make them easier to assign to routes. You may accomplish this using the `$middlewareGroups` property of your HTTP kernel.

Laravel includes predefined `web` and `api` middleware groups that contain common middleware you may want to apply to your web and API routes. Remember, these middleware groups are automatically applied by your application's `App\Providers\RouteServiceProvider` service provider to routes within your corresponding `web` and `api` route files:

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

Middleware groups may be assigned to routes and controller actions using the same syntax as individual middleware. Again, middleware groups make it more convenient to assign many middleware to a route at once:

    Route::get('/', function () {
        // ...
    })->middleware('web');

    Route::middleware(['web'])->group(function () {
        // ...
    });

> **Note**  
> Out of the box, the `web` and `api` middleware groups are automatically applied to your application's corresponding `routes/web.php` and `routes/api.php` files by the `App\Providers\RouteServiceProvider`.

<a name="sorting-middleware"></a>
### Sorting Middleware

Rarely, you may need your middleware to execute in a specific order but not have control over their order when they are assigned to the route. In this case, you may specify your middleware priority using the `$middlewarePriority` property of your `app/Http/Kernel.php` file. This property may not exist in your HTTP kernel by default. If it does not exist, you may copy its default definition below:

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var string[]
     */
    protected $middlewarePriority = [
        \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

<a name="middleware-parameters"></a>
## Middleware Parameters

Middleware can also receive additional parameters. For example, if your application needs to verify that the authenticated user has a given "role" before performing a given action, you could create an `EnsureUserHasRole` middleware that receives a role name as an additional argument.

Additional middleware parameters will be passed to the middleware after the `$next` argument:

    <?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class EnsureUserHasRole
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next, string $role): Response
        {
            if (! $request->user()->hasRole($role)) {
                // Redirect...
            }

            return $next($request);
        }

    }

Middleware parameters may be specified when defining the route by separating the middleware name and parameters with a `:`. Multiple parameters should be delimited by commas:

    Route::put('/post/{id}', function (string $id) {
        // ...
    })->middleware('role:editor');

<a name="terminable-middleware"></a>
## Terminable Middleware

Sometimes a middleware may need to do some work after the HTTP response has been sent to the browser. If you define a `terminate` method on your middleware and your web server is using FastCGI, the `terminate` method will automatically be called after the response is sent to the browser:

    <?php

    namespace Illuminate\Session\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;

    class TerminatingMiddleware
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            return $next($request);
        }

        /**
         * Handle tasks after the response has been sent to the browser.
         */
        public function terminate(Request $request, Response $response): void
        {
            // ...
        }
    }

The `terminate` method should receive both the request and the response. Once you have defined a terminable middleware, you should add it to the list of routes or global middleware in the `app/Http/Kernel.php` file.

When calling the `terminate` method on your middleware, Laravel will resolve a fresh instance of the middleware from the [service container](/docs/{{version}}/container). If you would like to use the same middleware instance when the `handle` and `terminate` methods are called, register the middleware with the container using the container's `singleton` method. Typically this should be done in the `register` method of your `AppServiceProvider`:

    use App\Http\Middleware\TerminatingMiddleware;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TerminatingMiddleware::class);
    }
