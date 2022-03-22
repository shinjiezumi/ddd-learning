# ソフトウェアに求められるユーザーインターフェース

ソフトウェアの利用者はユーザーインターフェースを通してアプリケーションを利用するため、ソフトウェアとして成り立たせるためにはユーザーインターフェースが必要。

文字列によって指示を出すCLI(コマンドラインインターフェース)や操作対象がグラフィックによって表現されるGUI(グラフィカルユーザーインターフェース)など。

ユーザーインターフェースとして採用するのがCLIであったりGUIであったとしてもドメイン駆動設計の恩恵は受けることが可能。

# CLIに組み込む

依存関係の登録をIoC Containerを利用して行う。`ServiceCollection`はC#のライブラリで、言語にあわせてライブラリを使う。

```php
class Program
{
    private static ServiceProvider $serviceProvider;
    
    public function main() 
    {
        self::startup();
    }
    
    private static function startup :void
    {
        // IoC Container
        $serviceCollection = new ServiceCollection();
        
        // 依存関係の登録を行う
        $serviceCollection->addSingleton(IUserRepository::class, InMemoryUserRepository::class);
        $serviceCollection->addTransient(UserService::class);
        $serviceCollection->addTransient(UserApplicationService::class);
        
        // 依存関係を行うプロバイダの生成
        $serviceProvider = $serviceCollection.buildServiceProvider();
    }
}
```