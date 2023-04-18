# フロントエンド

- [はじめに](#introduction)
- [PHP の利用](#using-php)
    - [PHP と Blade](#php-and-blade)
    - [Livewire](#livewire)
    - [スターターキット](#php-starter-kits)
- [Vue / React の使用](#using-vue-react)
    - [Inertia](#inertia)
    - [スターターキット](#inertia-starter-kits)
- [アセットの結合](#bundling-assets)

<a name="introduction"></a>
## はじめに

Laravelは、バックエンドフレームワークであり、[ルーティング](/laravel10_ja/routing)、[バリデーション](/laravel10_ja/validation)、[キャッシュ](/laravel10_ja/cache)、[キュー](/laravel10_ja/queues)、[ファイルストレージ](/laravel10_ja/filesystem) など、現代のWebアプリケーションを構築するために必要なすべての機能を提供しています。しかし、開発者に美しいフルスタックの経験を提供することも重要であり、アプリケーションのフロントエンドを構築するための強力なアプローチが含まれます。

Laravel でアプリケーションを構築する際、フロントエンド開発に取り組む主な方法は2つあります。どちらのアプローチを選ぶかは、フロントエンドを  PHP を活用して構築するか、Vue や React のような JavaScript フレームワークを使用して構築するかによって決まります。以下でこれらの選択肢について説明し、アプリケーションに最適なフロントエンド開発のアプローチを選択できるようにします。

<a name="using-php"></a>
## PHP の利用

<a name="php-and-blade"></a>
### PHP と Blade

これまで、ほとんどの PHP アプリケーションは、シンプルな HTML テンプレートを使用してブラウザに HTML をレンダリングし、リクエスト中にデータベースから取得されたデータを表示するために PHP の `echo` 文を使用していました。

```blade
<div>
    <?php foreach ($users as $user): ?>
        Hello, <?php echo $user->name; ?> <br />
    <?php endforeach; ?>
</div>
```

Laravel では、このようなHTMLのレンダリング方法は、[ビュー](/laravel10_ja/views) と [Blade](/laravel10_ja/blade) を使用して実現することができます。Blade は、データの表示、データの反復処理などに便利で短い構文を提供する非常に軽量なテンプレーティング言語です。

```blade
<div>
    @foreach ($users as $user)
        Hello, {{ $user->name }} <br />
    @endforeach
</div>
```

この方法でアプリケーションを構築する場合、通常、フォーム送信やその他のページ インタラクションは、サーバーからまったく新しい HTML ドキュメントを受け取り、ページ全体がブラウザーによって再レンダリングされます。 今日でも、多くのアプリケーションは、単純な Blade テンプレートを使用してこのようにフロントエンドを構築することに完全に適している場合があります。

<a name="growing-expectations"></a>
#### 期待の高まり

しかし、Web アプリケーションに対するユーザーの期待が成熟するにつれ、多くの開発者は、より洗練されたインタラクションを持つダイナミックなフロントエンドを構築する必要性を感じるようになりました。これを受けて、一部の開発者は、Vueや React のような JavaScript フレームワークを使用してアプリケーションのフロントエンドを構築し始めます。

一方、バックエンド言語に慣れている開発者は、主にバックエンド言語を使用しながら、現代の Web アプリケーション UI を構築するためのソリューションを開発しました。例えば [Rails](https://rubyonrails.org/) エコシステムでは、[Turbo](https://turbo.hotwired.dev/) [Hotwire](https://hotwired.dev/)、[Stimulus](https://stimulus.hotwired.dev/) などのライブラリが登場しています。

Laravel エコシステムでは、主に PHP を使用してモダンでダイナミックなフロントエンドを作成するためのニーズが、[Laravel Livewire](https://laravel-livewire.com) や [Alpine.js](https://alpinejs.dev/) の登場につながっています。

<a name="livewire"></a>
### Livewire

[Laravel Livewire](https://laravel-livewire.com) は、Vue や React のような JavaScript フレームワークで構築されたフロントエンドと同じように、ダイナミックでモダンで活気のあるフロントエンドを構築するための Laravel を活用したフレームワークです。

Livewire を使用する場合、UI の個別の部分をレンダリングし、アプリケーションのフロントエンドから呼び出され、操作されるメソッドやデータを公開する Livewire「コンポーネント」を作成します。例えば、シンプルな「Counter」コンポーネントは以下のようになります

```php
<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
```

そして、対応するカウンターのテンプレートは次のように記述されます。

```blade
<div>
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>
</div>
```

ご覧のとおり、Livewire を使用すると、Laravel アプリケーションのフロントエンドとバックエンドを接続する `wire:click` などの新しい HTML 属性を記述できます。 さらに、シンプルな Blade 式を使用して、コンポーネントの現在の状態をレンダリングできます。

多くの人にとって、Livewire は Laravel を使用したフロントエンド開発に革命をもたらし、最新の動的 Web アプリケーションを構築しながら Laravel の快適さを維持できるようにしました。 通常、Livewire を使用する開発者は、[Alpine.js](https://alpinejs.dev/) を利用して、ダイアログウィンドウをレンダリングする場合など、必要な場所にのみ JavaScript をフロントエンドに散りばめます。

Laravel が初めての方は、[ビュー](/laravel10_ja/views) と [Blade](/laravel10_ja/blade) の基本的な使用方法に慣れてから、[Laravel Livewire の公式ドキュメント](https://laravel-livewire.com/docs) を参照して、インタラクティブな Livewire コンポーネントを使ってアプリケーションを次のレベルに引き上げる方法を学んでください。

<a name="php-starter-kits"></a>
### スターター キット

PHP と Livewire を使用してフロントエンドを構築する場合は、Breeze または Jetstream の [スターター キット](/laravel10_ja/starter-kits) を活用して、アプリケーションの開発をすぐに開始できます。これらのスターターキットはどちらも、[Blade](/laravel10_ja/blade) と [Tailwind](https://tailwindcss.com) を使用して、アプリケーションのバックエンドとフロントエンドの認証フローを足場にします。 スキャフォールドし、次のビッグアイデアをすぐに構築できるようにします。

<a name="using-vue-react"></a>
## Vue / React の使用

Laravel と Livewire を使用して最新のフロントエンドを構築することは可能ですが、多くの開発者は依然として Vue や React などの JavaScript フレームワークの機能を活用することを好みます。 これにより、開発者は、NPM 経由で利用できる JavaScript パッケージとツールの豊富なエコシステムを活用できます。

ただし、追加のツールがなければ、Laravel を Vue または React と組み合わせると、クライアント側のルーティング、データ ハイドレーション、認証など、さまざまな複雑な問題を解決する必要が生じます。 クライアント側のルーティングは、多くの場合、[Nuxt](https://nuxtjs.org/) や [Next](https://nextjs.org/) などの独自の Vue / React フレームワークを使用して簡素化されます。 ただし、Laravel のようなバックエンド フレームワークをこれらのフロントエンド フレームワークと組み合わせる場合、データのハイドレーションと認証は依然として複雑で面倒な問題です。

さらに、開発者は 2 つの別個のコード リポジトリを維持する必要があり、多くの場合、両方のリポジトリ間でメンテナンス、リリース、および展開を調整する必要があります。 これらの問題は克服できないわけではありませんが、アプリケーションを開発するための生産的または楽しい方法ではないと考えています。

<a name="inertia"></a>
### 慣性

ありがたいことに、Laravel は両方の長所を提供します。 [Inertia](https://inertiajs.com) は、Laravel アプリケーションと最新の Vue または React フロントエンドの間のギャップを埋め、Laravel のルートとコントローラーをルーティングに活用しながら、Vue または React を使用して本格的な最新のフロントエンドを構築できるようにします。 データ ハイドレーションと認証 — すべてが単一のコード リポジトリ内にあります。 このアプローチにより、Laravel と Vue / React の両方の機能を損なうことなく、両方の機能を最大限に活用できます。

Inertia を Laravel アプリケーションにインストールしたら、通常どおりルートとコントローラーを記述します。 ただし、コントローラーから Blade テンプレートを返す代わりに、Inertia ページを返します。

php
<?php

名前空間 App\Http\Controllers;

App\Http\Controllers\Controller を使用します。
App\Models\User を使用します。
慣性\慣性を使用します。
Inertia\Response を使用します。

クラス UserController は Controller を拡張します
{
     /**
      * 特定のユーザーのプロフィールを表示します。
      */
     public function show(string $id): レスポンス
     {
         return Inertia::render('Users/Profile', [
             'user' => ユーザー::findOrFail($id)
         ]);
     }
}
```

慣性ページは Vue または React コンポーネントに対応し、通常はアプリケーションの「resources/js/Pages」ディレクトリ内に保存されます。 `Inertia::render` メソッドを介してページに与えられたデータは、ページ コンポーネントの「小道具」を水和するために使用されます。

```ビュー
<スクリプト設定>
「@/Layouts/Authenticated.vue」からレイアウトをインポートします。
'@inertiajs/inertia-vue3' から { ヘッド } をインポートします。

const props = defineProps(['user']);
</script>

<テンプレート>
     <Head title="ユーザー プロフィール" />

     <レイアウト>
         <テンプレート #ヘッダー>
             <h2 class="text-xl font-semibold text-gray-800 reading-tight">
                 プロフィール
             </h2>
         </テンプレート>

         <div class="py-12">
             こんにちは、{{ user.name }}
         </div>
     </レイアウト>
</テンプレート>
```

ご覧のとおり、Inertia を使用すると、フロントエンドを構築するときに Vue または React のフルパワーを活用しながら、Laravel を使用したバックエンドと JavaScript を使用したフロントエンドとの間に軽量のブリッジを提供できます。

#### サーバー側のレンダリング

アプリケーションでサーバー側のレンダリングが必要なために Inertia に飛び込むことを心配している場合でも、心配する必要はありません。 Inertia は [サーバー側レンダリングのサポート] (https://inertiajs.com/server-side-rendering) を提供します。 また、[Laravel Forge](https://forge.laravel.com) を介してアプリケーションをデプロイする場合、Inertia のサーバー側レンダリング プロセスが常に実行されていることを確認するのは簡単です。

<a name="inertia-starter-kits"></a>
### スターター キット

Inertia と Vue / React を使用してフロントエンドを構築したい場合は、Breeze または Jetstream [スターター キット](/docs/{{version}}/starter-kits#breeze-and-inertia) を活用してすぐに開始できます。 アプリケーションの開発。 これらのスターター キットは両方とも、Inertia、Vue / React、[Tailwind](https://tailwindcss.com)、および [Vite](https://vitejs.dev) を使用して、アプリケーションのバックエンドとフロントエンドの認証フローを足場にします。 次の大きなアイデアの構築を開始します。

<a name="bundling-assets"></a>
## アセットのバンドル

Blade と Livewire を使用してフロントエンドを開発するか、Vue / React と Inertia を使用してフロントエンドを開発するかに関係なく、アプリケーションの CSS をプロダクション対応のアセットにバンドルする必要がある可能性があります。 もちろん、アプリケーションのフロントエンドを Vue または React で構築することを選択した場合は、コンポーネントをブラウザ対応の JavaScript アセットにバンドルする必要もあります。

デフォルトでは、Laravel は [Vite](https://vitejs.dev) を利用してアセットをバンドルします。 Vite は、超高速のビルド時間と、ローカル開発中のほぼ瞬時のホット モジュール交換 (HMR) を提供します。 [スターター キット](/docs/{{version}}/starter-kits) を使用するものを含むすべての新しい Laravel アプリケーションには、軽量の Laravel Vite プラグインをロードする「vite.config.js」ファイルがあります。 これにより、Vite は Laravel アプリケーションで楽しく使用できます。

Laravel と Vite を開始する最も簡単な方法は、[Laravel Breeze](/docs/{{version}}/starter-kits#laravel-breeze) を使用してアプリケーションの開発を開始することです。これは、アプリケーションをすぐに開始できる最もシンプルなスターター キットです。 フロントエンドとバックエンドの認証足場を提供することによって。

> **注**
> Laravel での Vite の利用に関する詳細なドキュメントについては、[アセットのバンドルとコンパイルに関する専用ドキュメント](/docs/{{version}}/vite) を参照してください。
