# ファクトリの目的

複雑なオブジェクトをはその生成過程も複雑な処理になることがある。そうした処理はモデルを表現するドメインオブジェクトの趣旨をぼやけさせる。

かといって、その生成をクライアントに押し付けるのはよい方策ではなく、生成処理自体がドメインにおいて意味をもたなかったとしても、ドメインを表現する層の責務であることには変わりない。

求められることは複雑なオブジェクトの生成処理をオブジェクトとして定義すること。

この生成を責務とするオブジェクトのことをファクトリと呼び、ファクトリはオブジェクトの生成に関わる知識がまとめられたオブジェクト。

# 採番処理をファクトリに実装した場合

```php
class User
{
    private UserId $id;
    private UserName $name;
    
    public function __construct(UserName $name)
    {
        // some check..
        
        $this->id = new UserId(Guid.NewGuid().toString());
        $this->name = $name;
    }    
}
```

もし、採番方式がシーケンス(DB値+1)になった場合、ユーザーモデルにDB接続処理を記述することになってしまうので好ましくないのでファクトリを使うようにする。

```php
interface IUserFactory
{
    function create(UserName $name): User
}
```

ファクトリに定義されている`UserName`を引数に取り、`User`インスタンスを返却するメソッドが、ユーザー新規作成する際のコンストラクタの代わりとして利用される。

```php
class UserFactory implements IUserFactory
{
    function create(UserName $name): User
    {
        $seqId = xxx; // DBからシーケンス番号を取得
        
        $id = new UserId($seqId);
        return new User($id, $name);
    }
}
```

インスタンス生成処理がファクトリに移設されたことで`User`クラスをインスタンス化する際には必ず外部からUserIdが引き渡されることになり、`User`クラスで行っていたidの採番処理が不要になる。

その結果、DB接続処理をモデルに記述しなくて済むようになる。

ファクトリを利用すると以下のようになる。

```php
class UserApplicationService
{
    private IUserFactory $userFactory;
    private IUserRepository $userRepository;
    private IUserService $userService;
    
    function register(UserRegisterCommand $command)
    {
        $userName = new UserName($command->name);
        $user = $this->userFactory->create($userName);
        
        $this->userRepository->save($user);
    }
}
```

# 複雑な生成処理をカプセル化する

単純に生成方法が複雑なインスタンスを構築する処理をまとめるために、ファクトリを利用するのは良い習慣。

本来であれば初期化はコンストラクタの役目だが、コンストラクタは単純であるべきで複雑になる場合はファクトリを定義する。

「コンストラクタ内で他のオブジェクトを生成するかどうか」はファクトリを作る際の良い指標となる。

コンストラクタが他のオブジェクトを生成するようなことがあれば、そのオブジェクトが変更される際にコンストラクタも変更しないといけなくなる場合がある。

生成処理が複雑でないのであればコンストラクタを素直に利用すべきだが、ファクトリを導入すべきかを検討する習慣を身につけるのが重要。

