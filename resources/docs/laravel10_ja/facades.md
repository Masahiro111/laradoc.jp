# ファサード

- [はじめに](#introduction)
- [ファサードを使うタイミング](#when-to-use-facades)
     - [ファサードと依存性注入](#facades-vs-dependency-injection)
     - [ファサードとヘルパ関数](#facades-vs-helper-functions)
- [ファサードの仕組み](#how-facades-work)
- [リアルタイムファサード](#real-time-facades)
- [ファサードクラスリファレンス](#facade-class-reference)

<a name="introduction"></a>
## はじめに

Laravel のドキュメント全体で、「ファサード」を介して Laravel の機能とやりとりするコード例を見ることがあるでしょう。ファサードは、アプリケーションの [サービスコンテナ](/docs/{{version}}/container) で利用可能なクラスへの「静的」なインターフェースを提供します。Laravel には、ほぼすべての Laravel の機能にアクセスできる多くのファサードを搭載しています。

Laravel のファサードは、サービスコン「静的プロキシ」として機能し、従来の静的メソッドよりも高いテスト容易性と柔軟性を維持しながら、簡潔で表現力豊かな構文の利点を提供します。ファサードの仕組みが完全に理解できなくても大丈夫です。流れに沿って、Laravel について学習を続けてください。

Laravel のすべてのファサードは `Illuminate\Support\Facades` 名前空間で定義されています。そのため、以下のように簡単にファサードにアクセスできます。

    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Route;

    Route::get('/cache', function () {
        return Cache::get('key');
    });

Laravel ドキュメント全体を通じ、多くのコード例でファサードを使用して、フレームワークのさまざまな機能を実演しています。

<a name="helper-functions"></a>
#### ヘルパ関数

Laravel は、ファサードを補完するためのグローバルな「ヘルパ関数」を提供しており、Laravel 共通の機能を更に簡単に扱うことができます。 操作できる一般的なヘルパー関数として `view`、`response`、`url`、`config` などがあります。 Laravel が提供する各ヘルパ関数は、対応する機能とともにドキュメント化されています。完全なリストは専用の [ヘルパドキュメント](/docs/{{version}}/helpers) をご覧ください。

たとえば、`Illuminate\Support\Facades\Response` ファサードを使用して JSON レスポンスを生成する代わりに、単純に `response` 関数を使用することができます。ヘルパ関数はグローバルに利用可能なため、それらを使用するためにクラスをインポートする必要はありません。

    use Illuminate\Support\Facades\Response;

    Route::get('/users', function () {
        return Response::json([
            // ...
        ]);
    });

    Route::get('/users', function () {
        return response()->json([
            // ...
        ]);
    });

<a name="when-to-use-facades"></a>
## ファサードを使うタイミング

ファサードには多くの利点があります。手作業で挿入または設定する必要がある長いクラス名を覚えなくても、Laravel の機能を使用できる簡潔で覚えやすい構文を提供します。 さらに、PHP の動的メソッドを独自に使用しているため、テストが簡単です。

ただし、ファサードを使用する場合は注意が必要です。ファサードの主な危険性はクラスの「スコープクリープ」です。 ファサードは非常に使いやすく、依存性注入の必要がないため、クラス内のコードを成長させ続けてしまい 1つのクラスで多くのファサードを使用することが容易にになってしまうでしょう。 依存性注入を活用すると、大規模なコンストラクタによりクラスが大きくなっていないかを視覚的にフィードバックすることができるでしょう。したがって、ファサードを使用するときは、クラスの責任範囲が狭くならないように、クラスの規模に特に注意してください。クラスが大きくなりすぎる場合は、複数の小さなクラスに分割することを検討してください。

<a name="facades-vs-dependency-injection"></a>
### ファサードと依存性注入

依存性注入の主な利点の 1 つは、注入されたクラスの実装を交換できることです。これは、モックまたはスタブを挿入し、スタブでさまざまなメソッドが呼び出されたことを確認することができるため、テスト中に役立ちます。

通常、真に静的なクラスメソッドをモックしたりスタブしたりすることはできません。しかし、ファサードはサービスコンテナから解決されたオブジェクトへのメソッド呼び出しを動的メソッドでプロキシするため、注入されたクラスインスタンスのテストと同様にファサードをテストすることができます。 たとえば、次のルートがあるとします。

    use Illuminate\Support\Facades\Cache;

    Route::get('/cache', function () {
        return Cache::get('key');
    });

Laravel のファサードテストメソッドを使用して、`Cache::get` メソッドが期待した引数で呼び出されたことを確認するテストを次のように書くことができます。

    use Illuminate\Support\Facades\Cache;

    /**
     * A basic functional test example.
     */
    public function test_basic_example(): void
    {
        Cache::shouldReceive('get')
             ->with('key')
             ->andReturn('value');

        $response = $this->get('/cache');

        $response->assertSee('value');
    }

<a name="facades-vs-helper-functions"></a>
### ファサードとヘルパ関数

ファサードに加えて、Laravel には、ビューの生成、イベントの起動、ジョブのディスパッチ、HTTP レスポンスの送信などの一般的なタスクを実行できるさまざまな「ヘルパ」関数が含まれています。これらのヘルパ関数の多くは、対応するファサードと同じ機能を実行しています。たとえば、次のファサード呼び出しとヘルパ呼び出しは同等です。

    return Illuminate\Support\Facades\View::make('profile');

    return view('profile');

ファサードとヘルパ関数の間には実質的な違いはまったくありません。ヘルパ関数を使用する場合でも、対応するファサードとまったく同じ方法でテストできます。たとえば、次のルートがあるとします。

    Route::get('/cache', function () {
        return cache('key');
    });

`cache` ヘルパーは、`Cache` ファサードの基礎となるクラスの `get` メソッドを呼び出します。したがって、ヘルパ関数を使用している場合でも、次ようなテストを作成して、予期した引数でメソッドが呼び出されているかどうかを確認できます。

    use Illuminate\Support\Facades\Cache;

    /**
     * A basic functional test example.
     */
    public function test_basic_example(): void
    {
        Cache::shouldReceive('get')
             ->with('key')
             ->andReturn('value');

        $response = $this->get('/cache');

        $response->assertSee('value');
    }

<a name="how-facades-work"></a>
## How Facades Work

In a Laravel application, a facade is a class that provides access to an object from the container. The machinery that makes this work is in the `Facade` class. Laravel's facades, and any custom facades you create, will extend the base `Illuminate\Support\Facades\Facade` class.

The `Facade` base class makes use of the `__callStatic()` magic-method to defer calls from your facade to an object resolved from the container. In the example below, a call is made to the Laravel cache system. By glancing at this code, one might assume that the static `get` method is being called on the `Cache` class:

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Show the profile for the given user.
         */
        public function showProfile(string $id): View
        {
            $user = Cache::get('user:'.$id);

            return view('profile', ['user' => $user]);
        }
    }

Notice that near the top of the file we are "importing" the `Cache` facade. This facade serves as a proxy for accessing the underlying implementation of the `Illuminate\Contracts\Cache\Factory` interface. Any calls we make using the facade will be passed to the underlying instance of Laravel's cache service.

If we look at that `Illuminate\Support\Facades\Cache` class, you'll see that there is no static method `get`:

    class Cache extends Facade
    {
        /**
         * Get the registered name of the component.
         */
        protected static function getFacadeAccessor(): string
        {
            return 'cache';
        }
    }

Instead, the `Cache` facade extends the base `Facade` class and defines the method `getFacadeAccessor()`. This method's job is to return the name of a service container binding. When a user references any static method on the `Cache` facade, Laravel resolves the `cache` binding from the [service container](/docs/{{version}}/container) and runs the requested method (in this case, `get`) against that object.

<a name="real-time-facades"></a>
## Real-Time Facades

Using real-time facades, you may treat any class in your application as if it was a facade. To illustrate how this can be used, let's first examine some code that does not use real-time facades. For example, let's assume our `Podcast` model has a `publish` method. However, in order to publish the podcast, we need to inject a `Publisher` instance:

    <?php

    namespace App\Models;

    use App\Contracts\Publisher;
    use Illuminate\Database\Eloquent\Model;

    class Podcast extends Model
    {
        /**
         * Publish the podcast.
         */
        public function publish(Publisher $publisher): void
        {
            $this->update(['publishing' => now()]);

            $publisher->publish($this);
        }
    }

Injecting a publisher implementation into the method allows us to easily test the method in isolation since we can mock the injected publisher. However, it requires us to always pass a publisher instance each time we call the `publish` method. Using real-time facades, we can maintain the same testability while not being required to explicitly pass a `Publisher` instance. To generate a real-time facade, prefix the namespace of the imported class with `Facades`:

    <?php

    namespace App\Models;

    use Facades\App\Contracts\Publisher;
    use Illuminate\Database\Eloquent\Model;

    class Podcast extends Model
    {
        /**
         * Publish the podcast.
         */
        public function publish(): void
        {
            $this->update(['publishing' => now()]);

            Publisher::publish($this);
        }
    }

When the real-time facade is used, the publisher implementation will be resolved out of the service container using the portion of the interface or class name that appears after the `Facades` prefix. When testing, we can use Laravel's built-in facade testing helpers to mock this method call:

    <?php

    namespace Tests\Feature;

    use App\Models\Podcast;
    use Facades\App\Contracts\Publisher;
    use Illuminate\Foundation\Testing\RefreshDatabase;
    use Tests\TestCase;

    class PodcastTest extends TestCase
    {
        use RefreshDatabase;

        /**
         * A test example.
         */
        public function test_podcast_can_be_published(): void
        {
            $podcast = Podcast::factory()->create();

            Publisher::shouldReceive('publish')->once()->with($podcast);

            $podcast->publish();
        }
    }

<a name="facade-class-reference"></a>
## Facade Class Reference

Below you will find every facade and its underlying class. This is a useful tool for quickly digging into the API documentation for a given facade root. The [service container binding](/docs/{{version}}/container) key is also included where applicable.

<div class="overflow-auto">

Facade  |  Class  |  Service Container Binding
------------- | ------------- | -------------
App  |  [Illuminate\Foundation\Application](https://laravel.com/api/{{version}}/Illuminate/Foundation/Application.html)  |  `app`
Artisan  |  [Illuminate\Contracts\Console\Kernel](https://laravel.com/api/{{version}}/Illuminate/Contracts/Console/Kernel.html)  |  `artisan`
Auth  |  [Illuminate\Auth\AuthManager](https://laravel.com/api/{{version}}/Illuminate/Auth/AuthManager.html)  |  `auth`
Auth (Instance)  |  [Illuminate\Contracts\Auth\Guard](https://laravel.com/api/{{version}}/Illuminate/Contracts/Auth/Guard.html)  |  `auth.driver`
Blade  |  [Illuminate\View\Compilers\BladeCompiler](https://laravel.com/api/{{version}}/Illuminate/View/Compilers/BladeCompiler.html)  |  `blade.compiler`
Broadcast  |  [Illuminate\Contracts\Broadcasting\Factory](https://laravel.com/api/{{version}}/Illuminate/Contracts/Broadcasting/Factory.html)  |  &nbsp;
Broadcast (Instance)  |  [Illuminate\Contracts\Broadcasting\Broadcaster](https://laravel.com/api/{{version}}/Illuminate/Contracts/Broadcasting/Broadcaster.html)  |  &nbsp;
Bus  |  [Illuminate\Contracts\Bus\Dispatcher](https://laravel.com/api/{{version}}/Illuminate/Contracts/Bus/Dispatcher.html)  |  &nbsp;
Cache  |  [Illuminate\Cache\CacheManager](https://laravel.com/api/{{version}}/Illuminate/Cache/CacheManager.html)  |  `cache`
Cache (Instance)  |  [Illuminate\Cache\Repository](https://laravel.com/api/{{version}}/Illuminate/Cache/Repository.html)  |  `cache.store`
Config  |  [Illuminate\Config\Repository](https://laravel.com/api/{{version}}/Illuminate/Config/Repository.html)  |  `config`
Cookie  |  [Illuminate\Cookie\CookieJar](https://laravel.com/api/{{version}}/Illuminate/Cookie/CookieJar.html)  |  `cookie`
Crypt  |  [Illuminate\Encryption\Encrypter](https://laravel.com/api/{{version}}/Illuminate/Encryption/Encrypter.html)  |  `encrypter`
Date  |  [Illuminate\Support\DateFactory](https://laravel.com/api/{{version}}/Illuminate/Support/DateFactory.html)  |  `date`
DB  |  [Illuminate\Database\DatabaseManager](https://laravel.com/api/{{version}}/Illuminate/Database/DatabaseManager.html)  |  `db`
DB (Instance)  |  [Illuminate\Database\Connection](https://laravel.com/api/{{version}}/Illuminate/Database/Connection.html)  |  `db.connection`
Event  |  [Illuminate\Events\Dispatcher](https://laravel.com/api/{{version}}/Illuminate/Events/Dispatcher.html)  |  `events`
File  |  [Illuminate\Filesystem\Filesystem](https://laravel.com/api/{{version}}/Illuminate/Filesystem/Filesystem.html)  |  `files`
Gate  |  [Illuminate\Contracts\Auth\Access\Gate](https://laravel.com/api/{{version}}/Illuminate/Contracts/Auth/Access/Gate.html)  |  &nbsp;
Hash  |  [Illuminate\Contracts\Hashing\Hasher](https://laravel.com/api/{{version}}/Illuminate/Contracts/Hashing/Hasher.html)  |  `hash`
Http  |  [Illuminate\Http\Client\Factory](https://laravel.com/api/{{version}}/Illuminate/Http/Client/Factory.html)  |  &nbsp;
Lang  |  [Illuminate\Translation\Translator](https://laravel.com/api/{{version}}/Illuminate/Translation/Translator.html)  |  `translator`
Log  |  [Illuminate\Log\LogManager](https://laravel.com/api/{{version}}/Illuminate/Log/LogManager.html)  |  `log`
Mail  |  [Illuminate\Mail\Mailer](https://laravel.com/api/{{version}}/Illuminate/Mail/Mailer.html)  |  `mailer`
Notification  |  [Illuminate\Notifications\ChannelManager](https://laravel.com/api/{{version}}/Illuminate/Notifications/ChannelManager.html)  |  &nbsp;
Password  |  [Illuminate\Auth\Passwords\PasswordBrokerManager](https://laravel.com/api/{{version}}/Illuminate/Auth/Passwords/PasswordBrokerManager.html)  |  `auth.password`
Password (Instance)  |  [Illuminate\Auth\Passwords\PasswordBroker](https://laravel.com/api/{{version}}/Illuminate/Auth/Passwords/PasswordBroker.html)  |  `auth.password.broker`
Pipeline (Instance)  |  [Illuminate\Pipeline\Pipeline](https://laravel.com/api/{{version}}/Illuminate/Pipeline/Pipeline.html)  |  &nbsp;
Queue  |  [Illuminate\Queue\QueueManager](https://laravel.com/api/{{version}}/Illuminate/Queue/QueueManager.html)  |  `queue`
Queue (Instance)  |  [Illuminate\Contracts\Queue\Queue](https://laravel.com/api/{{version}}/Illuminate/Contracts/Queue/Queue.html)  |  `queue.connection`
Queue (Base Class)  |  [Illuminate\Queue\Queue](https://laravel.com/api/{{version}}/Illuminate/Queue/Queue.html)  |  &nbsp;
Redirect  |  [Illuminate\Routing\Redirector](https://laravel.com/api/{{version}}/Illuminate/Routing/Redirector.html)  |  `redirect`
Redis  |  [Illuminate\Redis\RedisManager](https://laravel.com/api/{{version}}/Illuminate/Redis/RedisManager.html)  |  `redis`
Redis (Instance)  |  [Illuminate\Redis\Connections\Connection](https://laravel.com/api/{{version}}/Illuminate/Redis/Connections/Connection.html)  |  `redis.connection`
Request  |  [Illuminate\Http\Request](https://laravel.com/api/{{version}}/Illuminate/Http/Request.html)  |  `request`
Response  |  [Illuminate\Contracts\Routing\ResponseFactory](https://laravel.com/api/{{version}}/Illuminate/Contracts/Routing/ResponseFactory.html)  |  &nbsp;
Response (Instance)  |  [Illuminate\Http\Response](https://laravel.com/api/{{version}}/Illuminate/Http/Response.html)  |  &nbsp;
Route  |  [Illuminate\Routing\Router](https://laravel.com/api/{{version}}/Illuminate/Routing/Router.html)  |  `router`
Schema  |  [Illuminate\Database\Schema\Builder](https://laravel.com/api/{{version}}/Illuminate/Database/Schema/Builder.html)  |  &nbsp;
Session  |  [Illuminate\Session\SessionManager](https://laravel.com/api/{{version}}/Illuminate/Session/SessionManager.html)  |  `session`
Session (Instance)  |  [Illuminate\Session\Store](https://laravel.com/api/{{version}}/Illuminate/Session/Store.html)  |  `session.store`
Storage  |  [Illuminate\Filesystem\FilesystemManager](https://laravel.com/api/{{version}}/Illuminate/Filesystem/FilesystemManager.html)  |  `filesystem`
Storage (Instance)  |  [Illuminate\Contracts\Filesystem\Filesystem](https://laravel.com/api/{{version}}/Illuminate/Contracts/Filesystem/Filesystem.html)  |  `filesystem.disk`
URL  |  [Illuminate\Routing\UrlGenerator](https://laravel.com/api/{{version}}/Illuminate/Routing/UrlGenerator.html)  |  `url`
Validator  |  [Illuminate\Validation\Factory](https://laravel.com/api/{{version}}/Illuminate/Validation/Factory.html)  |  `validator`
Validator (Instance)  |  [Illuminate\Validation\Validator](https://laravel.com/api/{{version}}/Illuminate/Validation/Validator.html)  |  &nbsp;
View  |  [Illuminate\View\Factory](https://laravel.com/api/{{version}}/Illuminate/View/Factory.html)  |  `view`
View (Instance)  |  [Illuminate\View\View](https://laravel.com/api/{{version}}/Illuminate/View/View.html)  |  &nbsp;
Vite  |  [Illuminate\Foundation\Vite](https://laravel.com/api/{{version}}/Illuminate/Foundation/Vite.html)  |  &nbsp;

</div>
