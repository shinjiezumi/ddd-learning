# アーキテクチャの役目

## アンチパターン：利口なUI
利口なUIは本来であればドメインオブジェクトに記載されるべき重要なルールやふるまいが、ユーザーインターフェースに記述されてしまっている状態で、ドメインを分離することが適わなかったアプリケーションに多く見られる。

そうしたシステムは改良に対するコストが異常に高くなってしまう。

## ドメイン駆動設計がアーキテクチャに求めること
利口なUIを避けることを決めたとしても容易ではない。ビジネスロジックを正しい場所に配置し続けることは、いかにその大切さを熟知している開発者であっても難しい。

それゆえ開発車に強い自制心を促す以外の方法を考える必要があり、アーキテクチャはその解決策になる。

アーキテクチャは方針で、何がどこに記述されるべきかといった疑問に対する回答を明確に、ロジックが無秩序に点在することを防ぐ。

開発者はアーキテクチャが示す方針にしたがうことで、「何をどこに書くのか」に振り回されないようになる。これは開発者がドメイン駆動設計の本質である「ドメインをとらえ、うまく表現する」ことに集中するために必要なこと。

ドメイン駆動設計がアーキテクチャに求めることは、ドメインオブジェクトが渦巻くレイヤーを隔離して、ソフトウェア特有の事情からドメインオブジェクトを防衛すること。

それを可能にするのであれば、アーキテクチャがどのようなものであっても構わない。

# アーキテクチャの解説
ドメイン駆動設計と同時に語られることの多いアーキテクチャは以下のとおり。

- レイヤードアーキテクチャ
- ヘキサゴナルアーキテクチャ
- クリーンアーキテクチャ

ドメイン駆動設計にとってはドメインが隔離されることのみが重要であり、必ずしもいずれかのアーキテクチャにしたがわなければいけないというわけではない。

またアーキテクチャにしたがったからといって、それすなわちドメイン駆動設計を実践したことにはならない。重要なのは、ドメインの本質に集中すること。

## レイヤードアーキテクチャ
ドメイン駆動設計の文脈で登場するアーキテクチャの中で、もっとも伝統的で有名なアーキテクチャで、その名のとおりいくつかの層が積み重なる形で表現される。

<img src="./04_レイヤードアーキテクチャ.jpg">

4つの層で構成されたものが代表的で、層の内訳は以下のとおり。

- プレゼンテーション層(ユーザーインターフェース層)
- アプリケーション層
- ドメイン層
- インフラストラクチャ層

### ドメイン層
この中でもっとも重要なそうで、ソフトウェアを適用しようとしている領域で問題解決に必要な知識を表現する。この層を明示的にして、ドメイン層に本来所属すべきドメインオブジェクトの隔離を促し、他の層へ流出しないようにする

### アプリケーション層
ドメイン層の住人を取りまとめる層でアプリケーションサービスが挙げられる。アプリケーションサービスはドメインオブジェクトの直接のクライアントとなり、ユースケースを実現するための進行役になる。

ドメイン層の住人はドメインの表現に徹しているので、アプリケーションとして成り立たせるためには彼らを問題解決に導く必要がある。そのような働きをするアプリケーション層はまさにドメイン層の住人を取りまとめる存在。

### プレゼンテーション層
ユーザーインターフェースとアプリケーションを結びつける。主な責務は表示と解釈で、システムの利用車にわかるように表示を行い、システム利用者の入力を解釈する。

ユーザーインターフェースとアプリケーションを結びつけることさえできれば、WebフレームワークであってもCLIであっても良い。

### インフラストラクチャ層

他の層を支える技術的基盤へのアクセスを提供するそうで、アプリケーションのためのメッセージ送信や、ドメインのための永続化を行うモジュールが含まれる。

### 解説

ここに存在する原則は依存の方向が上から下ということで、上位のレイヤーは自身より下位のレイヤーに依存することが許される。逆方向の直接的な依存は許されない。

ドメイン層からインフラストラクチャ層に依存の矢印が伸ばされているのは、ドメイン層のオブジェクトがインフラストラクチャ層のオブジェクトを取り扱うことを意味していない。

白抜きの矢印で汎化が含まれていることがわかり、リポジトリのインターフェースと実装クラスの関係がこの矢印にあたる。

### レイヤードアーキテクチャの実装サンプル(Laravelベース)

#### プレゼンテーション層に所属するコントローラー

```php
class UserController extends Controller
{
    private UserApplicationService $userApplicationService;

    public function __construct(UserApplicationService $userApplicationService)
    {
        $this->applicationService = $userApplicationService;
    }
    
	public function list(Request $request): UserListResponse
	{
    	$users = $this->userApplicationService->getAll();
    	return new UserListResponse($users);
	}

	public function index(UserGetRequest $request): UserGetResponse
	{
	    $command = new UserGetCommand($request->id);
    	$user = $this->userApplicationService->get($command);
    	return new UserGetResponse($user);
	}

	public function store(UserPostRequest $request): UserStoreResponse
	{
	    $command = new UserRegisterCommand($request->name);
    	$user = $this->userApplicationService->register($command);
    	return new UserStoreResponse($user);
	}

	public function update(UserUpdateRequest $request): void
	{
	    $command = new UserUpdateCommand($request->id, $request->name);
    	$this->userApplicationService->update($command);
	}
	
	public function destroy(UserDeleteCommand $request): void
	{
	    $command = new UserDeleteCommand($request->id);
    	$this->userApplicationService->delete($command);
	}
}
```

HTTPリクエストという利用者からの入力データをアプリケーションに伝えるための変換を行うMVCフレームワークのコントローラーは、入力を解釈してアプリケーションに結びつけるプレゼンテーション層の住人。

アプリケーションサービスのクライアントにもなっているので、依存の方向性も守られている。

#### アプリケーション層に所属するアプリケーションサービス

```php
class UserApplicationService
{
    private IUserFactory $userFactory;
    private IUserRepository $userRepository;
    private UserService $userService;
    
    public function __construct(IUserFactory $userFactory, IUserRepository $userRepository, UserService $userService)
    {
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }
    
    public function get(UserGetCommand $command): UserGetResult
    {
        $id = new UserId($command->id);
        $user = $this->userRepository->find($id);
        if ($user === null) {
            throw new UserNotFoundException($id, "ユーザーが見つかりませんでした");
        }
        return new UserGetResult($user);
    }
        
    public function getAll(): UserListResult
    {
        $users = $this->userRepository->findAll();
        return new UserListResult($users);
    }

    public function register(UserRegisterCommand $command): UserRegisterResult
    {
        $name = new UserName($command->name);
        $user = $this->userFactory->create($name);
        if ($this->userService->exists($user)) {
            throw new CannotRegisterUserException($user, 'ユーザーは既に存在しています');
        }
       
        $result = $this->userRepository->save($user); 
        return new UserRegisterResult($result);
    }

    public function update(UserUpdateCommand $command): void
    {
        $id = new UserId($command->id);
        $user = $this->userRepository->find($id);
        if ($user === null) {
            throw new UserNotFoundException($id, "ユーザーが見つかりませんでした");
        }

        $name = new UserName($command->name);
        $user->changeName($name);
        if ($this->userService->exists($user)) {
            throw new CannotRegisterUserException($user, 'ユーザーは既に存在しています');
        }
        
        $result = $this->userRepository->save($user); 
    }
    
    public function delete(UserDeleteCommand $command): void
    {
        $id = new UserId($command->id);
        $user = $this->userRepository->find($id);
        if ($user === null) {
            return;
        }

        $result = $this->userRepository->delete($user);
    }
}
```

アプリケーションサービスはアプリケーション層に所属するオブジェクトで、下位に位置するドメイン層とインフラストラクチャ層に対して依存している。

アプリケーション層は問題を解決するためにドメインオブジェクトが実施するタスクの進行管理を行う。

注意すべきはこのレイヤーにドメインのルールやふるまいを直接記述していはいけないことで、ビジネスの重要なルールはドメイン層に実装すべき。

#### ドメイン層

ユーザーのコード上の表現である`User`クラスやドメインサービスの`UserService`クラスはこの層に所属するオブジェクト

```php
class User
{
    private UserId $id;
    private UserName $name;
    private UserType $type;
    
    public function __construct(UserId $id, UserName $name, UserType $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;    
    }
    
    public function changeName(UserName $name): void
    {
        $this->name = $name;
    }
    
    public function upgrade(): void
    {
        $this->type = UserType::Premium;
    }

    public function downgrade(): void
    {
        $this->type = UserType::Normal;
    }
}

class UserService
{
    private IUserRepository $userRepository;
    
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function exists(User $user): bool
    {
        $duplicateUser = $this->userRepository->find($user->name);
        return $duplicateUser !== null;
    }
}
```

ドメインモデルに表現するコードはすべてこの層に集中する。またドメインオブジェクトをサポートする役割のあるファクトリやリポジトリのインターフェースもこの層に含まれる。

#### インフラストラクチャ

インフラストラクチャ層のオブジェクトは永続化を実施するリポジトリ。

```php
class EFUserRepository implements IUserRepository
{
    public function find(UserId $id): ?User
    {
        return  User::find($id);
    }
    
    // 割愛
}
```

インフラストラクチャ層にはドメインオブジェクトを直接的に支える技術的機能の他に、アプリケーション層やプレゼンテーション層のための技術的機能を担うオブジェクトも含まれる