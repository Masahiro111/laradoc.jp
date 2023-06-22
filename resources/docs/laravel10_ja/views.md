# ビュー

- [はじめに](#introduction)
    - [React / Vue でのビューの記述](#writing-views-in-react-or-vue)
- [ビューの作成とレンダリング](#creating-and-rendering-views)
    - [ネストしたビューディレクトリ](#nested-view-directories)
    - [最初に利用できるビュー](#creating-the-first-available-view)
    - [ビューの存在を判定](#determining-if-a-view-exists)
- [ビューにデータを渡す](#passing-data-to-views)
    - [すべてのビューでデータを共有](#sharing-data-with-all-views)
- [ビューコンポーザ](#view-composers)
    - [ビュークリエイタ](#view-creators)
- [ビューの最適化](#optimizing-views)

<a name="introduction"></a>
## はじめに

HTML 文書の文字列全体をルートやコントローラから直接返してしまうのは現実的ではありません。ビューでは、すべての HTML を別々のファイルとして配置できるような便利な方法を提供しています。

ビューは、コントローラ / アプリケーションロジックをプレゼンテーションロジックから分離し、`resources/views` ディレクトリに保存されます。Laravel を使用する場合、ビューテンプレートは通常 [Blade テンプレート言語](/docs/{{version}}/blade) を使用して記述されます。単純なビューは次のようになります。

```blade
<!-- View stored in resources/views/greeting.blade.php -->

<html>
    <body>
        <h1>Hello, {{ $name }}</h1>
    </body>
</html>
```

このビューは `resources/views/greeting.blade.php` に保存したら、次のようにグローバル `view` ヘルパを使用して結果を返します。

    Route::get('/', function () {
        return view('greeting', ['name' => 'James']);
    });

> **Note**  
> Blade テンプレートの作成方法に関する詳細情報は [Blade ドキュメント](/docs/{{version}}/blade) を確認してください。

<a name="writing-views-in-react-or-vue"></a>
### React / Vue でのビューの作成

多くの開発者は、Blade を介して PHP でフロントエンドのテンプレートを作成する代わりに、React または Vue を使用してテンプレートを作成することを好み始めています。Laravel では、[Inertia](https://inertiajs.com/) のおかげで、これを簡単に行うことができます。これは、SPA の構築によくある複雑な作業を行わずに、React / Vue のフロントエンドを Laravel のバックエンドに簡単に接続できるようにするライブラリです。

Breeze と Jetstream の [スターターキット](/docs/{{version}}/starter-kits) は、Inertia を利用した次の Laravel アプリケーションの優れた出発点となります。さらに、[Laravel Bootcamp](https://bootcamp.laravel.com) では、Vue や React の例を含む、Inertia を利用した Laravel アプリケーションの構築に関する完全なデモンストレーションが提供されています。

<a name="creating-and-rendering-views"></a>
## ビューの作成とレンダリング

アプリケーションの `resources/views` ディレクトリに `.blade.php` 拡張子を持つファイルを配置することで、ビューを作成できます。`.blade.php` 拡張子は、ファイルに [Blade テンプレート](/docs/{{version}}/blade) が含まれていることをフレームワークに通知します。Blade テンプレートには、HTML と Blade ディレクティブが含まれており、値のエコー、「if」文の作成、データの反復処理などを簡単に行うことができます。

ビューを作成したら、グローバル `view` ヘルパを使用して、アプリケーションのルートまたはコントローラからビューを返すことができます。

    Route::get('/', function () {
        return view('greeting', ['name' => 'James']);
    });

ビューは、`View` ファサードを使用して返すこともできます。

    use Illuminate\Support\Facades\View;

    return View::make('greeting', ['name' => 'James']);

ご覧のとおり、`view` ヘルパに渡される第１引数は、`resources/views` ディレクトリ内のビューファイルの名前に対応します。第２引数は、ビューで使用できるようにするデータの配列です。上記では、[Blade 構文](/docs/{{version}}/blade) を使用してビューに表示する、`name` 変数を渡しています。

<a name="nested-view-directories"></a>
### ネストされたビューのディレクトリ

ビューは、`resources/views` ディレクトリのサブディレクトリ内にネストすることもできます。「ドット」表記は、ネストされたビューを参照するために使用できます。たとえば、ビューが `resources/views/admin/profile.blade.php` に保存されている場合、次のようにアプリケーションのルート / コントローラからビューを返すことができます。

    return view('admin.profile', $data);

> **Warning**  
> ビューのディレクトリ名には `.` 文字を含めないでください。

<a name="creating-the-first-available-view"></a>
### 最初に利用できるビュー

`View` ファサードの`first` メソッドを使用すると、指定されたビューの配列に存在する最初のビューを作成できます。これは、アプリケーションまたはパッケージでビューのカスタマイズまたは上書きが許可されている場合に便利です。

    use Illuminate\Support\Facades\View;

    return View::first(['custom.admin', 'admin'], $data);

<a name="determining-if-a-view-exists"></a>
### ビューの存在を判定

ビューの存在を確認する必要がある場合、`View` ファサードの `exists` メソッドを使用します。ビューが存在する場合は、`true` を返します。

    use Illuminate\Support\Facades\View;

    if (View::exists('emails.customer')) {
        // ...
    }

<a name="passing-data-to-views"></a>
## Passing Data To Views

As you saw in the previous examples, you may pass an array of data to views to make that data available to the view:

    return view('greetings', ['name' => 'Victoria']);

When passing information in this manner, the data should be an array with key / value pairs. After providing data to a view, you can then access each value within your view using the data's keys, such as `<?php echo $name; ?>`.

As an alternative to passing a complete array of data to the `view` helper function, you may use the `with` method to add individual pieces of data to the view. The `with` method returns an instance of the view object so that you can continue chaining methods before returning the view:

    return view('greeting')
                ->with('name', 'Victoria')
                ->with('occupation', 'Astronaut');

<a name="sharing-data-with-all-views"></a>
### Sharing Data With All Views

Occasionally, you may need to share data with all views that are rendered by your application. You may do so using the `View` facade's `share` method. Typically, you should place calls to the `share` method within a service provider's `boot` method. You are free to add them to the `App\Providers\AppServiceProvider` class or generate a separate service provider to house them:

    <?php

    namespace App\Providers;

    use Illuminate\Support\Facades\View;

    class AppServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            // ...
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            View::share('key', 'value');
        }
    }

<a name="view-composers"></a>
## View Composers

View composers are callbacks or class methods that are called when a view is rendered. If you have data that you want to be bound to a view each time that view is rendered, a view composer can help you organize that logic into a single location. View composers may prove particularly useful if the same view is returned by multiple routes or controllers within your application and always needs a particular piece of data.

Typically, view composers will be registered within one of your application's [service providers](/docs/{{version}}/providers). In this example, we'll assume that we have created a new `App\Providers\ViewServiceProvider` to house this logic.

We'll use the `View` facade's `composer` method to register the view composer. Laravel does not include a default directory for class based view composers, so you are free to organize them however you wish. For example, you could create an `app/View/Composers` directory to house all of your application's view composers:

    <?php

    namespace App\Providers;

    use App\View\Composers\ProfileComposer;
    use Illuminate\Support\Facades;
    use Illuminate\Support\ServiceProvider;
    use Illuminate\View\View;

    class ViewServiceProvider extends ServiceProvider
    {
        /**
         * Register any application services.
         */
        public function register(): void
        {
            // ...
        }

        /**
         * Bootstrap any application services.
         */
        public function boot(): void
        {
            // Using class based composers...
            Facades\View::composer('profile', ProfileComposer::class);

            // Using closure based composers...
            Facades\View::composer('welcome', function (View $view) {
                // ...
            });

            Facades\View::composer('dashboard', function (View $view) {
                // ...
            });
        }
    }

> **Warning**  
> Remember, if you create a new service provider to contain your view composer registrations, you will need to add the service provider to the `providers` array in the `config/app.php` configuration file.

Now that we have registered the composer, the `compose` method of the `App\View\Composers\ProfileComposer` class will be executed each time the `profile` view is being rendered. Let's take a look at an example of the composer class:

    <?php

    namespace App\View\Composers;

    use App\Repositories\UserRepository;
    use Illuminate\View\View;

    class ProfileComposer
    {
        /**
         * Create a new profile composer.
         */
        public function __construct(
            protected UserRepository $users,
        ) {}

        /**
         * Bind data to the view.
         */
        public function compose(View $view): void
        {
            $view->with('count', $this->users->count());
        }
    }

As you can see, all view composers are resolved via the [service container](/docs/{{version}}/container), so you may type-hint any dependencies you need within a composer's constructor.

<a name="attaching-a-composer-to-multiple-views"></a>
#### Attaching A Composer To Multiple Views

You may attach a view composer to multiple views at once by passing an array of views as the first argument to the `composer` method:

    use App\Views\Composers\MultiComposer;
    use Illuminate\Support\Facades\View;

    View::composer(
        ['profile', 'dashboard'],
        MultiComposer::class
    );

The `composer` method also accepts the `*` character as a wildcard, allowing you to attach a composer to all views:

    use Illuminate\Support\Facades;
    use Illuminate\View\View;

    Facades\View::composer('*', function (View $view) {
        // ...
    });

<a name="view-creators"></a>
### View Creators

View "creators" are very similar to view composers; however, they are executed immediately after the view is instantiated instead of waiting until the view is about to render. To register a view creator, use the `creator` method:

    use App\View\Creators\ProfileCreator;
    use Illuminate\Support\Facades\View;

    View::creator('profile', ProfileCreator::class);

<a name="optimizing-views"></a>
## Optimizing Views

By default, Blade template views are compiled on demand. When a request is executed that renders a view, Laravel will determine if a compiled version of the view exists. If the file exists, Laravel will then determine if the uncompiled view has been modified more recently than the compiled view. If the compiled view either does not exist, or the uncompiled view has been modified, Laravel will recompile the view.

Compiling views during the request may have a small negative impact on performance, so Laravel provides the `view:cache` Artisan command to precompile all of the views utilized by your application. For increased performance, you may wish to run this command as part of your deployment process:

```shell
php artisan view:cache
```

You may use the `view:clear` command to clear the view cache:

```shell
php artisan view:clear
```
