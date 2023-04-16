# ディレクトリ構造

- [はじめに](#introduction)
- [プロジェクトルートディレクトリ](#the-root-directory)
     - [`app` ディレクトリ](#the-root-app-directory)
     - [`bootstrap` ディレクトリ](#the-bootstrap-directory)
     - [`config` ディレクトリ](#the-config-directory)
     - [`database` ディレクトリ](#the-database-directory)
     - [`public` ディレクトリ](#the-public-directory)
     - [`resources` ディレクトリ](#the-resources-directory)
     - [`routes` ディレクトリ](#the-routes-directory)
     - [`storage` ディレクトリ](#the-storage-directory)
     - [`tests` ディレクトリ](#the-tests-directory)
     - [`vendor` ディレクトリ](#the-vendor-directory)
- [App ディレクトリ](#the-app-directory)
     - [`Broadcasting` ディレクトリ](#the-broadcasting-directory)
     - [`Console` ディレクトリ](#the-console-directory)
     - [`Events` ディレクトリ](#the-events-directory)
     - [`Exceptions` ディレクトリ](#the-exceptions-directory)
     - [`Http` ディレクトリ](#the-http-directory)
     - [`Jobs` ディレクトリ](#the-jobs-directory)
     - [`Listeners` ディレクトリ](#the-listeners-directory)
     - [`Mail` ディレクトリ](#the-mail-directory)
     - [`Models` ディレクトリ](#the-models-directory)
     - [`Notifications` ディレクトリ](#the-notifications-directory)
     - [`Policies` ディレクトリ](#the-policies-directory)
     - [`Providers` ディレクトリ](#the-providers-directory)
     - [`Rules` ディレクトリ](#the-rules-directory)

<a name="introduction"></a>
＃＃ はじめに

デフォルトの Laravel アプリケーション構造は、大規模・小規模なアプリケーションのどちらにも適した優れた出発点を提供することを目的としています。しかし、アプリケーションの構成は自由に変更して構いません。Laravel は、Composer がクラスを自動読み込みできる限り、特定のクラスがどこにあるかにほとんど制限を設けていません。

> **Note**
> Laravel は初めてですか？[Laravel Bootcamp](https://bootcamp.laravel.com) でフレームワークを実際に使いながら最初の Laravel アプリケーションを作成するプロセスを学びましょう

<a name="the-root-directory"></a>
## プロジェクトルートディレクトリ

<a name="the-root-app-directory"></a>
#### app ディレクトリ

`app` ディレクトリには、アプリケーションのコアコードが含まれています。このディレクトリは後ほど詳しく説明しますが、アプリケーションのほぼすべてのクラスがこのディレクトリに存在します。

<a name="the-bootstrap-directory"></a>
#### bootstrap ディレクトリ

`bootstrap` ディレクトリには、フレームワークをブートストラップする `app.php` ファイルが含まれています。このディレクトリには、ルートキャッシュやサービスキャッシュファイルなどのパフォーマンス最適化のためのフレームワーク生成ファイルを格納する `cache` ディレクトリも含まれています。通常、このディレクトリ内のファイルを変更する必要はありません。

<a name="the-config-directory"></a>
#### config ディレクトリ

「config」ディレクトリには、その名前が示すようにアプリケーションの構成ファイルがすべて含まれています。これらのファイルをすべて読み込んで、利用可能なオプションを把握することが良いでしょう。

<a name="the-database-directory"></a>
#### database ディレクトリ

`database` データベースのマイグレーション、モデルファクトリ、およびシードが含まれています。必要に応じて、このディレクトリに `SQLite` データベースを格納することもできます。

<a name="the-public-directory"></a>
#### public ディレクトリ

`public` ディレクトリには `index.php` ファイルが含まれており、アプリケーションに入るすべてのリクエストのエントリーポイントであり、オートロードを設定します。このディレクトリには、画像、JavaScript、CSSなどのアセットも格納されています。

<a name="the-resources-directory"></a>
#### resources ディレクトリ

`resources` ディレクトリには、[views](/laravel10_ja/views) と、CSS や JavaScript などの未コンパイルのアセットが含まれています。

<a name="the-routes-directory"></a>
#### routes ディレクトリ

`routes` ディレクトリには、アプリケーションのすべてのルート定義が含まれています。 デフォルトで複数のルートファイルが含まれています。（`web.php`、`api.php`、`console.php`、`channels.php`）

`web.php` ファイルには、`RouteServiceProvider` が `web` ミドルウェアグループに配置するルートが含まれており、セッション状態、CSRF 保護、および Cookie 暗号化が提供されます。 アプリケーションがステートレスな RESTful API を提供しない場合、すべてのルートはおそらく `web.php` ファイルに定義されるでしょう。

`api.php` ファイルには、`RouteServiceProvider` が `api` ミドルウェアグループに配置するルートが含まれています。これらのルートはステートレスを意図しており、これらのルートを通じてアプリケーションに入るリクエストは、[トークン経由](/laravel10_ja/sanctum) で認証され、セッション状態にアクセスできません。

`console.php` ファイルは、クロージャベースのコンソールコマンドをすべて定義できます。各クロージャはコマンドインスタンスにバインドされており、各コマンドの IO メソッドとのやり取りを簡単にするアプローチが提供されます。このファイルは HTTP ルートを定義していませんが、アプリケーションへのコンソールベースのエントリーポイント（ルート）を定義しています。

`channels.php` ファイルは、アプリケーションがサポートするすべての [イベントブロードキャスト](/laravel10_ja/broadcasting) チャネルを登録できる場所です。

<a name="the-storage-directory"></a>
#### storage ディレクトリ

`storage` ディレクトリには、ログ、コンパイルされた Blade テンプレート、ファイル ベースのセッション、ファイル キャッシュ、およびフレームワークによって生成されたその他のファイルが含まれます。 このディレクトリは、「app」、「framework」、および「logs」ディレクトリに分離されています。 `app` ディレクトリは、アプリケーションによって生成されたファイルを保存するために使用できます。 `framework` ディレクトリは、フレームワークで生成されたファイルとキャッシュを格納するために使用されます。 最後に、`logs` ディレクトリにはアプリケーションのログ ファイルが含まれます。

`storage/app/public` ディレクトリは、ユーザーが生成したファイル (プロファイル アバターなど) を格納するために使用できます。 このディレクトリを指す `public/storage` にシンボリック リンクを作成する必要があります。 `php artisan storage:link` Artisan コマンドを使用してリンクを作成できます。

<a name="the-tests-directory"></a>
#### テスト ディレクトリ

`tests` ディレクトリには、自動化されたテストが含まれています。 例 [PHPUnit](https://phpunit.de/) 単体テストと機能テストは、すぐに使用できます。 各テスト クラスには、`Test` という単語を接尾辞として付ける必要があります。 `phpunit` または `php vendor/bin/phpunit` コマンドを使用してテストを実行できます。 または、テスト結果のより詳細で美しい表現が必要な場合は、`php artisan test` Artisan コマンドを使用してテストを実行できます。

<a name="the-vendor-directory"></a>
#### ベンダー ディレクトリ

`vendor` ディレクトリには、[Composer](https://getcomposer.org) の依存関係が含まれています。

<a name="the-app-directory"></a>
## App ディレクトリ

アプリケーションの大部分は、`app` ディレクトリに格納されています。 デフォルトでは、このディレクトリは「App」の下に名前空間があり、[PSR-4 自動ロード標準](https://www.php-fig.org/psr/psr-4/) を使用して Composer によって自動ロードされます。

`app` ディレクトリには、`Console`、`Http`、`Providers` などのさまざまな追加ディレクトリが含まれています。 `Console` および `Http` ディレクトリは、アプリケーションのコアに API を提供するものと考えてください。 HTTP プロトコルと CLI はどちらもアプリケーションとやり取りするためのメカニズムですが、実際にはアプリケーション ロジックは含まれていません。 つまり、これらはアプリケーションにコマンドを発行する 2 つの方法です。 `Console` ディレクトリにはすべての Artisan コマンドが含まれ、`Http` ディレクトリにはコントローラ、ミドルウェア、およびリクエストが含まれます。

`make` Artisan コマンドを使用してクラスを生成すると、`app` ディレクトリ内に他のさまざまなディレクトリが生成されます。 したがって、たとえば、`make:job` Artisan コマンドを実行してジョブ クラスを生成するまで、`app/Jobs` ディレクトリは存在しません。

> **注**
> `app` ディレクトリ内のクラスの多くは、Artisan によってコマンドを介して生成できます。 利用可能なコマンドを確認するには、ターミナルで「php artisan list make」コマンドを実行します。

<a name="the-broadcasting-directory"></a>
#### 放送ディレクトリ

`Broadcasting` ディレクトリには、アプリケーションのブロードキャスト チャネル クラスがすべて含まれています。 これらのクラスは、`make:channel` コマンドを使用して生成されます。 このディレクトリはデフォルトでは存在しませんが、最初のチャネルを作成するときに作成されます。 チャネルの詳細については、[イベント ブロードキャスト](/docs/{{version}}/broadcasting) のドキュメントをご覧ください。

<a name="the-console-directory"></a>
#### コンソール ディレクトリ

`Console` ディレクトリには、アプリケーション用のすべてのカスタム Artisan コマンドが含まれています。 これらのコマンドは、`make:command` コマンドを使用して生成できます。 このディレクトリには、カスタム Artisan コマンドが登録され、[スケジュールされたタスク](/docs/{{version}}/scheduling) が定義されるコンソール カーネルも格納されます。

<a name="the-events-directory"></a>
#### イベント ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`event:generate` および `make:event` Artisan コマンドによって作成されます。 `Events` ディレクトリには、[イベント クラス](/docs/{{version}}/events) が格納されています。 イベントを使用して、特定のアクションが発生したことをアプリケーションの他の部分に警告し、柔軟性と分離を大幅に向上させることができます。

<a name="the-exceptions-directory"></a>
#### 例外ディレクトリ

`Exceptions` ディレクトリには、アプリケーションの例外ハンドラが含まれており、アプリケーションによってスローされた例外を配置するのにも適しています。 例外のログまたはレンダリング方法をカスタマイズしたい場合は、このディレクトリの `Handler` クラスを変更する必要があります。

<a name="the-http-directory"></a>
#### HTTP ディレクトリ

`Http` ディレクトリには、コントローラ、ミドルウェア、およびフォーム リクエストが含まれています。 アプリケーションに入るリクエストを処理するほとんどすべてのロジックは、このディレクトリに配置されます。

<a name="the-jobs-directory"></a>
#### ジョブ ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`make:job` Artisan コマンドを実行すると作成されます。 `Jobs` ディレクトリには、アプリケーションの [キュー可能なジョブ](/docs/{{version}}/queues) が格納されています。 ジョブは、アプリケーションによってキューに入れられるか、現在のリクエスト ライフサイクル内で同期的に実行されます。 現在のリクエスト中に同期的に実行されるジョブは、[コマンド パターン](https://en.wikipedia.org/wiki/Command_pattern) の実装であるため、「コマンド」と呼ばれることがあります。

<a name="the-listeners-directory"></a>
#### リスナー ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`event:generate` または `make:listener` Artisan コマンドを実行すると作成されます。 `Listeners` ディレクトリには、[イベント](/docs/{{version}}/events) を処理するクラスが含まれています。 イベント リスナーは、イベント インスタンスを受け取り、発生したイベントに応答してロジックを実行します。 たとえば、「UserRegistered」イベントは「SendWelcomeEmail」リスナーによって処理される場合があります。

<a name="the-mail-directory"></a>
#### メールディレクトリ

このディレクトリはデフォルトでは存在しませんが、`make:mail` Artisan コマンドを実行すると作成されます。 `Mail` ディレクトリには、アプリケーションから送信された [メールを表すクラス](/docs/{{version}}/mail) がすべて含まれています。 Mail オブジェクトを使用すると、`Mail::send` メソッドを使用して送信できる単一の単純なクラスにメールを作成するすべてのロジックをカプセル化できます。

<a name="the-models-directory"></a>
#### モデル ディレクトリ

`Models` ディレクトリには、すべての [Eloquent モデル クラス](/docs/{{version}}/eloquent) が含まれています。 Laravel に含まれる Eloquent ORM は、データベースを操作するための美しくシンプルな ActiveRecord 実装を提供します。 各データベース テーブルには、そのテーブルとの対話に使用される対応する「モデル」があります。 モデルを使用すると、テーブル内のデータをクエリしたり、新しいレコードをテーブルに挿入したりできます。

<a name="the-notifications-directory"></a>
#### 通知ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`make:notification` Artisan コマンドを実行すると作成されます。 `Notifications` ディレクトリには、アプリケーション内で発生するイベントに関する単純な通知など、アプリケーションによって送信されるすべての「トランザクション」[通知](/docs/{{version}}/notifications) が含まれます。 Laravel の通知機能は、電子メール、Slack、SMS、またはデータベースに保存されたさまざまなドライバーを介して通知を送信することを抽象化します。

<a name="the-policies-directory"></a>
#### ポリシー ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`make:policy` Artisan コマンドを実行すると作成されます。 `Policies` ディレクトリには、アプリケーションの [認可ポリシー クラス](/docs/{{version}}/authorization) が含まれています。 ポリシーは、ユーザーがリソースに対して特定のアクションを実行できるかどうかを判断するために使用されます。

<a name="the-providers-directory"></a>
#### プロバイダー ディレクトリ

`Providers` ディレクトリには、アプリケーションのすべての [サービス プロバイダ](/docs/{{version}}/providers) が含まれています。 サービス プロバイダーは、サービス コンテナーでサービスをバインドしたり、イベントを登録したり、その他のタスクを実行したりして、アプリケーションを受信要求に備えて準備することにより、アプリケーションをブートストラップします。

新しい Laravel アプリケーションでは、このディレクトリにはすでにいくつかのプロバイダーが含まれています。 必要に応じて、独自のプロバイダーをこのディレクトリに自由に追加できます。

<a name="the-rules-directory"></a>
#### ルール ディレクトリ

このディレクトリはデフォルトでは存在しませんが、`make:rule` Artisan コマンドを実行すると作成されます。 `Rules` ディレクトリには、アプリケーションのカスタム検証ルール オブジェクトが含まれています。 ルールは、複雑な検証ロジックを単純なオブジェクトにカプセル化するために使用されます。 詳細については、[検証ドキュメント](/docs/{{version}}/validation)をご覧ください。
