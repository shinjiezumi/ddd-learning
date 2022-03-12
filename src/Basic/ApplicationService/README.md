# アプリケーションサービスとは

アプリケーションサービスは、ユースケースを実現するオブジェクト。

例えばユーザー登録が必要なシステムにおいて、「ユーザーを登録する」ユースケースや「ユーザー情報を変更する」ユースケースが必要で、ユーザー機能のアプリケーションサービスはこれらふるまいを定義する。

それらのふるまいは実際にドメインオブジェクトを組み合わせて実行するようなふるまいとなる。

# ユースケースを組み立てる

サンプルととしてSNSのユーザー機能を取り扱う。

利用者はシステムを利用するためにユーザー登録をする必要がある。

利用者は登録したユーザー情報を参照し、場合によっては変更、不要になった際は退会を行うこともできる。

## ドメインオブジェクトを準備する

今回取り扱うユーザーの概念はライフサイクルがあるためエンティティとして実装する。

## ユーザー登録処理を作成する

`UserApplicationService.register`参照

## ユーザー情報取得処理を作成する

`UserApplicationService.get`参照

返却する結果として、ドメインオブジェクトをそのまま戻り値とするか否かの選択は重要。

ドメインオブジェクトを公開する場合、アプリケーションサービスのコードはシンプルになる。

ただ、アプリケーションサービスのクライアントもドメインオブジェクトのメソッドを呼び出すことが可能になってしまい、ドメインオブジェクトに多くの依存が発生してしまう。

そのため、ドメインオブジェクトのふるまいを呼び出すのは、アプリケーションサービスに限定する方が良い。

ドメインオブジェクトを返すのではなく、クライアントにはDTOに移し替えて返却する。

`UserData`参照。

また、`UserData`のコンストラクタに`User`オブジェクトを渡してあげると、`User`オブジェクトの属性が増えても修正量が少なくて済む。

### 煩わしさを減らすために、DTO生成ツールを作るとよい

ドメインオブジェクトを公開する方法に比べ、記述が増えてしまうデメリットがある。

そこでドメインオブジェクトからDTOを自動生成するツールを作ると、デメリットが解消されるのでおすすめ。

## ユーザー情報更新処理を作成する

`UserApplicationService.update`、`UserUpdateCommand`参照

ユーザーの属性が増えた際にシグネチャが変更されてしまうので、コマンドオブジェクトを利用すると属性変更にも強くなる。

コマンドオブジェクトを作ることは間接的にアプリケーションサービスの処理を制御することと同義。

```php
// ユーザー名変更だけ行う
$updateNameCommand = new UserUpdateCommand('id', 'name', null);
$userApplicationService->update($updateNameCommand);

// メールアドレス変更だけ行う
$updateMailAddressCommand = new UserUpdateCommand('id', null, 'hoge@gmail.com');
$userApplicationService->update($updateMailAddressCommand);
```

コマンドオブジェクトは処理のファサードともいえる。

## 退会処理を作成する

`UserApplicationService.delete`参照

退会処理はインスタンスの復元を行い、削除するだけのシンプルな処理。

# ドメインのルールの流出

アプリーケーションサービスはドメインオブジェクトのタスク調整に徹するべきで、ドメインのルールは記述しない。

例えばドメインサービスに記述している重複確認を行うコードをアプリケーションサービスに記述してしまうと、同じようなコード(リポジトリからの復元)が点在してしまう。

# アプリケーションサービスと凝集度

凝集度はモジュールの責任範囲がどれだけ集中しているかを図る尺度。高めるとモジュールがひとつの事柄に集中することになり、堅牢性・信頼性・再利用性・可読性の観点から好ましいとされている。

凝集度を図る方法としてLCOM(Lack of Cohesion in Methods)という計算式がある。

これはすべてのインスタンス変数はすべてのメソッドで使われるべき、というもので、計算式はインスタンス変数とそれが利用されているメソッドの数から計算される。

以下のLowCohesionクラスは凝集度が低いクラス。

```php
class LowCohesion
{
  private int $value1;
  private int $value2;
  private int $value3;
  private int $value4;
  
  public function methodA()
  {
    return $this->value1 + $this->value2;
  }

  public function methodB()
  {
    return $this->value3 + $this->value4;
  }
}
```

`LowCohesion`クラスの`value1`は`methodA`で利用されているが、`methodB`では利用されておらず、`value1`と`methodB`は本質的に関係がない。

これらを分離することで凝集度を高めれる。

```php
class HighCohesionA
{
  private int $value1;
  private int $value2;
  
  public function methodA()
  {
    return $this->value1 + $this->value2;
  }
}

class HighCohesionB
{
  private int $value3;
  private int $value4;

  public function methodB()
  {
    return $this->value3 + $this->value4;
  }
}
```

いずれのクラスも全てのフィールドがそのクラスに定義されているすべてのメソッドで利用されており、凝集度が高い状態。

## 凝集度を高める

凝集度を高めるために、アプリケーションサービスも分割して凝集度を高める。

ユーザー重複チェックでドメインサービスを利用しているのはユーザー登録処理だけなので、凝集度を高めるために分割する。

`UserRegisterService`、`UserDeleteService`参照

処理内容がクラス名で表現されるのでメソッド名はシンプルは表現に変更できる。

