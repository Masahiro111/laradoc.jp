# スターター キット

- [はじめに](#introduction)
- [Laravel Breeze](#laravel-breeze)
     - [インストール](#laravel-breeze-installation)
     - [Breeze & Blade](#breeze-and-blade)
     - [Breeze & React / Vue](#breeze-and-inertia)
     - [Breeze & Next.js / API](#breeze-and-next)
- [Laravel Jetstream](#laravel-jetstream)

<a name="introduction"></a>
## はじめに

新しい Laravel アプリケーションを構築するための手助けとして、認証とアプリケーションスターターキットを提供しています。これらのキットは、アプリケーションのユーザ登録・認証するために必要なルート、コントローラー、ビューを自動的に生成します。

スターターキットを使うことは歓迎しますが、必須ではありません。新しい Laravel のインストールから始めて、独自のアプリケーションをゼロから構築することもできます。どちらにせよ、素晴らしいものが作れることを確信しています！

<a name="laravel-breeze"></a>
## Laravel Breeze

[Laravel Breeze](https://github.com/laravel/breeze) は、ログイン、登録、パスワードリセット、メールアドレス確認、パスワード確認を含む Laravel の [認証機能](/laravel10_ja/authentication) をすべてシンプルかつ最小限に実装したものです。さらに、Breeze にはユーザーが名前、メールアドレス、パスワードを更新できるシンプルな「プロフィール」ページが含まれています。

Laravel Breeze のデフォルトのビューレイヤーは、シンプルな [Blade テンプレート](/laravel10_ja/blade) を使用して [Tailwind CSS](https://tailwindcss.com) でスタイルが適用されています。また、Breeze は Vue または React を使用した [Inertia](https://inertiajs.com) を使ったアプリケーションのスケルトンを作成することもできます。

Breeze は、新しい Laravel アプリケーションを始めるのに最適なスタート地点であり、[Laravel Livewire](https://laravel-livewire.com) を使って Blade テンプレートを次のレベルに引き上げるプロジェクトにも適しています。

<img src="https://laravel.com/img/docs/breeze-register.png">

#### Laravel Bootcamp

Laravel を初めて使用する場合は、[Laravel Bootcamp](https://bootcamp.laravel.com) に参加してみてください。。Laravel Bootcamp では、Breeze を使って最初の Laravel アプリケーションを構築する方法を学ぶことができます。Laravel と Breeze が提供するすべての機能を体験するのに最適な方法です。

<a name="laravel-breeze-installation"></a>
### インストール

まず、[新しい Laravel アプリケーションを作成](/laravel10_ja/installation) して、データベースを構成し、[データベースマイグレーション](/laravel10_ja/migrations) を実行してください。新しい Laravel アプリケーションを作成したら、Composer を使用して Laravel Breeze をインストールできます。

```shell
composer require laravel/breeze --dev
```

Breeze をインストールしたら、以下のドキュメントで説明されている Breeze の「スタック」のいずれかを使用して、アプリケーションのスケルトンを作成できます。

<a name="breeze-and-blade"></a>
### Breeze & Blade

Composer が Laravel Breeze パッケージをインストールしたら、`breeze:install` Artisan コマンドを実行できます。このコマンドは、認証ビュー、ルート、コントローラー、およびアプリケーションにその他のリソースを公開します。Laravel Breeze は、その機能と実装に関する完全な制御と可視性を持つために、すべてのコードをアプリケーションに公開します。

デフォルトの Breeze "スタック" は Blade スタックで、シンプルな [Blade テンプレート](/docs/{{version}}/blade) を使ってアプリケーションのフロントエンドをレンダリングします。Blade スタックは、`breeze:install` コマンドを追加の引数なしで呼び出すことでインストールできます。Breeze のスケルトンがインストールされた後、アプリケーションのフロントエンドアセットもコンパイルする必要があります。

```shell
php artisan breeze:install

php artisan migrate
npm install
npm run dev
```

次に、Web ブラウザでアプリケーションの `/login` または `/register` URL に移動できます。 Breeze のすべてのルートは、`routes/auth.php` ファイル内で定義されています。

<a name="ダークモード"></a>
#### ダークモード

アプリケーションのフロントエンドをスケルトン化する際に Breeze に「ダークモード」サポートを含めたい場合は、`breeze:install` コマンドを実行する際に `--dark` ディレクティブを指定してください。

```shell
php artisan breeze:install --dark
```

> **Note**
> アプリケーションの CSS と JavaScript のコンパイルについて詳しくは、Laravel の [Vite ドキュメント](/docs/{{version}}/vite#running-vite) をご覧ください。

<a name="breeze-and-inertia"></a>
### Breeze & React / Vue

Laravel Breeze は、[Inertia](https://inertiajs.com) フロントエンド実装を介して React と Vue の足場も提供します。 Inertia を使用すると、従来のサーバー側のルーティングとコントローラーを使用して、最新の単一ページの React および Vue アプリケーションを構築できます。

Inertia を使用すると、React と Vue のフロントエンドのパワーを、Laravel と超高速の [Vite](https://vitejs.dev) コンパイルの信じられないほどのバックエンドの生産性と組み合わせて楽しむことができます。 Inertia スタックを使用するには、`breeze:install` Artisan コマンドを実行するときに、目的のスタックとして `vue` または `react` を指定します。 Breeze のスキャフォールディングをインストールしたら、アプリケーションのフロントエンド アセットもコンパイルする必要があります。

```shell
php artisan breeze:install vue

# Or...

php artisan breeze:install react

php artisan migrate
npm install
npm run dev
```

次に、Web ブラウザでアプリケーションの `/login` または `/register` URL に移動できます。 Breeze のすべてのルートは、`routes/auth.php` ファイル内で定義されています。

<a name="server-side-rendering"></a>
#### サーバー側のレンダリング

Breeze に [Inertia SSR](https://inertiajs.com/server-side-rendering) のサポートをスキャフォールディングさせたい場合は、`breeze:install` コマンドを呼び出すときに `ssr` オプションを指定できます。

シェル
php 職人風:インストール vue --ssr
php 職人のそよ風: 反応をインストール --ssr
```

<a name="breeze-and-next"></a>
### Breeze & Next.js / API

Laravel Breeze は、[Next](https://nextjs.org) や [Nuxt](https://nuxtjs.org) などによって強化された最新の JavaScript アプリケーションを認証する準備ができている認証 API をスキャフォールディングすることもできます。 開始するには、`breeze:install` Artisan コマンドを実行するときに、目的のスタックとして `api` スタックを指定します。

シェル
PHP職人風：APIをインストール

php 職人の移行
```

インストール中に、Breeze は「FRONTEND_URL」環境変数をアプリケーションの「.env」ファイルに追加します。 この URL は、JavaScript アプリケーションの URL である必要があります。 これは通常、ローカルでの開発中は `http://localhost:3000` になります。 さらに、`APP_URL` が `http://localhost:8000` に設定されていることを確認する必要があります。これは `serve` Artisan コマンドで使用されるデフォルトの URL です。

<a name="next-reference-implementation"></a>
#### Next.js リファレンス実装

最後に、このバックエンドを選択したフロントエンドとペアリングする準備が整いました。 Breeze フロントエンドの次のリファレンス実装は [GitHub で入手可能](https://github.com/laravel/breeze-next) です。 このフロントエンドは Laravel によって維持され、Breeze によって提供される従来の Blade および Inertia スタックと同じユーザー インターフェイスが含まれています。

<a name="laravel-jetstream"></a>
## Laravel ジェットストリーム

Laravel Breeze は、Laravel アプリケーションを構築するためのシンプルで最小限の出発点を提供しますが、Jetstream は、より堅牢な機能と追加のフロントエンド テクノロジ スタックでその機能を強化します。 **Laravel を初めて使用する場合は、Laravel Jetstream を卒業する前に、Laravel Breeze でコツを学ぶことをお勧めします。**

Jetstream は、Laravel 用に美しく設計されたアプリケーション スキャフォールディングを提供し、ログイン、登録、電子メール検証、2 要素認証、セッション管理、Laravel Sanctum による API サポート、およびオプションのチーム管理を含みます。 Jetstream は [Tailwind CSS](https://tailwindcss.com) を使用して設計されており、[Livewire](https://laravel-livewire.com) または [慣性](https://inertiajs.com) 駆動の選択を提供します。 フロントエンドの足場。

Laravel Jetstream のインストールに関する完全なドキュメントは、[公式の Jetstream ドキュメント](https://jetstream.laravel.com/3.x/introduction.html) にあります。
