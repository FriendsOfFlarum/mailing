<?php

/*
 * This file is part of fof/mailing.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $db = $schema->getConnection();

        $db->table('group_permission')
            ->where('permission', 'kilowhat-mailing.mail-all')
            ->update(['permission' => 'fof-mailing.mail-all']);

        $db->table('group_permission')
            ->where('permission', 'kilowhat-mailing.mail-individual')
            ->update(['permission' => 'fof-mailing.mail-individual']);
    },
    'down' => function (Builder $schema) {
        $db = $schema->getConnection();

        $db->table('group_permission')
            ->where('permission', 'fof-mailing.mail-all')
            ->update(['permission' => 'kilowhat-mailing.mail-all']);

        $db->table('group_permission')
            ->where('permission', 'fof-mailing.mail-individual')
            ->update(['permission' => 'kilowhat-mailing.mail-individual']);
    },
];
