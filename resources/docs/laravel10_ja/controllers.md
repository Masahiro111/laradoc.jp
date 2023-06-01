# コントローラ

- [はじめに](#introduction)
- [コントローラの記述](#writing-controllers)
     - [コントローラの基本](#basic-controllers)
     - [シングルアクションコントローラ](#single-action-controllers)
- [コントローラミドルウェア](#controller-middleware)
- [リソースコントローラ](#resource-controllers)
     - [部分的なリソースルート](#restful-partial-resource-routes)
     - [ネストされたリソース](#restful-nested-resources)
     - [リソースルートの命名](#restful-naming-resource-routes)
     - [リソースルートパラメータの命名](#restful-naming-resource-route-parameters)
     - [リソースルートのスコープ設定](#restful-scoping-resource-routes)
     - [リソース URI のローカライズ](#restful-localizing-resource-uris)
     - [リソース コントローラの補足](#restful-supplementing-resource-controllers)
     - [シングルトンリソースコントローラ](#singleton-resource-controllers)
- [依存性注入とコントローラ](#dependency-injection-and-controllers)

<a name="introduction"></a>
## はじめに

すべてのリクエスト処理ロジックをルートファイル内のクロージャとして定義する代わりに、「コントローラ」クラスを使用してこの動作を整理することをお勧めします。コントローラは、関連するリクエスト処理ロジックを 1 つのクラスにグループ化できます。 たとえば、`UserController` クラスは、ユーザーの表示、作成、更新、削除など、ユーザーに関連するすべての受信リクエストを処理します。 デフォルトでは、コントローラは `app/Http/Controllers` ディレクトリに保存されます。

<a name="writing-controllers"></a>
## コントローラの記述

<a name="basic-controllers"></a>
### コントローラの基本

新しいコントローラをすばやく生成するには、`make:controller` Artisan コマンドを実行します。デフォルトでは、アプリケーションのすべてのコントローラは `app/Http/Controllers` ディレクトリに保存されます。

```shell
php artisan make:controller UserController
```

基本的なコントローラの例を見てみましょう。コントローラには、受信した HTTP リクエストに応答するパブリックメソッドをいくつでも持つことができます。

    <?php

    namespace App\Http\Controllers;
    
    use App\Models\User;
    use Illuminate\View\View;

    class UserController extends Controller
    {
        /**
         * Show the profile for a given user.
         */
        public function show(string $id): View
        {
            return view('user.profile', [
                'user' => User::findOrFail($id)
            ]);
        }
    }

コントローラのクラスとメソッドを作成したら、次のようにコントローラメソッドへのルートを定義できます。

    use App\Http\Controllers\UserController;

    Route::get('/user/{id}', [UserController::class, 'show']);

受信リクエストが指定したルート URI と一致すると、`App\Http\Controllers\UserController` クラスの `show` メソッドが呼び出され、ルートパラメータがメソッドに渡されます。

> **Note**  
> コントローラは基底クラスを拡張する **必要はありません**。 しかしながら、`middleware` メソッドや `authorize`メソッドなどの便利な機能にはアクセスできません。

<a name="single-action-controllers"></a>
### シングルアクションコントローラ

コントローラのアクションが特に複雑な場合は、コントローラクラス全体をその 1 つのアクション専用にすると便利な場合があります。これを実現するには、コントローラ内で単一の `__invoke` メソッドを定義します。

    <?php

    namespace App\Http\Controllers;
    
    use App\Models\User;
    use Illuminate\Http\Response;

    class ProvisionServer extends Controller
    {
        /**
         * Provision a new web server.
         */
        public function __invoke()
        {
            // ...
        }
    }

シングルアクションコントローラのルートを登録する場合、コントローラメソッドを指定する必要はありません。コントローラの名前をルーターに渡すだけで済みます。

    use App\Http\Controllers\ProvisionServer;

    Route::post('/server', ProvisionServer::class);

`make:controller` Artisan コマンドの `--invokable` オプションを使用して、呼び出し可能なコントローラを生成できます。

```shell
php artisan make:controller ProvisionServer --invokable
```

> **Note**  
> コントローラのスタブについては、[スタブの公開](/docs/{{version}}/artisan#stub-customization) をご覧ください。また、スタブのカスタマイズもできます。

<a name="controller-middleware"></a>
## コントローラミドルウェア

[ミドルウェア](/docs/{{version}}/middleware) は、ルートファイル内のコントローラのルートに割り当てることができます。

    Route::get('profile', [UserController::class, 'show'])->middleware('auth');

または、コントローラのコンストラクタ内でミドルウェアを指定すると便利な場合があります。コントローラのコンストラクタ内で `middleware` メソッドを使用すると、コントローラのアクションにミドルウェアを割り当てることができます。

    class UserController extends Controller
    {
        /**
         * Instantiate a new controller instance.
         */
        public function __construct()
        {
            $this->middleware('auth');
            $this->middleware('log')->only('index');
            $this->middleware('subscribed')->except('store');
        }
    }

コントローラでは、クロージャを使用してミドルウェアを登録することもできます。これにより、ミドルウェアクラス全体を定義せずに、単一のコントローラのインラインミドルウェアを定義する便利な方法が提供されます。

    use Closure;
    use Illuminate\Http\Request;

    $this->middleware(function (Request $request, Closure $next) {
        return $next($request);
    });

<a name="resource-controllers"></a>
## リソースコントローラ

アプリケーション内の各 Eloquent モデルを「リソース」と考えると、アプリケーション内の各リソースに対して同じ一連のアクションを実行するのが一般的です。たとえば、アプリケーションに `Photo` モデルと `Movie` モデルが含まれていると想像してください。ユーザーはこれらのリソースを作成、読み取り、更新、または削除できる可能性があります。

この一般的なユースケースのため、Laravel リソースルーティングは、1 行のコードで典型的な作成、読み取り、更新、および削除 (「CRUD」) ルートをコントローラに割り当てます。 まず、`make:controller` Artisan コマンドの `--resource` オプションを使用して、これらのアクションを処理するコントローラをすばやく作成できます。

```shell
php artisan make:controller PhotoController --resource
```

このコマンドは、`app/Http/Controllers/PhotoController.php` にコントローラを生成します。 コントローラには、すぐに使用可能なリソース操作ごとのメソッドが含まれています。 次に、コントローラを指すリソースルートを登録していきます。

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class);

この 1 つのルート宣言により、リソースに対するさまざまなアクションを処理するための複数のルートが作成されます。生成されたコントローラには、これらのアクションごとにスタブ化されたメソッドがすでに含まれています。`route:list` Artisan コマンドを実行すると、アプリケーションのルートの概要をいつでも簡単に確認できます。

配列を `resources` メソッドに渡すことで、一度に多くのリソースコントローラを登録することもできます。

    Route::resources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

<a name="actions-handled-by-resource-controller"></a>
#### リソースコントローラによって処理されるアクション

動詞 | URI | アクション | ルート名
----------|------------------------|--------------|---------------------
GET       | `/photos`              | index        | photos.index
GET       | `/photos/create`       | create       | photos.create
POST      | `/photos`              | store        | photos.store
GET       | `/photos/{photo}`      | show         | photos.show
GET       | `/photos/{photo}/edit` | edit         | photos.edit
PUT/PATCH | `/photos/{photo}`      | update       | photos.update
DELETE    | `/photos/{photo}`      | destroy      | photos.destroy

<a name="customizing-missing-model-behavior"></a>
#### モデルが存在しない場合の挙動のカスタマイズ

通常、暗黙的に結合されたリソースモデルが見つからない場合には、404 の HTTP レスポンスが生成されます。しかし、リソースルートを定義する際に `missing` メソッドを呼び出すことで、この挙動をカスタマイズすることができます。`missing` メソッドは、リソースのいずれかのルートに対して暗黙的に結合されたモデルが見つからない場合に呼び出されるクロージャを受け入れます。

    use App\Http\Controllers\PhotoController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::resource('photos', PhotoController::class)
            ->missing(function (Request $request) {
                return Redirect::route('photos.index');
            });

<a name="soft-deleted-models"></a>
#### ソフトデリートしたモデル

通常、暗黙的なモデル結合は [ソフトデリート](/docs/{{version}}/eloquent#soft-deleting) したモデルを取得せず、代わりに 404 HTTP レスポンスを返します。しかし、リソースルートを定義するときに `withTrashed` メソッドを呼び出すことで、ソフトデリート済みモデルの操作を許可するようにフレームワークに指示できます。

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->withTrashed();

引数なしで `withTrashed` を呼び出すと、`show`、`edit`、`update` リソースルートの論理的に削除されたモデルが許可されます。配列を `withTrashed` メソッドに渡すことで、これらのルートのサブセットを指定できます。

    Route::resource('photos', PhotoController::class)->withTrashed(['show']);

<a name="specifying-the-resource-model"></a>
#### リソースモデルの指定

[ルートモデル結合](/docs/{{version}}/routing#route-model-binding) を使用して、リソースコントローラのメソッドでモデルインスタンスをタイプヒントしたい場合は、コントローラ生成の際にオプションで `--model` を付加してください。

```shell
php artisan make:controller PhotoController --model=Photo --resource
```

<a name="generating-form-requests"></a>
#### フォームリクエストの生成

リソースコントローラを生成するときに `--requests` オプションを指定して、コントローラの保存や更新メソッド用の [フォームリクエストクラス](/docs/{{version}}/validation#form-request-validation) を生成するよな Artisan コマンドを活用すると便利です。

```shell
php artisan make:controller PhotoController --model=Photo --resource --requests
```

<a name="restful-partial-resource-routes"></a>
### 部分的なリソースルート

リソースルートを宣言するとき、初期設定でセットされている完全なアクションの代わりに、コントローラが処理する必要があるアクションのサブセットだけを指定することも可能です。

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->only([
        'index', 'show'
    ]);

    Route::resource('photos', PhotoController::class)->except([
        'create', 'store', 'update', 'destroy'
    ]);

<a name="api-resource-routes"></a>
#### API リソースルート

API によって使用されるリソースルートを宣言する際に、通常、`create` や `edit` などの HTML テンプレートを提供するルートを除外したいときがあります。その場合は、 `apiResource` メソッドを使用して、これら 2 つのルートを自動的に除外できます。

    use App\Http\Controllers\PhotoController;

    Route::apiResource('photos', PhotoController::class);

配列を `apiResources` メソッドに渡すことで、多くの API リソースコントローラを一度に登録できます。

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\PostController;

    Route::apiResources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

`create` または `edit` メソッドを含まない API リソースコントローラをすばやく生成するには、`make:controller` コマンドを実行するときに `--api` スイッチを使用してみてください。

```shell
php artisan make:controller PhotoController --api
```

<a name="restful-nested-resources"></a>
### ネストされたリソース

場合によっては、ネストされたリソースへのルートを定義する必要があるかもしれません。 たとえば、写真リソースには、写真に添付できる複数のコメントがある場合があります。 リソースコントローラをネストするには、ルート宣言で「 `.`（ドット）」表記を使用できます。

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class);

このルートは、次のような URI でアクセスできるネストされたリソースを登録することが可能です。

    /photos/{photo}/comments/{comment}

<a name="scoping-nested-resources"></a>
#### ネストされたリソースのスコープ設定

Laravel の [暗黙的なモデル結合](/docs/{{version}}/routing#implicit-model-binding-scoping) 機能は、リソースの解決がされた子モデルが親モデルに属していることを確認するように、ネストされた結合を自動的にスコープできます。ネストされたリソースを定義するときに `scoped` メソッドを使用すると、自動スコープを有効にしたり、子リソースを取得するフィールドを Laravel に指示できたりします。これを実現する方法の詳細については、[リソースルートのスコープ設定](#restful-scoping-resource-routes) に関するドキュメントを参照してください。

<a name="shallow-nesting"></a>
#### Shallow ネスト

多くの場合、子 ID はすでに一意の識別子であるため、URI 内に親 ID と子 ID の両方を含める必要は必ずしもありません。自動インクリメントの主キーなどの一意の識別子を使用して URI セグメント内のモデルを識別する場合は、「Shallow（浅い）ネスト」も活用できます。

    use App\Http\Controllers\CommentController;

    Route::resource('photos.comments', CommentController::class)->shallow();

このルート定義では、次のルートが定義されます。

動詞 | URI | アクション | ルート名
----------|------------------------------|--- -----------|---------------------
GET       | `/photos/{photo}/comments`        | index        | photos.comments.index
GET       | `/photos/{photo}/comments/create` | create       | photos.comments.create
POST      | `/photos/{photo}/comments`        | store        | photos.comments.store
GET       | `/comments/{comment}`             | show         | comments.show
GET       | `/comments/{comment}/edit`        | edit         | comments.edit
PUT/PATCH | `/comments/{comment}`             | update       | comments.update
DELETE    | `/comments/{comment}`             | destroy      | comments.destroy

<a name="restful-naming-resource-routes"></a>
### リソースルートの命名

すべてのリソースコントローラアクションには、初期設定でルート名が付いています。ただし、`names` 配列に「希望のルート名」を渡すことで、これらの名前をオーバーライドできます。

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->names([
        'create' => 'photos.build'
    ]);

<a name="restful-naming-resource-route-parameters"></a>
### リソースルートパラメータの命名

`Route::resource` はデフォルトで、リソース名の「単数形」バージョンに基づいてリソースルートのルートパラメータを作成します。`parameters` メソッドを使用すると、リソースごとに簡単にオーバーライドできます。`parameters` メソッドに渡される配列は、リソース名とパラメータ名の連想配列である必要があります。

    use App\Http\Controllers\AdminUserController;

    Route::resource('users', AdminUserController::class)->parameters([
        'users' => 'admin_user'
    ]);

  上の例では、リソースの `show` ルートに対して次の URI を生成します。

    /users/{admin_user}

<a name="restful-scoping-resource-routes"></a>
### リソースルートのスコープ

Laravel の [スコープ付き暗黙的なモデル結合](/docs/{{version}}/routing#implicit-model-binding-scoping) 機能は、解決された子モデルが親モデルに属していることが確認されるように、ネストされた結合を自動的にスコープできます。ネストされたリソースを定義するときに `scoped` メソッドを使用すると、自動スコープを有効にしたり、子リソースを取得するフィールドを Laravel に指示したりできます。

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class)->scoped([
        'comment' => 'slug',
    ]);

上記のような、スコープ付きのネストされたリソースが登録されている場合は、以下のような URI でアクセスできます。

    /photos/{photo}/comments/{comment:slug}

カスタムキー付きの暗黙的な結合を、ネストされたルートパラメータとして使用する場合、Laravel は、親のリレーション名を推測する規則を活用して、親によってネストされたモデルを取得するためにクエリのスコープを自動的に設定します。この場合、`Photo` モデルには、`Comment` モデルを取得するために使用できる `comments`（ルートパラメータ名の複数形）という名前のリレーションがあると想定されます。

<a name="restful-localizing-resource-uris"></a>
### リソース URI のローカライズ

デフォルトでは、`Route::resource` は英語の動詞と複数形の規則を使用してリソース URI を作成します。`create` および `edit` アクション動詞をローカライズする必要がある場合は、`Route::resourceVerbs` メソッドを使用できます。これは、アプリケーションの  `App\Providers\RouteServiceProvider` 内の `boot` メソッドの先頭で、以下のように記述することができます。

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        Route::resourceVerbs([
            'create' => 'crear',
            'edit' => 'editar',
        ]);

        // ...
    }

Laravel's pluralizer supports [several different languages which you may configure based on your needs](/docs/{{version}}/localization#pluralization-language). Once the verbs and pluralization language have been customized, a resource route registration such as `Route::resource('publicacion', PublicacionController::class)` will produce the following URIs:

    /publicacion/crear

    /publicacion/{publicaciones}/editar

<a name="restful-supplementing-resource-controllers"></a>
### Supplementing Resource Controllers

If you need to add additional routes to a resource controller beyond the default set of resource routes, you should define those routes before your call to the `Route::resource` method; otherwise, the routes defined by the `resource` method may unintentionally take precedence over your supplemental routes:

    use App\Http\Controller\PhotoController;

    Route::get('/photos/popular', [PhotoController::class, 'popular']);
    Route::resource('photos', PhotoController::class);

> **Note**  
> Remember to keep your controllers focused. If you find yourself routinely needing methods outside of the typical set of resource actions, consider splitting your controller into two, smaller controllers.

<a name="singleton-resource-controllers"></a>
### Singleton Resource Controllers

Sometimes, your application will have resources that may only have a single instance. For example, a user's "profile" can be edited or updated, but a user may not have more than one "profile". Likewise, an image may have a single "thumbnail". These resources are called "singleton resources", meaning one and only one instance of the resource may exist. In these scenarios, you may register a "singleton" resource controller:

```php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::singleton('profile', ProfileController::class);
```

The singleton resource definition above will register the following routes. As you can see, "creation" routes are not registered for singleton resources, and the registered routes do not accept an identifier since only one instance of the resource may exist:

Verb      | URI                               | Action       | Route Name
----------|-----------------------------------|--------------|---------------------
GET       | `/profile`                        | show         | profile.show
GET       | `/profile/edit`                   | edit         | profile.edit
PUT/PATCH | `/profile`                        | update       | profile.update

Singleton resources may also be nested within a standard resource:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class);
```

In this example, the `photos` resource would receive all of the [standard resource routes](#actions-handled-by-resource-controller); however, the `thumbnail` resource would be a singleton resource with the following routes:

| Verb      | URI                              | Action  | Route Name               |
|-----------|----------------------------------|---------|--------------------------|
| GET       | `/photos/{photo}/thumbnail`      | show    | photos.thumbnail.show    |
| GET       | `/photos/{photo}/thumbnail/edit` | edit    | photos.thumbnail.edit    |
| PUT/PATCH | `/photos/{photo}/thumbnail`      | update  | photos.thumbnail.update  |

<a name="creatable-singleton-resources"></a>
#### Creatable Singleton Resources

Occasionally, you may want to define creation and storage routes for a singleton resource. To accomplish this, you may invoke the `creatable` method when registering the singleton resource route:

```php
Route::singleton('photos.thumbnail', ThumbnailController::class)->creatable();
```

In this example, the following routes will be registered. As you can see, a `DELETE` route will also be registered for creatable singleton resources:

| Verb      | URI                                | Action  | Route Name               |
|-----------|------------------------------------|---------|--------------------------|
| GET       | `/photos/{photo}/thumbnail/create` | create  | photos.thumbnail.create  |
| POST      | `/photos/{photo}/thumbnail`        | store   | photos.thumbnail.store   |
| GET       | `/photos/{photo}/thumbnail`        | show    | photos.thumbnail.show    |
| GET       | `/photos/{photo}/thumbnail/edit`   | edit    | photos.thumbnail.edit    |
| PUT/PATCH | `/photos/{photo}/thumbnail`        | update  | photos.thumbnail.update  |
| DELETE    | `/photos/{photo}/thumbnail`        | destroy | photos.thumbnail.destroy |

If you would like Laravel to register the `DELETE` route for a singleton resource but not register the creation or storage routes, you may utilize the `destroyable` method:

```php
Route::singleton(...)->destroyable();
```

<a name="api-singleton-resources"></a>
#### API Singleton Resources

The `apiSingleton` method may be used to register a singleton resource that will be manipulated via an API, thus rendering the `create` and `edit` routes unnecessary:

```php
Route::apiSingleton('profile', ProfileController::class);
```

Of course, API singleton resources may also be `creatable`, which will register `store` and `destroy` routes for the resource:

```php
Route::apiSingleton('photos.thumbnail', ProfileController::class)->creatable();
```

<a name="dependency-injection-and-controllers"></a>
## Dependency Injection & Controllers

<a name="constructor-injection"></a>
#### Constructor Injection

The Laravel [service container](/docs/{{version}}/container) is used to resolve all Laravel controllers. As a result, you are able to type-hint any dependencies your controller may need in its constructor. The declared dependencies will automatically be resolved and injected into the controller instance:

    <?php

    namespace App\Http\Controllers;

    use App\Repositories\UserRepository;

    class UserController extends Controller
    {
        /**
         * Create a new controller instance.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}
    }

<a name="method-injection"></a>
#### Method Injection

In addition to constructor injection, you may also type-hint dependencies on your controller's methods. A common use-case for method injection is injecting the `Illuminate\Http\Request` instance into your controller methods:

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
            $name = $request->name;

            // Store the user...

            return redirect('/users');
        }
    }

If your controller method is also expecting input from a route parameter, list your route arguments after your other dependencies. For example, if your route is defined like so:

    use App\Http\Controllers\UserController;

    Route::put('/user/{id}', [UserController::class, 'update']);

You may still type-hint the `Illuminate\Http\Request` and access your `id` parameter by defining your controller method as follows:

    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class UserController extends Controller
    {
        /**
         * Update the given user.
         */
        public function update(Request $request, string $id): RedirectResponse
        {
            // Update the user...

            return redirect('/users');
        }
    }
