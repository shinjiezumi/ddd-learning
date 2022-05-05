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

