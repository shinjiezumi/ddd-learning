# 値オブジェクトとは

システムには必要とされる処理にしたがって、そのシステムならではの値の表現がある。 ドメイン駆動設計ではこのようなシステム固有の値を現したオブジェクトを値オブジェクトと呼ぶ。

# 値オブジェクトの性質

- 不変である
- 交換が可能である
- 等価性によって比較される

## 不変である

以下のような実装は避ける。

```php
$fullName = new FullName('taro', 'yamada');
$fullName->changeLastName('sato');
```

生成したインスタンスをメソッドに引き渡したら、いつの間にか状態を変更されて意図せぬ挙動となる場合がある。

この解決策として状態を不変にする。いつの間にか変更されることが問題ならそもそも変更できないようにしてしまえば良い。

状態を変更できないというのはプログラムをシンプルにする可能性を秘めた制約でもある。

- 並行・並列処理を伴うプログラムを比較的に容易に実装できる
- 状態が変更されないオブジェクトであればひとつのオブジェクトをキャッシュして使い回すことでリソースの節約が可能

### 不変にすることによるデメリット

デメリットは、オブジェクトの値を一部変更したい時でも新たなインスタンスを生成する必要がある点。

状態を変更できるときに比べてパフォーマンスの寒天で不利になる。

## 交換が可能である

インスタンスの再生成による変更のみ許容する。

```php
$fullName = new FullName('taro', 'yamada');
$fullName = new FullName('taro', 'sato');
```

## 等価性によって比較される

値オブジェクトはシステム固有の値で、その属性(プロパティ)を取り出して比較するのではなく、 値と同じように値オブジェクト同士が比較できるようにする方が自然な記述になる。

比較用の`equals`メソッドを実装するのが一般的。

```php
$nameA = new FullName('taro', 'yamada');
$nameB = new FullName('taro', 'sato');

$compareResult = $nameA->equals($nameB);
```

このような実装にすると、値オブジェクトにプロパティが追加されても呼び出し元の修正が不要で保守性も高くなる。

# 値オブジェクトにする基準

どこまでを値オブジェクトにするかは難しい。

`FullName`のプロパティである`$lastName`と`$firstName`を、値オブジェクトにすることは間違いではない。

値オブジェクトにする/しないの一つの判断基準として、「そこにルールが存在しているか」という点と「それ単体で取扱いたいか」という点を考えてみる。

姓だけや名だけをシステムとして利用するシーンがなければ不要ともいえる。

逆に値オブジェクトにする場合は、姓と名を同じもの(`Name`)とするか、別物(`LastName`/`FirstName`)とするかも検討が必要。

重要なのは値オブジェクトを避けることではなく、値オブジェクトにすべきかを見極めて、そうすべきと判断したら大胆に実行すべき。

それは実装中に発見した場合でもドメインモデルとしてフィードバックしていく。

# 振る舞いを持った値オブジェクト

値オブジェクトはデータを保持するコンテナではなく、振る舞いを持つことができるオブジェクト。

お金を加算する時は通貨を揃える必要があるため、同一かどうかチェックする。

また、値オブジェクトは不変であるため、計算を行った結果は新たなインスタンスとして返却する。

```php
class Money
{
    private $amount;
    private $currency;
    
    public function Add(Money $arg): Money
    {
        if ($this->currency !== $arg->currency) {
            throw new InvalidArgumentException("通貨が異なります");
        }
    
        return new Money($this->amount + $arg->amount, $this->currency);
    }
}
```

バグは思い違いから発生するもの。計算処理にルールを記述しそれにそぐわない操作をはじくようにして、システマチックに誤った操作を防止する。

値オブジェクトはただのデータ構造を指しているわけではなく、オブジェクトに対する操作を振る舞いとして一処ににまとめることで、値オブジェクトは自身に関するルールをまとめたドメインオブジェクトらしさを帯びる。

# 値オブジェクトを採用するモチベーション

値オブジェクトを採用して数多くのクラスを定義することに抵抗を感じる開発者は居る。

値オブジェクトを使うモチベーションになるものとして以下がある。

- 表現力が増す
- 不正な値を存在させない
- 誤った代入を防ぐ
- ロジックの散財を防ぐ

## 表現力が増す

例えば工業製品にはロット番号やシリアル番号、製品番号などさまざな識別をする番号が存在している。それらは数字だけ、アルファベットも含めた文字列などさまざま。
これをプリミティブ型で表現すると、利用する箇所でその内容がどういったものかが分かりづらい。

```php
$modelNumber = "a12345-100-1";

doSomething($modelNumber);

function doSomething(string $modelNumber) {
    // ここでどういった内容、構成の番号かわからない
    printf($modelNumber);
}
```

`ModelNumber`クラスのように定義すると、製品番号の構成が一目でわかる。 値オブジェクトはその定義より、自分がどういったものかを主張する自己文書化を推し進める。

## 不正な値を存在させない

不正な値を存在させないために、値オブジェクトをうまく利用する。

`UserName`クラスは３文字未満のユーザー名を許可しないようになっており、この値オブジェクトを使い続ける限り、不正な値が存在することを防げる

## 誤った代入を防ぐ

例としてユーザーIDはシステムによって異なる。ユーザー名がそのままIDになったりメールアドレスなどの場合もある。 以下のようなコードでは、呼び出し元を確認する必要があり、IDの正当性が判断できない。
コードの正しさを証明するために自己文書化を進めて、コードの正しさをコードで表現できるようにする。

```php
function createUser(string $name): User
{
    $user = new User();
    $user->id = $name;
    return $user;
}

class User
{
    public $id;
}
```

`User`クラスのプロパティを値オブジェクトにすることで、型不一致となり不正な代入が発生しない。

```php
function createUser(string $name): User
{
   $userId = new UserId("hoge");
   $userName = new UserName("hoge");
   
   $user = new User();
   $user->id = $userName; // エラー
   
   return $user;
} 
```

## ロジックの散財を防ぐ

値オブジェクトを使わない場合、不正な値を存在させないために都度チェックを実装する必要がある。 また、仕様変更時にすべてのチェック処理に影響が及んでしまう。

値オブジェクトにすることで、ロジックの散財を防げて仕様変更にも強いコードになる。

# まとめ

値オブジェクトのコンセプトは「システム固有の値を作る」という単純なもの。 プリミティブ型でシステムを構築することは可能だが表現力が乏しくなるので、値オブジェクトを積極的に活用していく。
また、値オブジェクトを定義すると関連するルールや仕様が値オブジェクトに記述されるため、コードがドキュメントにもなる。
