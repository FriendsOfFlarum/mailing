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

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class SendMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $email,
        protected string $displayName,
        protected string $subject,
        protected string $text
    ) {
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
