# ソフトウェアに求められるユーザーインターフェース

ソフトウェアの利用者はユーザーインターフェースを通してアプリケーションを利用するため、ソフトウェアとして成り立たせるためにはユーザーインターフェースが必要。

文字列によって指示を出すCLI(コマンドラインインターフェース)や操作対象がグラフィックによって表現されるGUI(グラフィカルユーザーインターフェース)など。

ユーザーインターフェースとして採用するのがCLIであったりGUIであったとしてもドメイン駆動設計の恩恵は受けることが可能。

# CLIに組み込む

依存関係の登録をIoC Containerを利用して行う。`ServiceCollection`はC#のライブラリで、言語にあわせてライブラリを使う。

```php
$program = new Program();
$program->startup();

class Program
{
    private static ServiceProvider $serviceProvider;
    
    public static function startup(): void
    {
        // IoC Container
        static::$serviceProvider = new ServiceCollection();
        
        // 依存関係の登録を行う
        $serviceCollection->addSingleton(IUserRepository::class, InMemoryUserRepository::class);
        $serviceCollection->addTransient(UserService::class);
        $serviceCollection->addTransient(UserApplicationService::class);
        
        // 依存関係を行うプロバイダの生成
        $serviceProvider = $serviceCollection.buildServiceProvider();
    }
}
```

## メイン処理を実装する

```php
Program::startup();
$program = new Program();
$program->execute();

class Program
{
    public function execute() 
    {
        while (true) {
            echo "Input user name\n";
            echo ">\n";
            $input = trim(fgets(STDIN));
             
            $userApplicationService = self::$serviceProvider->getService(UserApplicationService::class);
            $command = new UserRegisterCommand($input);
            $userApplicationService->register($command); 

            echo "------------------------\n";
            echo "user created.\n";
            echo "user name.\n";
            echo "- {$input}\n";
            echo "------------------------\n";
             
            echo "continue(y)?.\n";
            echo ">.\n";

            $yesOrNo = trim(fgets(STDIN));
            if ($yesOrNo !== 'y') {
                break;
            }
        }
    }
}
```

IoC Container(ServiceProvider)から`UserApplicationService`を取得し、ユーザー登録処理を呼び出す。 インスタンスを直接生成せずにIoC
Container経由でインスタンスを取得することで、スタートアップスクリプトに依存関係に関する記述を集中させることが可能。

# MVCフレームワークに組み込む

## 依存関係を設定する

`割愛`

## コントローラを実装する

多くのMVCフレームワークはIoC Containerと連携しており、IoC Containerに登録されたオブジェクトをコンストラクタで受け取ることができる。
`UserApplicationService`を利用したい場合、以下のようにコンストラクタで受け取り、アクションから呼び出すように記述する。

```php
class UserController
{
    private UserApplicationService $userApplicationService;
    
    public function __construct(UserApplicationService $userApplicationService)
    {
        $this->userApplicationService = $userApplicationService;
    }
    
    public function register(UserRegisterRequest $request)
    {
        $command = new UserRegisterCommand($request->userName);
        $this->userApplicationService->register($command);
    }
    
    public function get(UserGetRequest $request) :UserData
    {
        $command = new UserGetCommand($request->userId);
        return $this->userApplicationService->get($command);
    }

    public function update(UserUpdateRequest $request)
    {
        $command = new UserUpdateCommand($request->userId, $request->userName);
        $this->userApplicationService->update($command);
    }

    public function delete(UserDeleteRequest $request)
    {
        $command = new UserDeleteCommand($request->userId);
        $this->userApplicationService->delete($command);
    }
}
```

いずれのアクションも、コントローラはフロントからのデータをビジネスロジックが必要とする入力データへ変換する作業に集中している。

ビジネスロジックをアプリケーションサービスに寄せるようになると、結果としてシンプルなものになる。

### コントローラの責務

コントローラの責務は入力への変換で、コントローラはユーザーからの入力をモデルが要求するメッセージに変換し、モデルに伝えることが責務。

もしもそれ以上のことをこなしているのであれば、ドメインの重要な知識やロジックがコントローラに漏れ出している可能性を疑うべき。

# ユニットテストを書く

割愛