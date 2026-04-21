<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Mailing\Controllers;

use Flarum\Foundation\ValidationException;
use Flarum\Group\Group;
use Flarum\Http\RequestUtil;
use Flarum\User\UserRepository;
use FoF\Mailing\Jobs\SendMail;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SendAdminEmailController implements RequestHandlerInterface
{
    public function __construct(
        protected UserRepository $users,
        protected Queue $queue
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);

        $data = (array) Arr::get($request->getParsedBody(), 'data');
        $recipients = collect((array) Arr::get($data, 'recipients'));

        $userIds = $recipients->filter(function ($model) {
            return Arr::get($model, 'type') === 'users';
        })->map(function ($model) {
            return Arr::get($model, 'id');
        })->toArray();

        $emails = $recipients->filter(function ($model) {
            return Arr::get($model, 'type') === 'fof-mailing-emails';
        })->map(function ($model) {
            return Arr::get($model, 'attributes.email');
        })->toArray();

        $groupIds = $recipients->filter(function ($model) {
            return Arr::get($model, 'type') === 'groups';
        })->map(function ($model) {
            return Arr::get($model, 'id');
        })->toArray();

        if (count($groupIds)) {
            $actor->assertCan('fof-mailing.mail-all');
        } else {
            $actor->assertCan('fof-mailing.mail-individual');
        }

        $userQuery = $this->users->query();

        // If the MEMBER_ID group is passed, we select every user of the database
        // Otherwise we restrict by user and group IDs as given
        if (!in_array(Group::MEMBER_ID, $groupIds)) {
            $userQuery->whereIn('id', $userIds)
                ->orWhereHas('groups', function (Builder $query) use ($groupIds) {
                    $query->whereIn('groups.id', $groupIds);
                });
        }

        $subject = (string) Arr::get($data, 'subject');
        $text = (string) Arr::get($data, 'text');

        $recipientCount = 0;

        $userQuery->chunk(50, function ($users) use ($subject, $text, &$recipientCount) {
            foreach ($users as $user) {
                $this->queue->push(new SendMail($user->email, $user->display_name, $subject, $text));

                $recipientCount++;
            }
        });

        foreach ($emails as $email) {
            $this->queue->push(new SendMail($email, $email, $subject, $text));

            $recipientCount++;
        }

        if ($recipientCount === 0) {
            /**
             * @var Translator $translator
             */
            $translator = resolve(Translator::class);

            throw new ValidationException([
                'recipients' => $translator->get('fof-mailing.api.no_recipients'),
            ]);
        }

        return new JsonResponse([
            'recipientsCount' => $recipientCount,
        ]);
    }
}
