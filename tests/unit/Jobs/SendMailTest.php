<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Mailing\Tests\unit\Jobs;

use FoF\Mailing\Jobs\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SendMailTest extends TestCase
{
    #[Test]
    public function job_implements_should_queue()
    {
        $job = new SendMail('user@example.com', 'User', 'Subject', 'Body');

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }
}
