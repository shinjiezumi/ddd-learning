<?php

echo "hoge\n";

if (count($argv) > 2) {
    printf("%s\n", $argv[1]);
}


$users = [1, 2, 1, 1, 2, 1];

$premiumUserCount = function () use ($users): int {
    $count = 0;
    foreach ($users as $user) {
        if ($user === 1) {
            $count++;
        }
    }
    return $count;
};


var_dump($premiumUserCount($users));

return ;

while (1) {
//    echo "入力してください\n";
//    // 標準入力から入力待ち
//    $line = trim(fgets(STDIN));
//    if ($line == "yes") {
//        echo "処理を続行します\n";
//        break;	// 無限ループを抜ける
//    }elseif($line == "no"){
//        echo "処理を終了します\n";
//        exit();
//    }else {
//        echo "....\n";
//    }

    Program::startup();
    $program = new Program();
    $program->execute();
}

class Program
{
    private static ServiceProvider $serviceProvider;

    public static function startup(): void
    {
        // IoC Container
        $serviceCollection = new ServiceCollection();

        // 依存関係の登録を行う
        $serviceCollection->addSingleton(IUserRepository::class, InMemoryUserRepository::class);
        $serviceCollection->addTransient(UserService::class);
        $serviceCollection->addTransient(UserApplicationService::class);

        // 依存関係を行うプロバイダの生成
        $serviceProvider = $serviceCollection . buildServiceProvider();
    }

    public function execute()
    {
        while (true) {
            echo "Input user name\n";
            echo ">\n";
            $input = trim(fgets(STDIN));

            // TODO
        }
    }
}
