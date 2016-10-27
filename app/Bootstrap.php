<?php

namespace App;

class Bootstrap extends \Peanut\Bootstrap\Yaml
{
    /**
     * @param $config
     */
    public function initialize(\Phalcon\Mvc\Micro $app)
    {

        $this->initCors();
        $this->initDatabase();
        $this->initSession();
        $this->initFilter();
        $this->initTemplate();
        //$this->initDebug();

    }

    public function initCors()
    {

        $origin = $this->getDi('request')->getHeader('ORIGIN') ? : '*';

        if (strtoupper($this->getDi('request')->getMethod()) == 'OPTIONS') {
            $this->getDi('response')
                ->setHeader('Access-Control-Allow-Origin', $origin)
                ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
                ->setHeader('Access-Control-Allow-Credentials', 'true');

            $this->getDi('response')->setStatusCode(200, 'OK')->send();
            exit;
        }

        $this->getDi('response')
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
            ->setHeader('Access-Control-Allow-Credentials', 'true');

    }

    public function initEnvironment()
    {

        $stageName = getenv('STAGE_NAME');

        if (!$stageName) {
            throw new \Exception('stage를 확인할수 없습니다.');
        }

        $this->stageName = $stageName;

    }

    /**
     * @return \Phalcon\Session
     */
    public function initSession()
    {

        $this->di->setShared('session', function () {
            $session = new \Phalcon\Session\Adapter\Redis([
                'uniqueId'   => 'my-private-app',
                'host'       => 'redis',
                'port'       => 6379,
                'persistent' => false,
                'lifetime'   => 3600,
                'prefix'     => 'my_',
                'index'      => 1,
            ]);
            $session->start();

            return $session;
        });

    }

    /**
     * @return \Phalcon\Filter
     */
    public function initFilter()
    {

        $this->di->setShared('filter', function () {
            $filter = new \Phalcon\Filter();
            $filter->add(\Phalcon\Filter::FILTER_INT, function ($value) {
                return filter_var(filter_var($value, FILTER_SANITIZE_NUMBER_INT), FILTER_VALIDATE_INT);
            });

            return $filter;
        });

    }

    /**
     * @return \Peanut\Template
     */
    public function initTemplate()
    {

        $stage                = $this->getStageName();
        $this->di->setShared('template', function () use ($stage) {
            $tpl            = new \Peanut\Template();
            $tpl->phpengine = true;
            $tpl->notice = false;

            switch ($stage) {
                case 'production':
                    $tpl->compileCheck = false;
                    break;
                case 'staging':
                    $tpl->compileCheck = true;
                    break;
                default:
                    $tpl->compileCheck = 'dev';
            }

            $tpl->compileRoot  = __BASE__.DIRECTORY_SEPARATOR.'.template';
            $tpl->templateRoot = __BASE__.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views';

            return $tpl;
        });

    }

    /**
     * @return array
     */
    public function initDatabase()
    {

        $stageName = $this->stageName;
        $debug     = $this->debug;
        $dbConfig  = $this->getDbConfig();

        $this->di->setShared('databases', function () use ($dbConfig) {
            return $dbConfig;
        });

        if (true === $debug) {
            $this->dbProfiler();
        }

    }

    /**
     * @return array
     */
    private function getDbConfig()
    {

        $dbUrls = json_decode(getenv('DATABASE_URL'), true);

        if (0 === count($dbUrls)) {
            throw new \Exception('DB URL을 확인하세요.');
        }

        $dbConfig = [];

        foreach ($dbUrls as $server => $url) {
            $dbConfig[$server] = $this->dsnParser($url);
        }

        return $dbConfig;

    }

    /**
     * @param $url
     * @return array
     */
    private function dsnParser($url)
    {

        $dbSource = parse_url($url);
        $user     = $dbSource['user'];
        $password = $dbSource['pass'];
        $dsn      = $dbSource['scheme'] . ':host=' . $dbSource['host'] . ';dbname=' . trim($dbSource['path'], '/').';charset=utf8mb4';

        return [
            'dsn'      => $dsn,
            'username' => $user,
            'password' => $password
        ];

    }

    public function initEventsManager()
    {

        $this->di->setShared('eventsManager', function () {
            return new \Phalcon\Events\Manager();
        });

    }

    public function initDbProfiler()
    {

        $this->di->setShared('dbProfiler', function () {
            return new \Phalcon\Db\Profiler();
        });

    }

    public function initDebug()
    {

        $debug = getenv('DEBUG');
        if ($debug == 1) {
            $this->debug = true;
            include_once __BASE__.'/app/helpers/debug.php';
        }

    }

    public function dbProfiler()
    {

        $this->initEventsManager();
        $this->initDbProfiler();

        $eventsManager = $this->getDi('eventsManager');
        $eventsManager->attach('db', function ($event, $connection) {
            $profiler = $this->di['dbProfiler'];

            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($connection->getSQLStatement(), $connection->getSQLVariables(), $connection->getSQLBindTypes());
            }

            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });

        $dbNames = array_keys($this->getDbConfig());
        foreach ($dbNames as $name) {
            \Peanut\Phalcon\Pdo\Mysql::name($name)->setEventsManager($eventsManager);
        }

    }
}
