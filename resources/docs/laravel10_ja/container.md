# サービスコンテナ

- [はじめに](#introduction)
     - [設定なしでの依存解決](#zero-configuration-resolution)
     - [コンテナを使うタイミング](#when-to-use-the-container)
- [結合](#binding)
     - [結合の基本](#binding-basics)
     - [インターフェイスを実装に結合する](#binding-interfaces-to-implementations)
     - [コンテキスト結合](#contextual-binding)
     - [プリミティブ結合](#binding-primitives)
     - [型付き可変長引数の結合](#binding-typed-variadics)
     - [タグ付け](#tagging)
     - [結合の拡張](#extending-bindings)
- [依存の解決](#resolving)
     - [Make メソッド](#the-make-method)
     - [自動注入](#automatic-injection)
- [メソッドの呼び出しと依存注入](#method-invocation-and-injection)
- [コンテナイベント](#container-events)
- [PSR-11](#psr-11)

<a name="introduction"></a>
## はじめに

Laravel のサービスコンテナは、クラスの依存関係を管理し、依存性の注入を行う強力なツールです。依存性注入とは、基本的には以下の意味です。クラスの依存関係は、コンストラクタを通じてクラスに「注入」されるか、場合によっては「セッター」メソッドを使って行われます。

簡単な例を見てみましょう。

    <?php

    namespace App\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Repositories\UserRepository;
    use App\Models\User;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Create a new controller instance.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}

        /**
         * Show the profile for the given user.
         */
        public function show(string $id): View
        {
            $user = $this->users->find($id);

            return view('user.profile', ['user' => $user]);
        }
    }

この例では、`UserController` はデータ ソースからユーザーを取得する必要があります。 そのため、ユーザーを取得できるサービスを**注入**します。 このコンテキストでは、`UserRepository` はおそらく [Eloquent](/docs/{{version}}/eloquent) を使用してデータベースからユーザー情報を取得します。 ただし、リポジトリは注入されているため、別の実装と簡単に交換できます。 アプリケーションをテストするときに、簡単に「モック」するか、`UserRepository` のダミー実装を作成することもできます。

Laravel コア自体に貢献するだけではなく、強力で大規模なアプリケーションを構築するには、Laravel のサービスコンテナーを深く理解することが不可欠です。

<a name="zero-configuration-resolution"></a>
### 設定なしでの依存解決

クラスに依存関係がないか、他の具体的なクラス (インターフェイスではない) にのみ依存している場合、そのクラスを解決する方法をコンテナーに指示する必要はありません。 たとえば、`routes/web.php` ファイルに次のコードを配置できます。

    <?php

    class Service
    {
        // ...
    }

    Route::get('/', function (Service $service) {
        die(get_class($service));
    });

この例では、アプリケーションの `/` ルートにアクセスすると、`Service` クラスが自動的に解決され、ルートのハンドラに注入されます。これは画期的です。これにより、設定ファイルが肥大化することなく、アプリケーションの開発を行い、依存性の注入を活用できます。

ありがたいことに、Laravel アプリケーションを構築する際に書く多くのクラスは、コンテナを介して自動的に依存関係を受け取ります。これには、[コントローラー](/docs/{{version}}/controllers)、[イベントリスナ](/docs/{{ version}}/events)、[ミドルウェア](/docs/{{version}}/middleware)  などが含まれます。さらに、[キューに入れられたジョブ](/docs/{{version}}/queues) の `handle` メソッドで依存関係を型指定することもできます。設定をせずとも、自動的に依存性注入の力を味わうと、それなしでは開発ができなくなる気がします。

<a name="when-to-use-the-container"></a>
### コンテナを使うタイミング

設定いらずで依存性の解決をすることができるため、ルートやコントローラ、イベントリスナなどで依存関係を型指定することで、コンテナと手動でやり取りすることなく作業を行うことがあると思います。例えば、現在のリクエストに簡単にアクセスできるように、ルート定義で `Illuminate\Http\Request` オブジェクトを型指定することができます。このコードを書くためにコンテナとやり取りする必要はありませんが、コンテナは依存関係の注入を裏で管理しています。

    use Illuminate\Http\Request;

    Route::get('/', function (Request $request) {
        // ...
    });

In many cases, thanks to automatic dependency injection and [facades](/docs/{{version}}/facades), you can build Laravel applications without **ever** manually binding or resolving anything from the container. **So, when would you ever manually interact with the container?** Let's examine two situations.

First, if you write a class that implements an interface and you wish to type-hint that interface on a route or class constructor, you must [tell the container how to resolve that interface](#binding-interfaces-to-implementations). Secondly, if you are [writing a Laravel package](/docs/{{version}}/packages) that you plan to share with other Laravel developers, you may need to bind your package's services into the container.

<a name="binding"></a>
## Binding

<a name="binding-basics"></a>
### Binding Basics

<a name="simple-bindings"></a>
#### Simple Bindings

Almost all of your service container bindings will be registered within [service providers](/docs/{{version}}/providers), so most of these examples will demonstrate using the container in that context.

Within a service provider, you always have access to the container via the `$this->app` property. We can register a binding using the `bind` method, passing the class or interface name that we wish to register along with a closure that returns an instance of the class:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->bind(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

Note that we receive the container itself as an argument to the resolver. We can then use the container to resolve sub-dependencies of the object we are building.

As mentioned, you will typically be interacting with the container within service providers; however, if you would like to interact with the container outside of a service provider, you may do so via the `App` [facade](/docs/{{version}}/facades):

    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Support\Facades\App;

    App::bind(Transistor::class, function (Application $app) {
        // ...
    });

> **Note**  
> There is no need to bind classes into the container if they do not depend on any interfaces. The container does not need to be instructed on how to build these objects, since it can automatically resolve these objects using reflection.

<a name="binding-a-singleton"></a>
#### Binding A Singleton

The `singleton` method binds a class or interface into the container that should only be resolved one time. Once a singleton binding is resolved, the same object instance will be returned on subsequent calls into the container:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->singleton(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

<a name="binding-scoped"></a>
#### Binding Scoped Singletons

The `scoped` method binds a class or interface into the container that should only be resolved one time within a given Laravel request / job lifecycle. While this method is similar to the `singleton` method, instances registered using the `scoped` method will be flushed whenever the Laravel application starts a new "lifecycle", such as when a [Laravel Octane](/docs/{{version}}/octane) worker processes a new request or when a Laravel [queue worker](/docs/{{version}}/queues) processes a new job:

    use App\Services\Transistor;
    use App\Services\PodcastParser;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->scoped(Transistor::class, function (Application $app) {
        return new Transistor($app->make(PodcastParser::class));
    });

<a name="binding-instances"></a>
#### Binding Instances

You may also bind an existing object instance into the container using the `instance` method. The given instance will always be returned on subsequent calls into the container:

    use App\Services\Transistor;
    use App\Services\PodcastParser;

    $service = new Transistor(new PodcastParser);

    $this->app->instance(Transistor::class, $service);

<a name="binding-interfaces-to-implementations"></a>
### Binding Interfaces To Implementations

A very powerful feature of the service container is its ability to bind an interface to a given implementation. For example, let's assume we have an `EventPusher` interface and a `RedisEventPusher` implementation. Once we have coded our `RedisEventPusher` implementation of this interface, we can register it with the service container like so:

    use App\Contracts\EventPusher;
    use App\Services\RedisEventPusher;

    $this->app->bind(EventPusher::class, RedisEventPusher::class);

This statement tells the container that it should inject the `RedisEventPusher` when a class needs an implementation of `EventPusher`. Now we can type-hint the `EventPusher` interface in the constructor of a class that is resolved by the container. Remember, controllers, event listeners, middleware, and various other types of classes within Laravel applications are always resolved using the container:

    use App\Contracts\EventPusher;

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected EventPusher $pusher
    ) {}

<a name="contextual-binding"></a>
### Contextual Binding

Sometimes you may have two classes that utilize the same interface, but you wish to inject different implementations into each class. For example, two controllers may depend on different implementations of the `Illuminate\Contracts\Filesystem\Filesystem` [contract](/docs/{{version}}/contracts). Laravel provides a simple, fluent interface for defining this behavior:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\UploadController;
    use App\Http\Controllers\VideoController;
    use Illuminate\Contracts\Filesystem\Filesystem;
    use Illuminate\Support\Facades\Storage;

    $this->app->when(PhotoController::class)
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('local');
              });

    $this->app->when([VideoController::class, UploadController::class])
              ->needs(Filesystem::class)
              ->give(function () {
                  return Storage::disk('s3');
              });

<a name="binding-primitives"></a>
### Binding Primitives

Sometimes you may have a class that receives some injected classes, but also needs an injected primitive value such as an integer. You may easily use contextual binding to inject any value your class may need:

    use App\Http\Controllers\UserController;
    
    $this->app->when(UserController::class)
              ->needs('$variableName')
              ->give($value);

Sometimes a class may depend on an array of [tagged](#tagging) instances. Using the `giveTagged` method, you may easily inject all of the container bindings with that tag:

    $this->app->when(ReportAggregator::class)
        ->needs('$reports')
        ->giveTagged('reports');

If you need to inject a value from one of your application's configuration files, you may use the `giveConfig` method:

    $this->app->when(ReportAggregator::class)
        ->needs('$timezone')
        ->giveConfig('app.timezone');

<a name="binding-typed-variadics"></a>
### Binding Typed Variadics

Occasionally, you may have a class that receives an array of typed objects using a variadic constructor argument:

    <?php

    use App\Models\Filter;
    use App\Services\Logger;

    class Firewall
    {
        /**
         * The filter instances.
         *
         * @var array
         */
        protected $filters;

        /**
         * Create a new class instance.
         */
        public function __construct(
            protected Logger $logger,
            Filter ...$filters,
        ) {
            $this->filters = $filters;
        }
    }

Using contextual binding, you may resolve this dependency by providing the `give` method with a closure that returns an array of resolved `Filter` instances:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give(function (Application $app) {
                    return [
                        $app->make(NullFilter::class),
                        $app->make(ProfanityFilter::class),
                        $app->make(TooLongFilter::class),
                    ];
              });

For convenience, you may also just provide an array of class names to be resolved by the container whenever `Firewall` needs `Filter` instances:

    $this->app->when(Firewall::class)
              ->needs(Filter::class)
              ->give([
                  NullFilter::class,
                  ProfanityFilter::class,
                  TooLongFilter::class,
              ]);

<a name="variadic-tag-dependencies"></a>
#### Variadic Tag Dependencies

Sometimes a class may have a variadic dependency that is type-hinted as a given class (`Report ...$reports`). Using the `needs` and `giveTagged` methods, you may easily inject all of the container bindings with that [tag](#tagging) for the given dependency:

    $this->app->when(ReportAggregator::class)
        ->needs(Report::class)
        ->giveTagged('reports');

<a name="tagging"></a>
### Tagging

Occasionally, you may need to resolve all of a certain "category" of binding. For example, perhaps you are building a report analyzer that receives an array of many different `Report` interface implementations. After registering the `Report` implementations, you can assign them a tag using the `tag` method:

    $this->app->bind(CpuReport::class, function () {
        // ...
    });

    $this->app->bind(MemoryReport::class, function () {
        // ...
    });

    $this->app->tag([CpuReport::class, MemoryReport::class], 'reports');

Once the services have been tagged, you may easily resolve them all via the container's `tagged` method:

    $this->app->bind(ReportAnalyzer::class, function (Application $app) {
        return new ReportAnalyzer($app->tagged('reports'));
    });

<a name="extending-bindings"></a>
### Extending Bindings

The `extend` method allows the modification of resolved services. For example, when a service is resolved, you may run additional code to decorate or configure the service. The `extend` method accepts two arguments, the service class you're extending and a closure that should return the modified service. The closure receives the service being resolved and the container instance:

    $this->app->extend(Service::class, function (Service $service, Application $app) {
        return new DecoratedService($service);
    });

<a name="resolving"></a>
## Resolving

<a name="the-make-method"></a>
### The `make` Method

You may use the `make` method to resolve a class instance from the container. The `make` method accepts the name of the class or interface you wish to resolve:

    use App\Services\Transistor;

    $transistor = $this->app->make(Transistor::class);

If some of your class's dependencies are not resolvable via the container, you may inject them by passing them as an associative array into the `makeWith` method. For example, we may manually pass the `$id` constructor argument required by the `Transistor` service:

    use App\Services\Transistor;

    $transistor = $this->app->makeWith(Transistor::class, ['id' => 1]);

If you are outside of a service provider in a location of your code that does not have access to the `$app` variable, you may use the `App` [facade](/docs/{{version}}/facades) or the `app` [helper](/docs/{{version}}/helpers#method-app) to resolve a class instance from the container:

    use App\Services\Transistor;
    use Illuminate\Support\Facades\App;

    $transistor = App::make(Transistor::class);

    $transistor = app(Transistor::class);

If you would like to have the Laravel container instance itself injected into a class that is being resolved by the container, you may type-hint the `Illuminate\Container\Container` class on your class's constructor:

    use Illuminate\Container\Container;

    /**
     * Create a new class instance.
     */
    public function __construct(
        protected Container $container
    ) {}

<a name="automatic-injection"></a>
### Automatic Injection

Alternatively, and importantly, you may type-hint the dependency in the constructor of a class that is resolved by the container, including [controllers](/docs/{{version}}/controllers), [event listeners](/docs/{{version}}/events), [middleware](/docs/{{version}}/middleware), and more. Additionally, you may type-hint dependencies in the `handle` method of [queued jobs](/docs/{{version}}/queues). In practice, this is how most of your objects should be resolved by the container.

For example, you may type-hint a repository defined by your application in a controller's constructor. The repository will automatically be resolved and injected into the class:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;
    use App\Models\User;

    class UserController extends Controller
    {
        /**
         * Create a new controller instance.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}

        /**
         * Show the user with the given ID.
         */
        public function show(string $id): User
        {
            $user = $this->users->findOrFail($id);

            return $user;
        }
    }

<a name="method-invocation-and-injection"></a>
## Method Invocation & Injection

Sometimes you may wish to invoke a method on an object instance while allowing the container to automatically inject that method's dependencies. For example, given the following class:

    <?php

    namespace App;

    use App\Repositories\UserRepository;

    class UserReport
    {
        /**
         * Generate a new user report.
         */
        public function generate(UserRepository $repository): array
        {
            return [
                // ...
            ];
        }
    }

You may invoke the `generate` method via the container like so:

    use App\UserReport;
    use Illuminate\Support\Facades\App;

    $report = App::call([new UserReport, 'generate']);

The `call` method accepts any PHP callable. The container's `call` method may even be used to invoke a closure while automatically injecting its dependencies:

    use App\Repositories\UserRepository;
    use Illuminate\Support\Facades\App;

    $result = App::call(function (UserRepository $repository) {
        // ...
    });

<a name="container-events"></a>
## Container Events

The service container fires an event each time it resolves an object. You may listen to this event using the `resolving` method:

    use App\Services\Transistor;
    use Illuminate\Contracts\Foundation\Application;

    $this->app->resolving(Transistor::class, function (Transistor $transistor, Application $app) {
        // Called when container resolves objects of type "Transistor"...
    });

    $this->app->resolving(function (mixed $object, Application $app) {
        // Called when container resolves object of any type...
    });

As you can see, the object being resolved will be passed to the callback, allowing you to set any additional properties on the object before it is given to its consumer.

<a name="psr-11"></a>
## PSR-11

Laravel's service container implements the [PSR-11](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-11-container.md) interface. Therefore, you may type-hint the PSR-11 container interface to obtain an instance of the Laravel container:

    use App\Services\Transistor;
    use Psr\Container\ContainerInterface;

    Route::get('/', function (ContainerInterface $container) {
        $service = $container->get(Transistor::class);

        // ...
    });

An exception is thrown if the given identifier can't be resolved. The exception will be an instance of `Psr\Container\NotFoundExceptionInterface` if the identifier was never bound. If the identifier was bound but was unable to be resolved, an instance of `Psr\Container\ContainerExceptionInterface` will be thrown.
