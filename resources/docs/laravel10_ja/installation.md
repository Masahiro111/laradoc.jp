# インストール

- [Laravel の紹介](#meet-laravel)
    - [なぜ Laravel なのか？](#why-laravel)
- [初めての Laravel プロジェクト](#your-first-laravel-project)
- [Laravel & Docker](#laravel-and-docker)
    - [macOS で始める](#getting-started-on-macos)
    - [Windowsで始める](#getting-started-on-windows)
    - [Linux で始める](#getting-started-on-linux)
    - [Sail サービスを選択する](#choosing-your-sail-services)
- [初期設定](#initial-configuration)
    - [環境ベースの初期設定](#environment-based-configuration)
    - [データベースとマイグレーション](#databases-and-migrations)
- [次のステップへ](#next-steps)
    - [Laravel フルスタックフレームワーク](#laravel-the-fullstack-framework)
    - [Laravel API バックエンド](#laravel-the-api-backend)

<a name="meet-laravel"></a>
## Laravel の紹介

Laravelは、表現力豊かでエレガントなシンタックスを持つWebアプリケーションフレームワークです。Webフレームワークは、アプリケーションを作成するための構造と出発点を提供し、私たちが詳細に汗を流す間、あなたは素晴らしいものを作ることに集中することができます

Laravelは、徹底した依存性注入、表現力豊かなデータベース抽象化レイヤー、キューとスケジュールされたジョブ、ユニットテストと統合テストなどの強力な機能を提供しながら、素晴らしい開発者体験を提供しようと努力しています。

PHPのWebフレームワークが初めての方でも、長年の経験をお持ちの方でも、Laravelはあなたとともに成長できるフレームワークです。私たちは、あなたがウェブデベロッパーとしての第一歩を踏み出すお手伝いをし、また、あなたの専門知識を次のレベルに引き上げるための後押しをします。あなたがどんなアプリを作るのか待ちきれません。

> **Note**
> Laravelは初めてですか？[Laravel Bootcamp](https://bootcamp.laravel.com)では、フレームワークのハンズオンツアーを行いながら、初めてのLaravelアプリケーションを構築する方法をご紹介します。

<a name="why-laravel"></a>
### なぜ Laravel なのか？

Webアプリケーションを構築する際には、様々なツールやフレームワークがあります。しかし、LaravelはモダンなフルスタックWebアプリケーションを構築するための最良の選択であると信じています。

#### プログレッシブフレームワーク

私たちは、Laravelを「プログレッシブ」フレームワークと呼びたいと考えています。これは、Laravelがあなたと共に成長することを意味します。もしあなたがウェブ開発の第一歩を踏み出したばかりなら、Laravelの膨大なドキュメント、ガイド、[ビデオチュートリアル](https://laracasts.com) のライブラリは、あなたが圧倒されることなくノウハウを学ぶのを助けてくれるでしょう。

上級開発者であれば、Laravelは[依存性注入](/docs/laravel10_ja/container)、[ユニットテスト](/docs/laravel10_ja/testing)、[キュー](/docs/laravel10_ja/queues)、[リアルタイムイベント](/docs/laravel10_ja/broadcasting) など、強固なツールを提供しています。Laravelは、プロフェッショナルなWebアプリケーションを構築するために細かく調整されており、企業の作業負荷を処理する準備ができています。

#### スケーラブルなフレームワーク

Laravel は信じられないほどスケーラブルです。PHPのスケーリングに適した性質と、Redis のような高速な分散キャッシュシステムをビルトインでサポートしています。Laravel を使って簡単に水平スケーリングを行えるでしょう。実際、Laravel のアプリケーションは、1ヶ月あたり数億のリクエストを処理するように簡単にスケーリングされています。

大規模なスケーリングを必要としていますか？[Laravel Vapor](https://vapor.laravel.com) のようなプラットフォームでは、AWS の最新のサーバーレステクノロジー上で Laravel アプリケーションをほぼ無制限のスケールで実行することができます。

#### コミュニティの枠組み

Laravelは、PHPのエコシステムにおける最高のパッケージを組み合わせ、最も堅牢で開発者に優しいフレームワークを提供しています。さらに、世界中の何千人もの才能ある開発者が [フレームワークに貢献しています](https://github.com/laravel/framework) 。もしかしたら、あなたもLaravelのコントリビューターになれるかもしれませんよ。

<a name="your-first-laravel-project"></a>
## 初めてのLaravelプロジェクト

最初のLaravelプロジェクトを作成する前に、ローカルマシンにPHPと[Composer](https://getcomposer.org)がインストールされていることを確認する必要があります。macOSで開発している場合、PHPとComposerは[Homebrew](https://brew.sh/)を介してインストールすることができます。また、[NodeとNPMのインストール](https://nodejs.org)もお勧めします。

PHPとComposerのインストールが完了したら、Composerの`create-project`コマンドで新しいLaravelプロジェクトを作成することができます

```nothing
composer create-project laravel/laravel example-app
```

または、Composer経由でLaravelインストーラーをグローバルにインストールして、新しいLaravelプロジェクトを作成することもできます：

```nothing
composer global require laravel/installer

laravel new example-app
```

プロジェクト作成後、LaravelのArtisan CLI `serve`コマンドを使用して、Laravelのローカル開発サーバーを起動します。

```nothing
cd example-app

php artisan serve
```

Artisan開発サーバーを起動すると、アプリケーションはWebブラウザで`http://localhost:8000`にアクセスできるようになります。次に、[Laravelエコシステムへの次のステップを開始する](#next-steps)準備が整いました。もちろん、[データベースの設定](#databases-and-migrations)も必要でしょう。

> **Note**  
> Laravel アプリケーションの開発を先取りしたい場合は、[スターターキット](docs/laravel10_ja/starter-kits)を使用することができます。Laravel スターターキットは、あなたの新しいLaravelアプリケーションのために、バックエンドとフロントエンドの認証スカフォールディングを提供します。

<a name="laravel-and-docker"></a>
## Laravel & Docker

We want it to be as easy as possible to get started with Laravel regardless of your preferred operating system. So, there are a variety of options for developing and running a Laravel project on your local machine. While you may wish to explore these options at a later time, Laravel provides [Sail](/docs/{{version}}/sail), a built-in solution for running your Laravel project using [Docker](https://www.docker.com).
あなたの好みのオペレーティングシステムに関係なく、Laravelをできるだけ簡単に始められるようにしたいと考えています。そのために、ローカルマシンでLaravelプロジェクトを開発し実行するための様々なオプションを提供しています。これらのオプションについては後で説明しますが、Laravelは、[Docker](https://www.docker.com)、[Sail](/docs/{{バージョン}}/sail)を提供したLaravelプロジェクトを実行するための組み込みソリューションを提供します。

Docker is a tool for running applications and services in small, light-weight "containers" which do not interfere with your local machine's installed software or configuration. This means you don't have to worry about configuring or setting up complicated development tools such as web servers and databases on your local machine. To get started, you only need to install [Docker Desktop](https://www.docker.com/products/docker-desktop).
Dockerは、アプリケーションやサービスを小さく軽量な「コンテナ」で実行するためのツールで、ローカルマシンのインストール済みソフトウェアや設定に干渉しません。つまり、ローカルマシンにWebサーバーやデータベースなどの複雑な開発ツールを設定したり、セットアップしたりする心配がありません。[Docker Desktop](https://www.docker.com/products/docker-desktop) を使えば、すぐに使い始めることができます。

Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker configuration. Sail provides a great starting point for building a Laravel application using PHP, MySQL, and Redis without requiring prior Docker experience.
Laravel Sailは、LaravelのデフォルトのDocker設定と連携するための軽量なコマンドラインインターフェイスです。Sailは、PHP、MySQL、RedisでLaravelアプリケーションを構築するための素晴らしい出発点となり、事前のDocker経験を必要としません。Dockerの経験は必須ではありません。

> **Note**  
> Already a Docker expert? Don't worry! Everything about Sail can be customized using the `docker-compose.yml` file included with Laravel.
すでにDockerのエキスパートですか？ご安心ください！Laravelに含まれる`docker-compose.yml`ファイルを使用して、Sailですべてをカスタマイズすることができます。

<a name="getting-started-on-macos"></a>
### macOSで始める

If you're developing on a Mac and [Docker Desktop](https://www.docker.com/products/docker-desktop) is already installed, you can use a simple terminal command to create a new Laravel project. For example, to create a new Laravel application in a directory named "example-app", you may run the following command in your terminal:
Macで開発していて、[Docker Desktop](https://www.docker.com/products/docker-desktop)が既にインストールされていれば、簡単なターミナルコマンドで新しいLaravelプロジェクトを作成することができます。例えば、「example-app」という名前のディレクトリに新しいLaravelアプリケーションを作成するには、ターミナルで以下のコマンドを実行します

```shell
curl -s "https://laravel.build/example-app" | bash
```

もちろん、このURLの「example-app」を好きなものに変更することができます。ただし、アプリケーション名には英数字、ダッシュ、アンダースコアしか含まれていないことを確認してください。Laravelアプリケーションのディレクトリは、コマンドを実行したディレクトリの中に作成されます。

Sailのインストールは、Sailのアプリケーションコンテナがローカルマシンに構築される間、数分かかる場合があります。

After the project has been created, you can navigate to the application directory and start Laravel Sail. Laravel Sail provides a simple command-line interface for interacting with Laravel's default Docker configuration:
プロジェクトが作成されたら、アプリケーションのディレクトリに移動してLaravel Sailを起動します。Laravel Sailは、LaravelのデフォルトのDocker構成と連携するためのシンプルなコマンドラインインターフェイスを提供します：

```shell
cd example-app

./vendor/bin/sail up
```

Once the application's Docker containers have been started, you can access the application in your web browser at: http://localhost.
アプリケーションのDockerコンテナが起動したら、`http://localhost` に Web ブラウザでアプリケーションにアクセスできます。

> **Note**  
> To continue learning more about Laravel Sail, review its [complete documentation](/docs/{{version}}/sail).
Laravel Sailの詳細については、その[完全なドキュメント](/docs/{{バージョン}}/sail)を参照してください。

<a name="getting-started-on-windows"></a>
### Windowsで始める

Before we create a new Laravel application on your Windows machine, make sure to install [Docker Desktop](https://www.docker.com/products/docker-desktop). Next, you should ensure that Windows Subsystem for Linux 2 (WSL2) is installed and enabled. WSL allows you to run Linux binary executables natively on Windows 10. Information on how to install and enable WSL2 can be found within Microsoft's [developer environment documentation](https://docs.microsoft.com/en-us/windows/wsl/install-win10).
Windowsマシンで新しいLaravelアプリケーションを作成する前に、[Docker Desktop](https://www.docker.com/products/docker-desktop)をインストールすることを確認してください。次に、Windows Subsystem for Linux 2 (WSL2)がインストールされ、有効になっていることを確認する必要があります。WSLを使用すると、Windows 10上でLinuxのバイナリ実行ファイルをネイティブに実行することができます。WSL2のインストールと有効化に関する情報は、Microsoftの[開発者環境ドキュメント](https://docs.microsoft.com/en-us/windows/wsl/install-win10)内に記載されています。

> **Note**  
> After installing and enabling WSL2, you should ensure that Docker Desktop is [configured to use the WSL2 backend](https://docs.docker.com/docker-for-windows/wsl/).
 WSL2をインストールして有効にした後、Docker Desktopが[WSL2バックエンドを使用するように設定されている](https://docs.docker.com/docker-for-windows/wsl/)ことを確認する必要があります。

Next, you are ready to create your first Laravel project. Launch [Windows Terminal](https://www.microsoft.com/en-us/p/windows-terminal/9n0dx20hk701?rtc=1&activetab=pivot:overviewtab) and begin a new terminal session for your WSL2 Linux operating system. Next, you can use a simple terminal command to create a new Laravel project. For example, to create a new Laravel application in a directory named "example-app", you may run the following command in your terminal:
次に、最初のLaravelプロジェクトを作成する準備が整いました。[Windows Terminal](https://www.microsoft.com/en-us/p/windows-terminal/9n0dx20hk701?rtc=1&activetab=pivot:overviewtab) を起動し、WSL2 Linuxオペレーティングシステム用の新しいターミナルセッションを開始します。次に、単純なターミナルコマンドを使用して、新しいLaravelプロジェクトを作成することができます。例えば、「example-app」という名前のディレクトリに新しいLaravelアプリケーションを作成するには、ターミナルで次のコマンドを実行します：

```shell
curl -s https://laravel.build/example-app | bash
```

Of course, you can change "example-app" in this URL to anything you like - just make sure the application name only contains alpha-numeric characters, dashes, and underscores. The Laravel application's directory will be created within the directory you execute the command from.
もちろん、このURLの「example-app」を好きなものに変更することができます。ただし、アプリケーション名には英数字、ダッシュ、アンダースコアしか含まれていないことを確認してください。Laravelアプリケーションのディレクトリは、コマンドを実行したディレクトリの中に作成されます。

Sail installation may take several minutes while Sail's application containers are built on your local machine.
Sailのインストールは、Sailのアプリケーションコンテナがローカルマシンに構築される間、数分かかる場合があります。

After the project has been created, you can navigate to the application directory and start Laravel Sail. Laravel Sail provides a simple command-line interface for interacting with Laravel's default Docker configuration:
プロジェクトが作成されたら、アプリケーションのディレクトリに移動してLaravel Sailを起動します。Laravel Sailは、LaravelのデフォルトのDocker構成と対話するためのシンプルなコマンドラインインターフェイスを提供します：

```shell
cd example-app

./vendor/bin/sail up
```

Once the application's Docker containers have been started, you can access the application in your web browser at: http://localhost.
アプリケーションのDockerコンテナが起動したら、ウェブブラウザでアプリケーションにアクセスできます: http://localhost.

> **Note**  
> Laravel Sailについてさらに学ぶには、その[完全なドキュメント](/docs/{{バージョン}}/sail)を確認してください。

#### WSL2内で開発する

Of course, you will need to be able to modify the Laravel application files that were created within your WSL2 installation. To accomplish this, we recommend using Microsoft's [Visual Studio Code](https://code.visualstudio.com) editor and their first-party extension for [Remote Development](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.vscode-remote-extensionpack).
もちろん、WSL2インストール内に作成されたLaravelアプリケーションファイルを変更できる必要があります。これを実現するには、Microsoftの[Visual Studio Code](https://code.visualstudio.com)エディターと、[リモート開発](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.vscode-remote-extensionpack)のファーストパーティ拡張機能を使うことをお勧めします。

Once these tools are installed, you may open any Laravel project by executing the `code .` command from your application's root directory using Windows Terminal.
これらのツールをインストールしたら、Windowsターミナルを使用して、アプリケーションのルートディレクトリから`code .`コマンドを実行することで、任意のLaravelプロジェクトを開くことができます。

<a name="getting-started-on-linux"></a>
### Linuxで始める

If you're developing on Linux and [Docker Compose](https://docs.docker.com/compose/install/) is already installed, you can use a simple terminal command to create a new Laravel project. For example, to create a new Laravel application in a directory named "example-app", you may run the following command in your terminal:
Linuxで開発していて、[Docker Compose](https://docs.docker.com/compose/install/)が既にインストールされている場合、簡単なターミナルコマンドで新しいLaravelプロジェクトを作成することができます。例えば、"example-app "というディレクトリに新しいLaravelアプリケーションを作成するには、ターミナルで次のコマンドを実行します：

```shell
curl -s https://laravel.build/example-app | bash
```

Of course, you can change "example-app" in this URL to anything you like - just make sure the application name only contains alpha-numeric characters, dashes, and underscores. The Laravel application's directory will be created within the directory you execute the command from.
もちろん、このURLの「example-app」を好きなものに変更することができます。ただし、アプリケーション名には英数字、ダッシュ、アンダースコアしか含まれていないことを確認してください。Laravelアプリケーションのディレクトリは、コマンドを実行したディレクトリの中に作成されます。

Sail installation may take several minutes while Sail's application containers are built on your local machine.
Sailのインストールは、Sailのアプリケーションコンテナがローカルマシンで構築される間、数分かかる場合があります。

After the project has been created, you can navigate to the application directory and start Laravel Sail. Laravel Sail provides a simple command-line interface for interacting with Laravel's default Docker configuration:
プロジェクトが作成されたら、アプリケーションのディレクトリに移動し、Laravel Sailを起動することができます。Laravel Sailは、LaravelのデフォルトのDocker構成と対話するためのシンプルなコマンドラインインターフェイスを提供します：

```shell
cd example-app

./vendor/bin/sail up
```

Once the application's Docker containers have been started, you can access the application in your web browser at: http://localhost.
アプリケーションのDockerコンテナが起動したら、ウェブブラウザでアプリケーションにアクセスできます: http://localhost.

> **Note**  
> To continue learning more about Laravel Sail, review its [complete documentation](/docs/{{version}}/sail).
Laravel Sailについてさらに詳しく学ぶには、その[完全なドキュメント](/docs/{{バージョン}}/sail)を確認してください。

<a name="choosing-your-sail-services"></a>
### Choosing Your Sail Services
Sailのサービスを選択する

When creating a new Laravel application via Sail, you may use the `with` query string variable to choose which services should be configured in your new application's `docker-compose.yml` file. Available services include `mysql`, `pgsql`, `mariadb`, `redis`, `memcached`, `meilisearch`, `minio`, `selenium`, and `mailpit`:
Sail経由で新しいLaravelアプリケーションを作成する場合、`with`クエリ文字列変数を使用して、新しいアプリケーションの `docker-compose.yml` ファイルに設定するサービスを選択することができます。利用可能なサービスは `mysql`、`pgsql`、`mariadb`、`redis`、`memcached`、`meilisearch`、`minio`、`selenium`、`mailpit` である：

```shell
curl -s "https://laravel.build/example-app?with=mysql,redis" | bash
```

If you do not specify which services you would like configured, a default stack of `mysql`, `redis`, `meilisearch`, `mailpit`, and `selenium` will be configured.

You may instruct Sail to install a default [Devcontainer](/docs/{{version}}/sail#using-devcontainers) by adding the `devcontainer` parameter to the URL:

```shell
curl -s "https://laravel.build/example-app?with=mysql,redis&devcontainer" | bash
```

<a name="initial-configuration"></a>
## Initial Configuration

All of the configuration files for the Laravel framework are stored in the `config` directory. Each option is documented, so feel free to look through the files and get familiar with the options available to you.

Laravel needs almost no additional configuration out of the box. You are free to get started developing! However, you may wish to review the `config/app.php` file and its documentation. It contains several options such as `timezone` and `locale` that you may wish to change according to your application.

<a name="environment-based-configuration"></a>
### Environment Based Configuration

Since many of Laravel's configuration option values may vary depending on whether your application is running on your local machine or on a production web server, many important configuration values are defined using the `.env` file that exists at the root of your application.

Your `.env` file should not be committed to your application's source control, since each developer / server using your application could require a different environment configuration. Furthermore, this would be a security risk in the event an intruder gains access to your source control repository, since any sensitive credentials would get exposed.

> **Note**  
> For more information about the `.env` file and environment based configuration, check out the full [configuration documentation](/docs/{{version}}/configuration#environment-configuration).

<a name="databases-and-migrations"></a>
### Databases & Migrations

Now that you have created your Laravel application, you probably want to store some data in a database. By default, your application's `.env` configuration file specifies that Laravel will be interacting with a MySQL database and will access the database at `127.0.0.1`. If you are developing on macOS and need to install MySQL, Postgres, or Redis locally, you may find it convenient to utilize [DBngin](https://dbngin.com/).

If you do not want to install MySQL or Postgres on your local machine, you can always use a [SQLite](https://www.sqlite.org/index.html) database. SQLite is a small, fast, self-contained database engine. To get started, create a SQLite database by creating an empty SQLite file. Typically, this file will exist within the `database` directory of your Laravel application:

```shell
touch database/database.sqlite
```

Next, update your `.env` configuration file to use Laravel's `sqlite` database driver. You may remove the other database configuration options:

```ini
DB_CONNECTION=sqlite # [tl! add]
DB_CONNECTION=mysql # [tl! remove]
DB_HOST=127.0.0.1 # [tl! remove]
DB_PORT=3306 # [tl! remove]
DB_DATABASE=laravel # [tl! remove]
DB_USERNAME=root # [tl! remove]
DB_PASSWORD= # [tl! remove]
```

Once you have configured your SQLite database, you may run your application's [database migrations](/docs/{{version}}/migrations), which will create your application's database tables:

```shell
php artisan migrate
```

<a name="next-steps"></a>
## Next Steps

Now that you have created your Laravel project, you may be wondering what to learn next. First, we strongly recommend becoming familiar with how Laravel works by reading the following documentation:

<div class="content-list" markdown="1">

- [Request Lifecycle](/docs/{{version}}/lifecycle)
- [Configuration](/docs/{{version}}/configuration)
- [Directory Structure](/docs/{{version}}/structure)
- [Frontend](/docs/{{version}}/frontend)
- [Service Container](/docs/{{version}}/container)
- [Facades](/docs/{{version}}/facades)

</div>

How you want to use Laravel will also dictate the next steps on your journey. There are a variety of ways to use Laravel, and we'll explore two primary use cases for the framework below.

> **Note**
> New to Laravel? Check out the [Laravel Bootcamp](https://bootcamp.laravel.com) for a hands-on tour of the framework while we walk you through building your first Laravel application.

<a name="laravel-the-fullstack-framework"></a>
### Laravel The Full Stack Framework

Laravel may serve as a full stack framework. By "full stack" framework we mean that you are going to use Laravel to route requests to your application and render your frontend via [Blade templates](/docs/{{version}}/blade) or a single-page application hybrid technology like [Inertia](https://inertiajs.com). This is the most common way to use the Laravel framework, and, in our opinion, the most productive way to use Laravel.

If this is how you plan to use Laravel, you may want to check out our documentation on [frontend development](/docs/{{version}}/frontend), [routing](/docs/{{version}}/routing), [views](/docs/{{version}}/views), or the [Eloquent ORM](/docs/{{version}}/eloquent). In addition, you might be interested in learning about community packages like [Livewire](https://laravel-livewire.com) and [Inertia](https://inertiajs.com). These packages allow you to use Laravel as a full-stack framework while enjoying many of the UI benefits provided by single-page JavaScript applications.

If you are using Laravel as a full stack framework, we also strongly encourage you to learn how to compile your application's CSS and JavaScript using [Vite](/docs/{{version}}/vite).

> **Note**  
> If you want to get a head start building your application, check out one of our official [application starter kits](/docs/{{version}}/starter-kits).

<a name="laravel-the-api-backend"></a>
### Laravel The API Backend

Laravel may also serve as an API backend to a JavaScript single-page application or mobile application. For example, you might use Laravel as an API backend for your [Next.js](https://nextjs.org) application. In this context, you may use Laravel to provide [authentication](/docs/{{version}}/sanctum) and data storage / retrieval for your application, while also taking advantage of Laravel's powerful services such as queues, emails, notifications, and more.

If this is how you plan to use Laravel, you may want to check out our documentation on [routing](/docs/{{version}}/routing), [Laravel Sanctum](/docs/{{version}}/sanctum), and the [Eloquent ORM](/docs/{{version}}/eloquent).

> **Note**  
> Need a head start scaffolding your Laravel backend and Next.js frontend? Laravel Breeze offers an [API stack](/docs/{{version}}/starter-kits#breeze-and-next) as well as a [Next.js frontend implementation](https://github.com/laravel/breeze-next) so you can get started in minutes.
