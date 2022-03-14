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