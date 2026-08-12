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

use Flarum\Extend;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use FoF\Mailing\Jobs\SendMail;
use Illuminate\Queue\QueueRoutes;
use PHPUnit\Framework\Attributes\Test;

/**
 * A mailing pushes one SendMail per recipient, so on the default queue every
 * worker in the pool opens an SMTP connection at once. Admins need to be able
 * to route these onto a dedicated queue with a small worker count to cap
 * concurrent connections to their mail relay — see issue #2.
 *
 * Core resolves the queue by class hierarchy when a job is pushed, which only
 * applies to jobs extending Flarum\Queue\AbstractJob.
 */
class QueueRoutingTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-mailing');
    }

    protected function routes(): QueueRoutes
    {
        return $this->app()->getContainer()->make('queue.routes');
    }

    #[Test]
    public function the_job_extends_the_core_base_job()
    {
        $this->app();

        $this->assertInstanceOf(
            \Flarum\Queue\AbstractJob::class,
            new SendMail('a@b.local', 'a', 'subject', 'text'),
            'SendMail must extend AbstractJob for core to resolve a queue route for it'
        );
    }

    #[Test]
    public function the_job_is_unrouted_by_default()
    {
        $this->app();

        $this->assertNull(
            $this->routes()->getQueue(new SendMail('a@b.local', 'a', 'subject', 'text')),
            'Without an explicit route the job should fall through to the driver default'
        );
    }

    #[Test]
    public function the_job_can_be_routed_onto_a_dedicated_queue()
    {
        $this->extend(
            (new Extend\Queue())->route(SendMail::class, 'emails')
        );

        $this->app();

        $this->assertSame(
            'emails',
            $this->routes()->getQueue(new SendMail('a@b.local', 'a', 'subject', 'text'))
        );
    }

    /**
     * Routing a base class covers its subclasses, so an operator can send every
     * Flarum job to one queue and still give mailings their own.
     */
    #[Test]
    public function a_route_on_the_base_class_also_covers_this_job()
    {
        $this->extend(
            (new Extend\Queue())->route(\Flarum\Queue\AbstractJob::class, 'background')
        );

        $this->app();

        $this->assertSame(
            'background',
            $this->routes()->getQueue(new SendMail('a@b.local', 'a', 'subject', 'text')),
            'The job should inherit a route registered against AbstractJob'
        );
    }

    #[Test]
    public function a_route_on_the_job_itself_wins_over_one_on_the_base_class()
    {
        $this->extend(
            (new Extend\Queue())
                ->route(\Flarum\Queue\AbstractJob::class, 'background')
                ->route(SendMail::class, 'emails')
        );

        $this->app();

        $this->assertSame(
            'emails',
            $this->routes()->getQueue(new SendMail('a@b.local', 'a', 'subject', 'text')),
            'The most specific route should win'
        );
    }
}
