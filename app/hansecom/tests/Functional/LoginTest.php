<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class LoginTest extends WebTestCase
{
    protected function setUp(): void
    {
        $client = static::createClient();
        $kernel = $client->getKernel();
        $application = new Application($kernel);
    
        $commands = [
            'doctrine:database:drop' => ['--force' => true],
            'doctrine:database:create' => ['--if-not-exists' => true],
            'doctrine:migrations:migrate' => ['--no-interaction' => true],
            'doctrine:fixtures:load' => ['--no-interaction' => true],
        ];
        
        foreach ($commands as $commandName => $arguments) {
            $command = $application->find($commandName);
            $commandTester = new CommandTester($command);
            $commandTester->execute($arguments, ['capture_stderr_separately' => true]);

            dump($commandTester);
        }
    }

    public function testLogin(): void
    {
        $client = static::getClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="login_form"]');

        $form = $crawler->filter('button#login')->form([
            '_username' => 'testuser@example.com',
            '_password' => 'testpassword',
        ]);

        $client->submit($form);
        $client->followRedirect();

        //dump($client);
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table#all_quotes');
    }

}
