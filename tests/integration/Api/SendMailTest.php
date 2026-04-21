<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Mailing\Tests\integration\Api;

use Carbon\Carbon;
use Flarum\Group\Group;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class SendMailTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    /**
     * @var TestHandler|null
     */
    protected $logHandler;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-mailing');

        // Capture sent mail through the log mail driver
        $this->setting('mail_driver', 'log');
        $this->setting('forum_title', 'TestForum');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
            ],
        ]);
    }

    /**
     * Swap Flarum's logger for an in-memory Monolog TestHandler so mail output
     * routed through FlarumLogTransport can be inspected in assertions.
     *
     * Also explicitly registers this extension's locale files on the LocaleManager.
     * The test framework resolves LocaleManager during extension enable() (in the
     * Locales extender's onEnable lifecycle hook), which fires before the extender's
     * resolving() callback is registered, so the extension's locale files are never
     * loaded in tests without an explicit registration here.
     */
    protected function captureMail(): void
    {
        $this->logHandler = new TestHandler();
        $logger = new Logger('test', [$this->logHandler]);

        $container = $this->app()->getContainer();
        $container->instance('log', $logger);
        $container->instance(LoggerInterface::class, $logger);

        $locales = $container->make(\Flarum\Locale\LocaleManager::class);
        $locales->addTranslations('en', __DIR__.'/../../../resources/locale/en.yml');
        $locales->clearCache();
    }

    /**
     * @return array[] Each record has 'level', 'message', 'context', ...
     */
    protected function sentMails(): array
    {
        // FlarumLogTransport writes each message at info level
        return array_values(array_filter(
            $this->logHandler->getRecords(),
            fn ($r) => str_contains($r['message'] ?? '', 'To: ') || str_contains($r['message'] ?? '', 'Subject:')
        ));
    }

    /**
     * Seed a custom group with the given permission and assign user 2 to it.
     */
    protected function grantPermission(string $permission): void
    {
        $this->prepareDatabase([
            'groups' => [
                ['id' => 100, 'name_singular' => 'Mailer', 'name_plural' => 'Mailers'],
            ],
            'group_user' => [
                ['user_id' => 2, 'group_id' => 100],
            ],
            'group_permission' => [
                ['group_id' => 100, 'permission' => $permission, 'created_at' => Carbon::now()->toDateTimeString()],
            ],
        ]);
    }

    /**
     * @test
     */
    public function guest_cannot_send_mail()
    {
        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'json' => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '1']],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        // Flarum responds 400 when a guest actor hits assertCan() on this custom endpoint
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_without_permissions_cannot_send_to_individual()
    {
        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_without_permissions_cannot_send_to_group()
    {
        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'groups', 'id' => (string) Group::MEMBER_ID]],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_mail_individual_can_send_to_individual()
    {
        $this->grantPermission('fof-mailing.mail-individual');
        $this->captureMail();

        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode((string) $response->getBody(), true);
        $this->assertEquals(1, $body['recipientsCount']);

        $mails = $this->sentMails();
        $this->assertCount(1, $mails);
        $this->assertStringContainsString('To: normal@machine.local', $mails[0]['message']);
        $this->assertStringContainsString('Subject: [TestForum] Hi', $mails[0]['message']);
        $this->assertStringContainsString('Hello', $mails[0]['message']);
    }

    /**
     * @test
     */
    public function user_with_mail_individual_cannot_send_to_group()
    {
        $this->grantPermission('fof-mailing.mail-individual');

        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'groups', 'id' => (string) Group::MEMBER_ID]],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_mail_all_can_send_to_group()
    {
        $this->grantPermission('fof-mailing.mail-all');

        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'groups', 'id' => (string) Group::MEMBER_ID]],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_mail_all_cannot_send_to_individual_only()
    {
        $this->grantPermission('fof-mailing.mail-all');

        // Recipients contain only individual users — controller requires mail-individual for this
        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function plain_text_mail_uses_text_plain_content_type()
    {
        $this->grantPermission('fof-mailing.mail-individual');
        $this->captureMail();

        $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => 'Plain',
                        'text'       => 'Hello world',
                        'asHtml'     => false,
                    ],
                ],
            ])
        );

        $mails = $this->sentMails();
        $this->assertCount(1, $mails);
        $this->assertStringContainsString('Content-Type: text/plain', $mails[0]['message']);
        $this->assertStringNotContainsString('Content-Type: text/html', $mails[0]['message']);
    }

    /**
     * @test
     */
    public function html_mail_uses_text_html_content_type()
    {
        $this->grantPermission('fof-mailing.mail-individual');
        $this->captureMail();

        $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => 'HTML',
                        'text'       => '<p>Hello <b>world</b></p>',
                        'asHtml'     => true,
                    ],
                ],
            ])
        );

        $mails = $this->sentMails();
        $this->assertCount(1, $mails);
        $this->assertStringContainsString('Content-Type: text/html', $mails[0]['message']);
    }

    /**
     * @test
     */
    public function empty_subject_falls_back_to_default_subject_translation()
    {
        $this->grantPermission('fof-mailing.mail-individual');
        $this->captureMail();

        $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'recipients' => [['type' => 'users', 'id' => '2']],
                        'subject'    => '',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $mails = $this->sentMails();
        $this->assertCount(1, $mails);
        $this->assertStringContainsString('Subject: [TestForum] Message from forum administration', $mails[0]['message']);
    }

    /**
     * @test
     */
    public function admin_with_no_recipients_gets_validation_error()
    {
        $response = $this->send(
            $this->request('POST', '/api/admin-mail', [
                'authenticatedAs' => 1,
                'json'            => [
                    'data' => [
                        'recipients' => [],
                        'subject'    => 'Hi',
                        'text'       => 'Hello',
                    ],
                ],
            ])
        );

        $this->assertEquals(422, $response->getStatusCode());
    }
}
