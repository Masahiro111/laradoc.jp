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

新しい Laravel アプリケーションを構築するための手助けとして、認証とアプリケーションスターターキットを提供しています。これらのキットは、アプリケーションに必要なルート、コントローラー、ビューを自動的に生成し、ユーザーの登録と認証を可能にします。

これらのスターターキットを使用していただくことも歓迎しますが、必須ではありません。単に Laravel の新しいバージョンをインストールすることで、自分自身でアプリケーションを作成することもできます。いずれの場合でも、素晴らしいものが作れると思います。

<a name="laravel-breeze"></a>
## Laravel Breeze

[Laravel Breeze](https://github.com/laravel/breeze) は、ログイン、ユーザ登録、パスワードリセット、メール認証、パスワード確認などを含む、Laravel のすべての [認証機能](/laravel10_ja/authentication) を最小限、かつシンプルに実装したものとなっています。さらに、Breeze には、ユーザーが自分の名前、電子メール アドレス、およびパスワードを更新できるシンプルな「プロフィール」ページが含まれています。

Laravel Breeze のデフォルトビューレイヤーは、[Tailwind CSS](https://tailwindcss.com) でスタイル設定されたシンプルな [Blade テンプレート](/laravel10_ja/blade) で構成されています。また、Breeze は、Vue や React と [Inertia](https://inertiajs.com) を使用してアプリケーションを自動生成することもできます。

Breeze は、新しい Laravel アプリケーションを始めるための素晴らしい出発点を提供し、[Laravel Livewire](https://laravel-livewire.com) を使用して Blade テンプレートを次のレベルに引き上げることを計画しているプロジェクトにも最適です。

<img src="https://laravel.com/img/docs/breeze-register.png">

#### Laravel Bootcamp

Laravel を初めて使用する場合は、[Laravel Bootcamp](https://bootcamp.laravel.com) に参加してみてください。。Laravel Bootcamp では、Breeze を使用して最初の Laravel アプリケーションを構築する手順を説明しています。Laravel と Breeze が提供するすべての機能を学ぶ良い方法となるでしょう。

<a name="laravel-breeze-installation"></a>
### インストール

まず、[新しい Laravel アプリケーションを作成](/laravel10_ja/installation) して、データベースを構成し、[データベースマイグレーション](/laravel10_ja/migrations) を実行する必要があります。新しい Laravel アプリケーションを作成したら、Composer を使用して Laravel Breeze をインストールできます。

```shell
composer require laravel/breeze --dev
```

Breeze をインストールしたら、以下のドキュメントで説明されている Breeze の「スタック」のいずれかを使用して、アプリケーションを自動生成することか可能です。

<a name="breeze-and-blade"></a>
### Breeze & Blade

Composer が Laravel Breeze パッケージをインストールした後に、`breeze:install` Artisanコマンドを実行できます。このコマンドにより、認証ビュー、ルート、コントローラ、その他のリソースがアプリケーションに公開されます。Laravel Breeze はすべてのコードをアプリケーションに公開するため、機能と実装に完全な制御と可視性があります。

デフォルトの Breeze の「スタック」は Blade スタックで、シンプルな [Blade テンプレート](/docs/{{version}}/blade) を使用してアプリケーションのフロントエンドをレンダリングします。 Blade スタックは、他の引数を指定せずに「breeze:install」コマンドを呼び出すことでインストールできます。 Breeze のスキャフォールディングをインストールしたら、アプリケーションのフロントエンド アセットもコンパイルする必要があります。

シェル
php職人風:インストール

php 職人の移行
npm インストール
npm 実行 dev
```

次に、Web ブラウザでアプリケーションの `/login` または `/register` URL に移動できます。 Breeze のすべてのルートは、`routes/auth.php` ファイル内で定義されています。

<a name="ダークモード"></a>
#### ダークモード

アプリケーションのフロントエンドをスキャフォールディングするときに Breeze に「ダーク モード」サポートを含めたい場合は、`breeze:install` コマンドを実行するときに `--dark` ディレクティブを指定するだけです。

シェル
php 職人風:インストール --dark
```

> **注**
> アプリケーションの CSS と JavaScript のコンパイルについて詳しくは、Laravel の [Vite ドキュメント](/docs/{{version}}/vite#running-vite) をご覧ください。

<a name="そよ風と慣性"></a>
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
