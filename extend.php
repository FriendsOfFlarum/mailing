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

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend;
use Flarum\Api\Context;
use Flarum\Api\Endpoint;
use Flarum\Api\Resource;
use Flarum\Api\Schema;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    new Extend\Locales(__DIR__.'/resources/locale'),
    (new Extend\Routes('api'))
        ->post('/admin-mail', 'fof.mailing.create-mail', Controllers\SendAdminEmailController::class),
    // @TODO: Replace with the new implementation https://docs.flarum.org/2.x/extend/api#extending-api-resources
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(function (ForumSerializer $serializer): array {
            $actor = $serializer->getActor();

            return [
                'fofMailingCanMailAll'        => $actor->can('fof-mailing.mail-all'),
                'fofMailingCanMailIndividual' => $actor->can('fof-mailing.mail-individual'),
            ];
        }),
];
