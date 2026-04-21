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
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class SendMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $email,
        protected string $subject,
        protected string $text,
        protected bool $html = false
    ) {
    }

    public function handle(SettingsRepositoryInterface $settings, Mailer $mailer, Translator $translator): void
    {
        $mailer->send([], [], function (Message $message) use ($settings, $translator) {
            $message->setBody($this->text, $this->html ? 'text/html' : 'text/plain');
            $message->to($this->email);
            $message->subject('['.$settings->get('forum_title').'] '.($this->subject !== '' ? $this->subject : $translator->get('fof-mailing.email.default_subject')));
        });
    }
}
