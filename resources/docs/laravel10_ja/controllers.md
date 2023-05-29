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
## Controller Middleware

[Middleware](/docs/{{version}}/middleware) may be assigned to the controller's routes in your route files:

    Route::get('profile', [UserController::class, 'show'])->middleware('auth');

Or, you may find it convenient to specify middleware within your controller's constructor. Using the `middleware` method within your controller's constructor, you can assign middleware to the controller's actions:

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

Controllers also allow you to register middleware using a closure. This provides a convenient way to define an inline middleware for a single controller without defining an entire middleware class:

    use Closure;
    use Illuminate\Http\Request;

    $this->middleware(function (Request $request, Closure $next) {
        return $next($request);
    });

<a name="resource-controllers"></a>
## Resource Controllers

If you think of each Eloquent model in your application as a "resource", it is typical to perform the same sets of actions against each resource in your application. For example, imagine your application contains a `Photo` model and a `Movie` model. It is likely that users can create, read, update, or delete these resources.

Because of this common use case, Laravel resource routing assigns the typical create, read, update, and delete ("CRUD") routes to a controller with a single line of code. To get started, we can use the `make:controller` Artisan command's `--resource` option to quickly create a controller to handle these actions:

```shell
php artisan make:controller PhotoController --resource
```

This command will generate a controller at `app/Http/Controllers/PhotoController.php`. The controller will contain a method for each of the available resource operations. Next, you may register a resource route that points to the controller:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class);

This single route declaration creates multiple routes to handle a variety of actions on the resource. The generated controller will already have methods stubbed for each of these actions. Remember, you can always get a quick overview of your application's routes by running the `route:list` Artisan command.

You may even register many resource controllers at once by passing an array to the `resources` method:

    Route::resources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

<a name="actions-handled-by-resource-controller"></a>
#### Actions Handled By Resource Controller

Verb      | URI                    | Action       | Route Name
----------|------------------------|--------------|---------------------
GET       | `/photos`              | index        | photos.index
GET       | `/photos/create`       | create       | photos.create
POST      | `/photos`              | store        | photos.store
GET       | `/photos/{photo}`      | show         | photos.show
GET       | `/photos/{photo}/edit` | edit         | photos.edit
PUT/PATCH | `/photos/{photo}`      | update       | photos.update
DELETE    | `/photos/{photo}`      | destroy      | photos.destroy

<a name="customizing-missing-model-behavior"></a>
#### Customizing Missing Model Behavior

Typically, a 404 HTTP response will be generated if an implicitly bound resource model is not found. However, you may customize this behavior by calling the `missing` method when defining your resource route. The `missing` method accepts a closure that will be invoked if an implicitly bound model can not be found for any of the resource's routes:

    use App\Http\Controllers\PhotoController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Redirect;

    Route::resource('photos', PhotoController::class)
            ->missing(function (Request $request) {
                return Redirect::route('photos.index');
            });

<a name="soft-deleted-models"></a>
#### Soft Deleted Models

Typically, implicit model binding will not retrieve models that have been [soft deleted](/docs/{{version}}/eloquent#soft-deleting), and will instead return a 404 HTTP response. However, you can instruct the framework to allow soft deleted models by invoking the `withTrashed` method when defining your resource route:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->withTrashed();

Calling `withTrashed` with no arguments will allow soft deleted models for the `show`, `edit`, and `update` resource routes. You may specify a subset of these routes by passing an array to the `withTrashed` method:

    Route::resource('photos', PhotoController::class)->withTrashed(['show']);

<a name="specifying-the-resource-model"></a>
#### Specifying The Resource Model

If you are using [route model binding](/docs/{{version}}/routing#route-model-binding) and would like the resource controller's methods to type-hint a model instance, you may use the `--model` option when generating the controller:

```shell
php artisan make:controller PhotoController --model=Photo --resource
```

<a name="generating-form-requests"></a>
#### Generating Form Requests

You may provide the `--requests` option when generating a resource controller to instruct Artisan to generate [form request classes](/docs/{{version}}/validation#form-request-validation) for the controller's storage and update methods:

```shell
php artisan make:controller PhotoController --model=Photo --resource --requests
```

<a name="restful-partial-resource-routes"></a>
### Partial Resource Routes

When declaring a resource route, you may specify a subset of actions the controller should handle instead of the full set of default actions:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->only([
        'index', 'show'
    ]);

    Route::resource('photos', PhotoController::class)->except([
        'create', 'store', 'update', 'destroy'
    ]);

<a name="api-resource-routes"></a>
#### API Resource Routes

When declaring resource routes that will be consumed by APIs, you will commonly want to exclude routes that present HTML templates such as `create` and `edit`. For convenience, you may use the `apiResource` method to automatically exclude these two routes:

    use App\Http\Controllers\PhotoController;

    Route::apiResource('photos', PhotoController::class);

You may register many API resource controllers at once by passing an array to the `apiResources` method:

    use App\Http\Controllers\PhotoController;
    use App\Http\Controllers\PostController;

    Route::apiResources([
        'photos' => PhotoController::class,
        'posts' => PostController::class,
    ]);

To quickly generate an API resource controller that does not include the `create` or `edit` methods, use the `--api` switch when executing the `make:controller` command:

```shell
php artisan make:controller PhotoController --api
```

<a name="restful-nested-resources"></a>
### Nested Resources

Sometimes you may need to define routes to a nested resource. For example, a photo resource may have multiple comments that may be attached to the photo. To nest the resource controllers, you may use "dot" notation in your route declaration:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class);

This route will register a nested resource that may be accessed with URIs like the following:

    /photos/{photo}/comments/{comment}

<a name="scoping-nested-resources"></a>
#### Scoping Nested Resources

Laravel's [implicit model binding](/docs/{{version}}/routing#implicit-model-binding-scoping) feature can automatically scope nested bindings such that the resolved child model is confirmed to belong to the parent model. By using the `scoped` method when defining your nested resource, you may enable automatic scoping as well as instruct Laravel which field the child resource should be retrieved by. For more information on how to accomplish this, please see the documentation on [scoping resource routes](#restful-scoping-resource-routes).

<a name="shallow-nesting"></a>
#### Shallow Nesting

Often, it is not entirely necessary to have both the parent and the child IDs within a URI since the child ID is already a unique identifier. When using unique identifiers such as auto-incrementing primary keys to identify your models in URI segments, you may choose to use "shallow nesting":

    use App\Http\Controllers\CommentController;

    Route::resource('photos.comments', CommentController::class)->shallow();

This route definition will define the following routes:

Verb      | URI                               | Action       | Route Name
----------|-----------------------------------|--------------|---------------------
GET       | `/photos/{photo}/comments`        | index        | photos.comments.index
GET       | `/photos/{photo}/comments/create` | create       | photos.comments.create
POST      | `/photos/{photo}/comments`        | store        | photos.comments.store
GET       | `/comments/{comment}`             | show         | comments.show
GET       | `/comments/{comment}/edit`        | edit         | comments.edit
PUT/PATCH | `/comments/{comment}`             | update       | comments.update
DELETE    | `/comments/{comment}`             | destroy      | comments.destroy

<a name="restful-naming-resource-routes"></a>
### Naming Resource Routes

By default, all resource controller actions have a route name; however, you can override these names by passing a `names` array with your desired route names:

    use App\Http\Controllers\PhotoController;

    Route::resource('photos', PhotoController::class)->names([
        'create' => 'photos.build'
    ]);

<a name="restful-naming-resource-route-parameters"></a>
### Naming Resource Route Parameters

By default, `Route::resource` will create the route parameters for your resource routes based on the "singularized" version of the resource name. You can easily override this on a per resource basis using the `parameters` method. The array passed into the `parameters` method should be an associative array of resource names and parameter names:

    use App\Http\Controllers\AdminUserController;

    Route::resource('users', AdminUserController::class)->parameters([
        'users' => 'admin_user'
    ]);

 The example above generates the following URI for the resource's `show` route:

    /users/{admin_user}

<a name="restful-scoping-resource-routes"></a>
### Scoping Resource Routes

Laravel's [scoped implicit model binding](/docs/{{version}}/routing#implicit-model-binding-scoping) feature can automatically scope nested bindings such that the resolved child model is confirmed to belong to the parent model. By using the `scoped` method when defining your nested resource, you may enable automatic scoping as well as instruct Laravel which field the child resource should be retrieved by:

    use App\Http\Controllers\PhotoCommentController;

    Route::resource('photos.comments', PhotoCommentController::class)->scoped([
        'comment' => 'slug',
    ]);

This route will register a scoped nested resource that may be accessed with URIs like the following:

    /photos/{photo}/comments/{comment:slug}

When using a custom keyed implicit binding as a nested route parameter, Laravel will automatically scope the query to retrieve the nested model by its parent using conventions to guess the relationship name on the parent. In this case, it will be assumed that the `Photo` model has a relationship named `comments` (the plural of the route parameter name) which can be used to retrieve the `Comment` model.

<a name="restful-localizing-resource-uris"></a>
### Localizing Resource URIs

By default, `Route::resource` will create resource URIs using English verbs and plural rules. If you need to localize the `create` and `edit` action verbs, you may use the `Route::resourceVerbs` method. This may be done at the beginning of the `boot` method within your application's `App\Providers\RouteServiceProvider`:

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
