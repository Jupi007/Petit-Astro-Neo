<?php

declare(strict_types=1);

namespace App\Tests\Functional\Pages;

use App\Tests\Common\PageTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

class DefaultTest extends SuluTestCase
{
    use PageTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createWebsiteClient();
        $this->purgeDatabase();
        $this->initPhpcr();
    }

    public function testDefault(): void
    {
        $this->createPage(
            template: 'default',
            webspaceKey: 'petit-astro',
            data: [
                'title' => 'Test',
                'url' => '/test',
                'published' => true,
                'coverSize' => 'small',
            ],
        );

        $this->client->request(Request::METHOD_GET, '/fr/test');
        $this->assertResponseIsSuccessful();
    }

    protected static function getDocumentManager(): DocumentManagerInterface
    {
        return static::getContainer()->get('sulu_document_manager.document_manager');
    }
}
