# 仕様とは

オブジェクトの評価は単純なものであればメソッドとして定義されるが、全ての評価が単純な処理とは限らない。評価処理にはオブジェクトのメソッドとして定義されるには似つかわしくないものも存在する。

そういった複雑な評価の手順は、アプリケーションサービスに記述されることが多いが、オブジェクトの評価はドメインの重要なルールでサービスに記述されてしまうことは問題。

この対策として挙げられるのが仕様で、仕様はあるオブジェクトがある評価基準に達しているかを判定するオブジェクト。

## 複雑な評価処理

あるオブジェクトがある特定の条件に従っているかを評価する処理は、オブジェクトのメソッドとして定義される。

```php
class Circle
{
    public function isFull() :bool 
    {
        return $this->countMembers() + 1;
    }
}
```

単純じゃ条件であれば問題ない。 ただ、例えばサークルの人数上限がユーザーの種別によって変動する場合のルールは以下のとおり。

- ユーザーにはプレミアムユーザーという種別がある
- サークルに所属するユーザーの最大数はサークルのオーナー含めて30名まで
- プレミアムユーザーが10名以上所属している場合、メンバーの最大数が50名まで引き上げられる。

`Circle`はサークルに所属するメンバーを保持しているが、`UserId`のリストを保持しているにすぎず、プレミアムユーザーが何名存在するかはユーザーのリポジトリに問い合わせる必要がある。

しかし`Circle`はユーザーのリポジトリは保持していない。そのためアプリケーションサービス上で判定すると以下のようになる。

```php
class CircleApplicationService
{
    private ICircleRepository $circleRepository;
    private IUserRepository $userRepository;
    
    public function join(CircleJoinCommand $command)
    {
        $circleId = new CircleId($command->circleId);
        $circle = $this->circleRepository->find($circleId);
        
        $users = $this->userRepository->find($circle->memberIds);
        // サークルに所属しているプレミアムユーザーの人数により上限が変わる
        $countPremiumUser = function ($users) int {
            $count = 0;
            foreach ($users as $user) {
                if ($user->isPremium) {
                    $count++;
                }
            }
            return $count;
        }
        $premiumUserCount = $countPremiumUser($users);
        $circleUpperLimit = $premiumUserCount < 10 ? 30 : 50;
        return $this->countMembers() >= $circleUpperLimit;
    }
}
```

本来サークルが満員かどうかの確認はドメインのルールで、サービスにドメインのルールに基づくロジックを記述することは避けなくてはならない。

ドメインのルールはドメインオブジェクトに定義すべき。

ただ、ドメインオブジェクトに定義するとなると、`Circle`はメンバーのIDしか持っておらずリポジトリを渡す必要が出てきてしまう。

エンティティや値オブジェクトはドメインモデルの表現に専念すべきで、リポジトリを操作することは避けないければならない。

## 仕様による解決

エンティティや値オブジェクトにリポジトリを操作させないためにとられる手段は「仕様」と呼ばれるオブジェクトを利用した解決で、以下のようになる。

```php
class CircleFullSpecification
{
    private IUserRepository $userRepository;
    
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    } 
    
    public function isSatisfiedBy(Circle $circle): bool 
    {
        $users = $this->userRepository->find($circle->memberIds);
        $countPremiumUser = function ($users) int {
            $count = 0;
            foreach ($users as $user) {
                if ($user->isPremium) {
                    $count++;
                }
            }
            return $count;
        }
        $premiumUserCount = $countPremiumUser($users);
        $circleUpperLimit = $premiumUserCount < 10 ? 30 : 50;
        return $this->countMembers() >= $circleUpperLimit;
    }
}
```

仕様はオブジェクトの評価のみ行う。複雑な評価手順をオブジェクトに埋もれさせずに切り出すことでその趣旨は明確になる。

仕様を利用した時のサークルメンバー追加処理は以下のようになる。

```php
class CircleApplicationService
{
    private ICircleRepository $circleRepository;
    private IUserRepository $userRepository;
    
    public function join(CircleJoinCommand $command) 
    {
        $circleId = new CircleId($command->circleId);
        $circle = $this->circleRepository->find($circleId);
        $circleFullSpecification = new CircleFullSpecification($this->userRepository);
        if ($circleFullSpecification->isSatisfiedBy($circle)) {
            throw new CircleFullException($circleId);
        }
        
        // snip
    }
}
```

複雑な評価手順はカプセル化され、コードの意図は明確になる。

## リポジトリの使用を避ける

仕様はれっきとしたドメインオブジェクトであり、その内部でリポジトリを操作することを避ける考え方もある。

```php
class CircleMembers
{
    private CircleId $id;
    private User $owner;
    private array $members;
    
    public function __construct(CircleId $id, User $owner, array $members)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->members = $members;
    }
    
    public function countMembers(): int 
    {
        return count($this->memberIds) + 1;
    }
    
    public function countPremiumMembers(bool $containsOwner): int 
    {   
        $premiumUserCount = $this->countPremiumMembers();
        if ($containsOwner) {
            return $premiumUserCount + ($this->owner->isPremium ? 1 : 0);
        } else {
            return $premiumUserCount;
        }
    }
    
    public function countPremiumUser(): int
    {
        $count = 0;
        foreach ($this->members as $user) {
            if ($user->isPremium) {
                $count++;
            }
        }
        return $count;
    }
}
```

`CircleMembers`は汎用的なリストではなくメンバー情報全てを保持していて、かつ独自の計算処理をメソッドとして定義できる。

よって仕様とサービスは以下のようになる。

```php
class CircleFullSpecification
{
    public function isSatisfiedBy(CircleMembers $members): bool 
    {
        $premiumUserCount = $members->countPremiumMembers(false);
        $circleUpperLimit = $premiumUserCount < 10 ? 30 : 50;
        return $this->countMembers() >= $circleUpperLimit;
    }
}
```

```php
class CircleApplicationService
{
    private ICircleRepository $circleRepository;
    private IUserRepository $userRepository;
    
    public function join(CircleJoinCommand $command) 
    {
        $circleId = new CircleId($command->circleId);
        $circle = $this->circleRepository->find($circleId);
        $members = new CircleMembers($circle->id, $circle->owner, $circle->members)
        $circleFullSpecification = new CircleFullSpecification($members);
        if ($circleFullSpecification->isSatisfiedBy($circle)) {
            throw new CircleFullException($circleId);
        }
        
        // snip
    }
}
```

# 仕様とリポジトリを組み合わせる

仕様は単独で取り扱う以外にも、リポジトリと組み合わせて活用する手法が存在する。 それはリポジトリに仕様を引き渡して、仕様に合致するオブジェクトを検索する手法。

リポジトリには検索を行うメソッドが定義されるが検索処理の中には重要なルールを含むものが存在する。こうした検索処理をリポジトリのメソッドとして定義してしまうと、重要なルールはリポジトリの実装クラスに記述されてしまう。

そういったとき、重要なルールを仕様オブジェクトとして定義し、リポジトリに引き渡せば重要なルールが実装クラスに漏れ出すことを防げる。

## おすすめサークルに見る複雑な検索処理

おすすめサークルの検索機能を例に考えてみる。 まずはおすすめサークルの定義を考える。

- 直近1ヶ月以内に結成されたサークルである
- 所属メンバー数が10名以上である

これまでユーザーやサークルの検索を実質的に行ってきたのはリポジトリだったので、おすすめサークル検索機能も同様に定義してみる。

```php
interface ICircleRepository
{
    public function findRecommended(DateTime $now);
}
```

`findRecommended`メソッドは引き渡された日付に従って最適なサークルを見繕ってくれるメソッドで、アプリケーションサービスはこれを利用する。

```php
class CircleApplicationService
{
    public function GetRecommend(CircleGetRecommendRequest $request): CircleGetRecommendResult
    {
        $recommendedCircle = $this->circleRepository->findRecommended($now);
        return new CircleGetRecommendResult($recommendedCircle);
    }
}
```

処理自体は正しく動作するが、おすすめサークルの検索条件がリポジトリの実装クラスに依存してしまっている問題がある。

おすすめサークルの条件は重要なルールで、リポジトリの実装クラスに左右されることは推奨されない。

## 仕様による解決

ドメインの重要な知識はできる限りドメインオブジェクトとして定義すべき。

```php
class CircleRecommendSpecification
{
    private DateTime $date;
    
    public function __construct(DateTime $date)
    {
        $this->date = $date;
    }
    
    public isSatisfiedBy(Cricle $circle): bool 
    {
        if ($circle->countMembers() < 10) {
            return false;
        }

       // イメージ        
        return $circle->created > $this->date->add('1D');
    }
}
```

```php
class CircleApplicationService
{
    private ICircleRepository $circleRepository;
    
    public function GetRecommend(CircleGetRecommendRequest $request): CircleGetRecommendResult
    {
        $recommendedCircleSpec = new CircleRecommendSpecification($now);
        
        $circles = $this->circleRepository->findAll();
        // 仕様を使っておすすめサークルを検索。詳細割愛
        // $recommendedCircle = xx
        return new CircleGetRecommendResult($recommendedCircle);
    }
}
```

このようにすればおすすめサークルの条件をリポジトリに記述する必要がなくなる。

