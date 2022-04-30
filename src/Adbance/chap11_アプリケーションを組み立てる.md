# アプリケーションを組み立てるフロー

まず最初に確認することは、どういった機能が求められているかで、要求にしたがって必要な機能を考える。

追加する機能が定まったら、今度はその機能を成り立たせるために必要となるユースケースを洗い出す。

機能を実現しようとしたとき単一のユースケースだけでは無理であることも多く、いくつかのユースケースを必要とすることもある。

ユースケースが揃ったらそれらを実現するにあたって、必要となる概念とそこに存在するルールからアプリケーションが必要とする知識を選び出し、ドメインオブジェクトを準備する。

# サークル機能を作る

今まではSNSのユーザー機能を題材にしてきたので、ユーザー同士の交流を促すための機能としてサークル機能を作っていく。

サークルは同じ趣味をもつユーザー同士で交流するために作成されるグループで、たとえばスポーツを行うためのグルーブやボードゲームで遊ぶためのグループなど多岐にわたる。

## サークル機能の分析

サークル機能を実現するにあたって必要とされるユースケースは「サークルの作成」と「サークルへの参加」など。サークルからの脱退や削除といったユースケースは対象外とする。

サークルには次のルールがある。

- サークル名は3文字以上、20文字以下
- サークル名は重複しない
- サークルに所属するユーザーの最大数はサークルのオーナー含めて30名まで

これらのルールを踏まえて２つのユースケースを組み立てていく。

# サークルの知識やルールをオブジェクトとして準備する

まずはサークルを構成要素をコードとして表現していく。

サークルはライフサイクルがあるオブジェクトなのでエンティティになる。

ライフサイクルを表現するには識別子となる値が必要なので、値オブジェクトとして実装する

```php
class CircleId
{
    private string $value;

    public function __construct(?string $value)
    {
        if (is_null($value))
            throw new Exception("xxx");
        
        $this->value = $value;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
}
```

またサークルの名前を表す値オブジェクトも用意し、サークル名に存在するルールに従い異常値を検知したら例外を送出するようにする。

```php
class CircleName
{
    private string $value;

    public function __construct(?string $value)
    {
        if (is_null($value))
            throw new Exception("xxx");
        if (mb_strlen($value) < 3)
            throw new Exception("サークル名は3文字以上です");
        if (mb_strlen($value) > 20)
            throw new Exception("サークル名は20文字以下です");
        
        $this->value = $value;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function equals(CircleName $obj) bool
    {
        return $this->value === $obj->getValue();
    }
}
```

サークル名クラスには「サークル名は3文字以上20文字以下」というルールが記述され、また「サークル名はすべてのサークルで重複しない」というルールに対応するため、サークル名同士を比較するふるまいが定義されている。

これらの値オブジェクトを利用してライフサイクルをもったオブジェクトであるサークルエンティティを用意する。

```php
class Circle
{
    private CircleId $id;
    private CircleName $name;
    private User $owner;
    private array $members;
    
    public function __construct(CircleId $id, CircleName $name, User $owner, array $members)
    {
        $this->id = $id;
        $this->name = $name;
        $this->owner= $owner;
        $this->members = $members;
    }
    
    public function getId() :CircleId
    {
        return $this->id;
    }
    
    public function getName() :CircleName
    {
        return $this->name;
    }
    
    public function getOwner() :User
    {
        return $this->owner;
    }
    
    public function getMembers() :array
    {
        return $this->members;
    }
}
```

サークルにはサークルのオーナーになるユーザーを表す`owner`と所属しているユーザーの一覧を表す`members`が定義されている。

次にサークルの永続化を行うために必要となるリポジトリを用意する。

```php
interface ICircleRepository
{
    public function Save(Circle $circle) :void;
    public function FindById(CircleId $id) :Circle;
    public function FindByName(CircleName $name) :Circle;
}
```

ユースケースのロジックを組み立てる分には、このインターフェースを実装したクラスを定義することはまだ不要で、まずはロジックを組み立てることに集中する。

サークルを静止絵するファクトリも同じように準備する。

```php
interface ICircleFactory
{
    public function create(CircleName $name, User $owner);
}
```

サークルはユーザー名が重複していないかを確認する必要がある。

重複に関するふるまいを`Circle`クラスに定義すると違和感が生じるため、ドメインサービスとして定義する

```php
class CircleService
{
    private ICircleRepository $circleRepository;
    
    public function __construct(ICircleRepository $circleRepository)
    {
        $this->circleRepository = $circleRepository;
    }
    
    public function exists(Circle $circle) :bool
    {
        $duplicated = $this->circleRepository->find($circle->name);
        return $duplicated != null;
    }
}
```

以上で値オブジェクトからドメインサービスまで一通りのオブジェクトの用意が終わり、必要最低限の準備が整った。

これらを取りまとめてユースケースを実現していく。

# ユースケースを組み立てる

サークルを作成する処理を実装する。まずコマンドオブジェクトを準備する。

```php
class CircleCreateCommand
{
    public string $userId;
    public string $name;
    
    public function __construct(string $userId, string $name)
    {
        $this->userId = $userId;
        $this->name = $name;
    }
}
```

クライアントではこのコマンドオブジェクトを使ってサークルを作成するユーザー（サークルのオーナー）のIDと作成しようとしているサークルの名前を指定する。

続いてコマンドを受け取って実際に処理を行うサークル作成処理を実装する。

```php
class CircleApplicationService
{
    private ICircleFactory $circleFactory;
    private ICircleRepository $circleRepository;
    private CircleService $circleService;
    private IUserRepository $userRepository;
   
    public function __construct(ICircleFactory $circleFactory, ICircleRepository $circleRepository, CircleService $circleService, IUserRepository $userRepository)
    {
        $this->circleFactory = $circleFactory;
        $this->circleRepository = $circleRepository;
        $this->circleService = $circleService;
        $this->userRepository = $userRepository;
    }
    
    public function create(CircleCommand $command)
    {
        $ownerId = new UserId($command->userId);
        $owner = $this->userRepository->find($ownerId);
        if ($owner === null) {
            throw new UserNotFoundException($ownerId, "サークルのオーナーとなるユーザーが見つかりませんでした");
        }
            
        $name = new CircleName($command->name);
        $circle = $this->circleFactory->create($name, $owner);    
        
        if ($this->circleService->exists($circle))
        {
            throw new CannotRegisterCircleException($circle, "サークルは既に存在しています");
        }
        
        $this->circleRepository->save($circle);
    }
}
```

サークルを作成するためにまず最初にサークルのオーナーとなるユーザーを検索する。

ユーザーの存在を確認できたらサークルを生成し、重複確認を行っている。重複しないことが確認できたらリポジトリに永続化を依頼し処理を完了する。

次にこの`CircleApplicationService`にユーザーがサークルに参加するための処理を追加する。

```php
class CircleJoinCommand
{
    public string $userId;
    public string $circleId;

    public function __construct(string $userId, string $circleId)
    {
        $this->userId = $userId;
        $this->circleId = $circleId;
    }
}
```

サークルに参加するユーザーのIDと参加先のサークルIDを指定することで、どのユーザーがどのサークルに参加するかを指定する。

```php
class CircleApplicationService
{
    // <snip>

    public function join(CircleJoinCommand $command) 
    {
        $memberId = new UserId($command->userId);
        $member = $this->userRepository->find($memberId);
        if ($member === null) {
            throw new UserNotFoundException($memberId, "ユーザーが見つかりませんでした");
        }
        
        $circleId = new CircleId($command->circleId);
        $circle = $this->circleRepository->find($circleId);
        if ($circle === null) {
            throw new CircleNotFoundExceptin($circleId, "サークルが見つかりませんでした");
        }
        
        // サークルのオーナーを含めて30名未満か確認する
        if ($circle->members->count >= 29) {
            throw new CircleFullExceptin($circleId);
        }
        
        // メンバーを追加する
        $circle->members->add($member);
        $this->circleRepository->save($circle);
    }
}
```

サークル参加処理ではサークルに参加しようとしているユーザーを検索し、参加先のサークルを検索する。

そして「サークルに所属するユーザーの最大数はサークルのオーナー含めて30名まで」というルールに適合しているかを確認し、サークルのメンバーとしてユーザーを追加している。

この実装にはひとつ問題があり、それは`if ($circle->members->count >= 29) {`の記述。

例えばサークルの勧誘というユースケースが増えた場合、そこにも同様の判定が必要になりチェックする処理が点在してしまう。

本来はドメインオブジェクトに記述すべきで、この問題を解決するのが`集約`という考え方。