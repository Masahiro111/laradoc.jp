# サービスプロバイダー

- [はじめに](#introduction)
- [サービスプロバイダの作成](#writing-service-providers)
     - [register メソッド](#the-register-method)
     - [boot メソッド](#the-boot-method)
- [プロバイダの登録](#registering-providers)
- [遅延プロバイダ](#deferred-providers)

<a name="introduction"></a>
## はじめに

サービスプロバイダは、Laravel アプリケーションの起動を処理する中心的な場所です。あなたが作成するアプリケーション同様に、Laravel のすべてのコアサービスは、サービスプロバイダを経由して **初期起動処理** を行っています。

しかし、「**初期起動処理**」とは何を意味するのでしょうか？ 一般的には、サービスコンテナによる結合、イベントリスナ、ミドルウェア、さらにはルートの登録など、**登録する** ことを意味します。サービスプロバイダは、アプリケーションを設定するための中心的な場所です。

Laravel に含まれている `config/app.php` ファイルを開くと、`providers` 配列がありあす。これは、アプリケーションにロードされるすべてのサービスプロバイダクラスです。デフォルトでは、Laravelコアサービスプロバイダの一連のセットがこの配列にリストされています。これらのプロバイダは、メーラー、キュー、キャッシュなどの Laravel コアコンポーネントを処理起動処理します。これらのプロバイダの多くは「遅延」プロバイダです。つまり、すべてのリクエストでロードされるわけではなく、提供されるサービスが実際に必要になったときにのみロードされます。

この概要では、独自のサービス プロバイダーを作成し、それらを Laravel アプリケーションに登録する方法を学習します。
この概要では、独自のサービスプロバイダを作成し、Laravelアプリケーションに登録する方法を学びます。

> **Note**
> Laravel がリクエストを処理し、内部でどのように機能するかについて詳しく知りたい場合は、Laravel [リクエスト ライフサイクル](/docs/{{version}}/lifecycle) に関するドキュメントをご覧ください。

<a name="writing-service-providers"></a>
## サービスプロバイダの作成

すべてのサービスプロバイダは、`Illuminate\Support\ServiceProvider` クラスを拡張します。 ほとんどのサービス プロバイダには、「register」メソッドと「boot」メソッドが含まれています。 `register` メソッド内では、**[サービスコンテナー](/docs/{{version}}/container)** にバインドするものだけを記述してください。`register` メソッド内で、イベントリスナ、ルート、またはその他の機能を登録しようとしないでください。

Artisan CLI は、`make:provider` コマンドを使って新しいプロバイダを生成できます。

```shell
php artisan make:provider RiakServiceProvider
```

<a name="the-register-method"></a>
### The Register Method

As mentioned previously, within the `register` method, you should only bind things into the [service container](/docs/{{version}}/container). You should never attempt to register any event listeners, routes, or any other piece of functionality within the `register` method. Otherwise, you may accidentally use a service that is provided by a service provider which has not loaded yet.

Let's take a look at a basic service provider. Within any of your service provider methods, you always have access to the `$app` property which provides access to the service container:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            $this->app->singleton(Connection::class, function (Application $app) {
                return new Connection(config('riak'));
            });
        }
    }

This service provider only defines a `register` method, and uses that method to define an implementation of `App\Services\Riak\Connection` in the service container. If you're not yet familiar with Laravel's service container, check out [its documentation](/docs/{{version}}/container).

<a name="the-bindings-and-singletons-properties"></a>
#### The `bindings` And `singletons` Properties

If your service provider registers many simple bindings, you may wish to use the `bindings` and `singletons` properties instead of manually registering each container binding. When the service provider is loaded by the framework, it will automatically check for these properties and register their bindings:

    <?php

    namespace App\Providers;

    use App\Contracts\DowntimeNotifier;
    use App\Contracts\ServerProvider;
    use App\Services\DigitalOceanServerProvider;
    use App\Services\PingdomDowntimeNotifier;
    use App\Services\ServerToolsProvider;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * All of the container bindings that should be registered.
         *
         * @var array
         */
        public $bindings = [
            ServerProvider::class => DigitalOceanServerProvider::class,
        ];

        /**
         * All of the container singletons that should be registered.
         *
         * @var array
         */
        public $singletons = [
            DowntimeNotifier::class => PingdomDowntimeNotifier::class,
            ServerProvider::class => ServerToolsProvider::class,
        ];
    }

<a name="the-boot-method"></a>
### The Boot Method

So, what if we need to register a [view composer](/docs/{{version}}/views#view-composers) within our service provider? This should be done within the `boot` method. **This method is called after all other service providers have been registered**, meaning you have access to all other services that have been registered by the framework:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;
    use Illuminate\Support\ServiceProvider;

    class ComposerServiceProvider extends ServiceProvider
    {
        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            View::composer('view', function () {
                // ...
            });
        }
    }

<a name="boot-method-dependency-injection"></a>
#### Boot Method Dependency Injection

You may type-hint dependencies for your service provider's `boot` method. The [service container](/docs/{{version}}/container) will automatically inject any dependencies you need:

    use Illuminate\Contracts\Routing\ResponseFactory;

    /**
     * Bootstrap any application services.
     */
    public function boot(ResponseFactory $response): void
    {
        $response->macro('serialized', function (mixed $value) {
            // ...
        });
    }

<a name="registering-providers"></a>
## Registering Providers

All service providers are registered in the `config/app.php` configuration file. This file contains a `providers` array where you can list the class names of your service providers. By default, a set of Laravel core service providers are listed in this array. These providers bootstrap the core Laravel components, such as the mailer, queue, cache, and others.

To register your provider, add it to the array:

    'providers' => [
        // Other Service Providers

        App\Providers\ComposerServiceProvider::class,
    ],

<a name="deferred-providers"></a>
## Deferred Providers

If your provider is **only** registering bindings in the [service container](/docs/{{version}}/container), you may choose to defer its registration until one of the registered bindings is actually needed. Deferring the loading of such a provider will improve the performance of your application, since it is not loaded from the filesystem on every request.

Laravel compiles and stores a list of all of the services supplied by deferred service providers, along with the name of its service provider class. Then, only when you attempt to resolve one of these services does Laravel load the service provider.

To defer the loading of a provider, implement the `\Illuminate\Contracts\Support\DeferrableProvider` interface and define a `provides` method. The `provides` method should return the service container bindings registered by the provider:

    <?php

    namespace App\Providers;

    use App\Services\Riak\Connection;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\Support\DeferrableProvider;
    use Illuminate\Support\ServiceProvider;

    class RiakServiceProvider extends ServiceProvider implements DeferrableProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            $this->app->singleton(Connection::class, function (Application $app) {
                return new Connection($app['config']['riak']);
            });
        }

        /**
         * Get the services provided by the provider.
         *
         * @return array<int, string>
         */
        public function provides(): array
        {
            return [Connection::class];
        }
    }
