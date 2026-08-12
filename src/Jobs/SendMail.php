<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Mailing\Jobs;

use Flarum\Queue\AbstractJob;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

/**
 * Extends core's AbstractJob so admins can route mailings onto their own
 * queue with `(new Extend\Queue())->route(SendMail::class, 'emails')`.
 *
 * That matters for large mailings: this job is pushed once per recipient, so
 * on the default queue every worker in the pool opens an SMTP connection at
 * once. A dedicated queue with a small worker count caps concurrent
 * connections to the mail relay independently of the rest of the queue.
 */
class SendMail extends AbstractJob
{
    public function __construct(
        protected string $email,
        protected string $displayName,
        protected string $subject,
        protected string $text
    ) {
        parent::__construct();
    }

    public function handle(SettingsRepositoryInterface $settings, Mailer $mailer, Translator $translator, Factory $view): void
    {
        $forumTitle = $settings->get('forum_title');
        $userEmail = $this->email;
        $username = $this->displayName;

        $view->share(compact('forumTitle', 'userEmail', 'username'));

        $infoContent = $this->text;

        $subject = '['.$forumTitle.'] '.($this->subject !== '' ? $this->subject : $translator->get('fof-mailing.email.default_subject'));

        $mailer->send(
            [
                'text' => 'fof-mailing::emails.plain.adminMail',
                'html' => 'fof-mailing::emails.html.adminMail',
            ],
            compact('infoContent'),
            function (Message $message) use ($subject) {
                $message->to($this->email);
                $message->subject($subject);
            }
        );
    }
}
