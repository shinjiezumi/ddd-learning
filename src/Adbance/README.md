# 依存とは

依存はあるオブジェクトからあるオブジェクトを参照するだけで発生する。

例えば以下のコードでは`ObjectA`は`ObjectB`に依存していると言える。

```php
class ObjectA
{
    private ObjectB $objectB;
}
```

これはインターフェースとその実装クラスにもいえる。

`UserRepository`クラスは`IUserRepository`インターフェースを実装していて、`IUserRepository`の定義が存在しなかったらエラーとなる。

つまり`UserRepository`クラスは`IUserRepository`インターフェースに依存している状態。

```php
interface IUserRepository
{
    public function find(UserID $id) :User
}

class UserRepository implements IUserRepository
{
    public function find(UserID $id) :User
    {
        // snip
    }
}
```

アプリケーションサービスはリポジトリインタークラスを直接利用(依存)するのではなく、インターフェース(抽象型)を利用するようにする。

インターフェースを利用することで、テストではインメモリDBを利用するなど柔軟に対応できるため。

# 依存関係逆転の原則とは

依存関係逆転の原則(Dependency Inversion Principle)は以下のように定義されている。

- 上位レベルのモジュールは下位レベルのモジュールに依存してはならない。どちらのモジュールも抽象に依存すべきである。
- 抽象は、実装の詳細に依存してはならない。実装の詳細が抽象に依存すべきである。

依存関係逆転の原則はソフトウェアを柔軟なものに変化させ、ビジネスロジックを技術的な要素から守るのに欠かせないもの。

