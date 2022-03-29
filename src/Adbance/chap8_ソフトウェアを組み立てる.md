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