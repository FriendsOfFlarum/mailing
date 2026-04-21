<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Mailing;

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less')
        ->jsDirectory(__DIR__.'/js/dist/forum'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\View())
        ->namespace('fof-mailing', __DIR__.'/resources/views'),

    (new Extend\Routes('api'))
        ->post('/admin-mail', 'fof.mailing.create-mail', Controllers\SendAdminEmailController::class),

    (new Extend\ApiResource(Resource\ForumResource::class))
        ->fields(fn () => [
            Schema\Boolean::make('fofMailingCanMailAll')
                ->get(fn (object $forum, Context $context) => $context->getActor()->can('fof-mailing.mail-all')),
            Schema\Boolean::make('fofMailingCanMailIndividual')
                ->get(fn (object $forum, Context $context) => $context->getActor()->can('fof-mailing.mail-individual')),
        ]),
];
