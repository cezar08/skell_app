<?php
namespace Core\Test;

use Zend\Db\Adapter\Adapter;
use Core\Db\TableGateway;
use Zend\Mvc\Application;
use Zend\Di\Di;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\MvcEvent;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \Zend\Mvc\Application
     */
    protected $application;
    
    /**
     * @var \Zend\Di\Di
     */
    protected $di;

    public function setup()
    {
        parent::setup();
        $config = include 'config/application.config.php';
        $config['module_listener_options']['config_static_paths'] = array(getcwd() . '/config/test.config.php');

        if (file_exists(__DIR__ . '/config/test.config.php')) {
            $moduleConfig = include __DIR__ . '/config/test.config.php';
            array_unshift($config['module_listener_options']['config_static_paths'], $moduleConfig);
        }

        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(
                isset($config['service_manager']) ? $config['service_manager'] : array()
        ));
        $this->serviceManager->setService('ApplicationConfig', $config);       
        $moduleManager = $this->serviceManager->get('ModuleManager');
        $moduleManager->loadModules();
        $this->routes = array();
        $not_include = array(
            'SlmQueue', 'SlmQueueDoctrine', 'ZF\Apigility', 'ZF\Apigility\Provider', 'AssetManager',
            'ZF\ApiProblem', 'ZF\MvcAuth', 'ZF\OAuth2', 'ZF\Hal', 'ZF\ContentNegotiation', 'ZF\ContentValidation', 'ZF\Rest',
            'ZF\Rpc', 'ZF\Versioning', 'ZF\DevelopmentMode', 'ZendDeveloperTools', 'DoctrineModule', 'DoctrineORMModule');

        foreach ($moduleManager->getModules() as $m) {
            if (!in_array($m, $not_include))
                $moduleConfig = include __DIR__ . '/../../../../' . ucfirst($m) . '/config/module.config.php';

            if (isset($moduleConfig['router'])) {
                foreach ($moduleConfig['router']['routes'] as $key => $name) {
                    $this->routes[$key] = $name;
                }
            }
        }

        $this->serviceManager->setAllowOverride(true);
        $this->application = $this->serviceManager->get('Application');
        $this->event = new MvcEvent();
        $this->event->setTarget($this->application);
        $module = null;

        if (isset($this->application->getRequest()->getContent()[1])) {
            $param1 = $this->application->getRequest()->getContent()[1];
            $prepare_module = explode('/', $param1);

            if (isset($prepare_module[1]))
                $module = $prepare_module[1];
        }

        if ($module) {
            $folder = getcwd().'/module/'.$module.'/src/'.$module.'/Entity';
            $files = scandir($folder);
            $classes = array();
            foreach ($files as $file) {

                if (strstr($file, '.php')) {
                    if ($file != 'DefaultQueue.php') {
                        $classe = "$module\\Entity\\" . str_replace('.php', '', $file);
                        $classes[] = $this->getEntityManager()->getClassMetadata($classe);
                    }
                }

            }
        }else{
            $classes = $this->getEntityManager()
            ->getMetadataFactory()
            ->getAllMetadata();
        }

        $this->event->setApplication($this->application)
                ->setRequest($this->application->getRequest())
                ->setResponse($this->application->getResponse());
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->getEntityManager());        
        $tool->createSchema($classes);
    }

    public function tearDown()
    {
        parent::tearDown();
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->getEntityManager());
	    $classes = $this->getEntityManager()
            ->getMetadataFactory()
            ->getAllMetadata();
	    $tool->dropSchema($classes);
    }

    /**
     * Retrieve Service
     *
     * @param  string $service
     * @return Service
     */
    protected function getService($service)
    {
        return $this->serviceManager->get($service);
    }

        /**
     * Retrieve EntityManager
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() 
    {
        return $this->getService('Doctrine\ORM\EntityManager');        
    }
}
