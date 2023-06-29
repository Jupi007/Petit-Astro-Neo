<?php

declare(strict_types=1);

use App\Tests\Common\PageTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class HomepageTest extends SuluTestCase
{
    use PageTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createWebsiteClient();
        $this->purgeDatabase();
        $this->initPhpcr();
    }

    public function testHomepage(): void
    {
        $this->createPage(
            template: 'homepage',
            webspaceKey: 'petit-astro',
            data: [
                'title' => 'Accueil',
                'url' => '/accueil',
                'published' => true,
            ],
        );

        $this->client->request('GET', '/fr/accueil');
        $this->assertResponseIsSuccessful();
    }

    protected static function getDocumentManager(): DocumentManagerInterface
    {
        return static::getContainer()->get('sulu_document_manager.document_manager');
    }
}
