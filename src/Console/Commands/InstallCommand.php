<?php

namespace Medz\Fans\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application.';

    /**
     * The console command filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Run the command.
     *
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function handle()
    {
        $config = $this->getLaravel()->make('config');
        $envFile = $this->getLaravel()->environmentFilePath();

        if (! $this->filesystem->exists($envFile)) {
            $this->filesystem->put($envFile, 'APP_KEY=');
        }

        $this->call('key:generate', ['--force' => true]);

        // -------------------
        $appURL = $this->questionAppURL();
        $dbHost = $this->questionDbHost();
        $dbPort = $this->questionDbPort();
        $dbName = $this->questionDbName();
        $dbUser = $this->questionDbUser();
        $dbPassword = $this->questionDbPassword();

        $env = 'APP_KEY='.$config->get('app.key').PHP_EOL.
               'APP_URL='.$appURL.PHP_EOL.
               'DB_CONNECTION='.'mysql'.PHP_EOL.
               'DB_HOST='.$dbHost.PHP_EOL.
               'DB_PORT='.$dbPort.PHP_EOL.
               'DB_DATABASE='.$dbName.PHP_EOL.
               'DB_USERNAME='.$dbUser.PHP_EOL.
               'DB_PASSWORD='.$dbPassword.PHP_EOL;

        $config->set('app.url', $appURL);
        $config->set('app.env', 'local');
        $config->set('database.default', 'mysql');
        $config->set('database.connections.mysql.host', $dbHost);
        $config->set('database.connections.mysql.database', $dbName);
        $config->set('database.connections.mysql.username', $dbUser);
        $config->set('database.connections.mysql.password', $dbPassword == 'null' ? null : $dbPassword);

        $this->filesystem->put($envFile, $env);
        $this->filesystem->delete($this->getLaravel()->basePath('phpwind9/data/install.lock'));
        $this->call('migrate:refresh', ['--seed' => true, '--force' => true]);

        $config->set('app.url', $appURL);
        $url = url('/old/install.php');
        $url = str_replace('http://localhost', $appURL, $url);
        $this->openBrowser($url);

        $this->info('Open the URL and proceed with the installation:');
        $this->alert($url);
    }

    /**
     * opens a url in your system default browser.
     *
     * @param string $url
     */
    private function openBrowser($url)
    {
        $url = ProcessUtils::escapeArgument($url);

        if (windows_os()) {
            $process = new Process('start "web" explorer "'.$url.'"');

            return $process->run();
        }

        $process = new Process('which xdg-open');
        $linux = $process->run();

        $process = new Process('which open');
        $osx = $process->run();

        if ($linux === 0) {
            $process = new Process('xdg-open '.$url);

            return $process->run();
        } elseif ($osx === 0) {
            $process = new Process('open '.$url);

            return $process->run();
        }
    }

    /**
     * 提问 database user password.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionDbPassword()
    {
        $dbPassword = $this->ask('Enter your database user password', 'null');
        if (! $this->confirm(sprintf('Confirm database user password is "%s"?', $dbPassword), false)) {
            return $this->questionDbPassword();
        }

        return $dbPassword;
    }

    /**
     * 提问 database username.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionDbUser()
    {
        $dbUsername = $this->ask('Enter your database username', 'root');
        if (! $this->confirm(sprintf('Confirm database username is "%s"?', $dbUsername), false)) {
            return $this->questionDbUser();
        }

        return $dbUsername;
    }

    /**
     * 提问 database name.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionDbName()
    {
        $dbName = $this->ask('Enter your database name');
        if (! $this->confirm(sprintf('Confirm database name is "%s"?', $dbName), false)) {
            return $this->questionDbName();
        }

        return $dbName;
    }

    /**
     * 提问 database port.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionDbPort()
    {
        $dbPort = $this->ask('Enter your database port', 3306);
        if (! $this->confirm(sprintf('Confirm database port is "%s"?', $dbPort), false)) {
            return $this->questionDbPort();
        }

        return $dbPort;
    }

    /**
     * 提问 database host.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionDbHost()
    {
        $dbHost = $this->ask('Enter your database host', '127.0.0.1');
        if (! $this->confirm(sprintf('Confirm host is "%s"?', $dbHost), false)) {
            return $this->questionDbHost();
        }

        return $dbHost;
    }

    /**
     * 提问 APP_URL.
     *
     * @return string
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function questionAppURL()
    {
        $appURL = $this->ask('Enter your domain name');
        if (! $this->confirm(sprintf('Confirm domain name is "%s"?', $appURL), false)) {
            return $this->questionAppURL();
        }

        return $appURL;
    }
}
