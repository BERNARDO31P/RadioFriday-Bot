<?php
set_time_limit(0);
error_reporting(error_reporting() & ~E_NOTICE);

if ($argc < 2)
    die("Usage: php " . $argv[0] . " {start|restart|stop}\n");

switch ($argv[1]) {
    case "start":
        if (exec("which composer") == "")
            system("apt install composer -y");

        system("composer validate");
        system("composer update");
        system("composer install");

        require_once(__DIR__ . "/vendor/autoload.php");
        require_once(__DIR__ . "/assets/lib/ts3admin.class.php");
        require_once(__DIR__ . "/assets/lib/bot.class.php");

        $config = parse_ini_file(__DIR__ . "/assets/config/bot.ini", true);
        $functions = array("afkMover", "supportNotify", "chatSystem", "getClients");

        $query = new ts3admin($config['connection']['host'], $config['connection']['queryPort']);
        $query->connect();
        $query->login($config['connection']['username'], $config['connection']['password']);
        $query->selectServer($config['connection']['port']);
        $query->setName("CloudBot");

        $bot = new bot($query);
        echo "\nStarting the bot!\n";

        foreach ($functions as $function) {
            $pid = pcntl_fork();
            if ($pid == 0) {
                switch ($config['functions'][$function]) {
                    case "false":
                        echo "\033[31mDisabled\033[37m the " . $function . " function\n";
                        break;
                    case "true":
                        echo "\033[32mEnabled\033[37m the " . $function . " function\n";
                    default:
                        $bot->$function();
                        break;
                }
                exit();
            }
        }

        file_put_contents(__DIR__ . "/.pids", json_encode($bot->getPids()));
        break;
    case "stop":
        if (!file_exists(__DIR__ . "/.pids"))
            die ("The bot isn't running\n");

        $pids = json_decode(file_get_contents(__DIR__ . "/.pids"), true);
        foreach ($pids as $pid)
            posix_kill($pid, SIGKILL);

        unlink(__DIR__ . "/.pids");
        echo "The bot has been stopped\n";
        break;
    case "restart":
        $pid = pcntl_fork();
        if ($pid == 0) {
            system("php " . $argv[0] . " stop");
            system("php " . $argv[0] . " start");
            exit();
        }
        sleep(5);
        break;
    default:
        die("Wrong usage.");
        break;
}
