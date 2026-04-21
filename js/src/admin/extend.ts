import app from 'flarum/admin/app';
import Extend from 'flarum/common/extenders';

export default [
  new Extend.Admin()
    .permission(
      () => ({
        icon: 'fas fa-envelope',
        label: app.translator.trans('fof-mailing.admin.permissions.mail_all'),
        permission: 'fof-mailing.mail-all',
      }),
      'moderate'
    )
    .permission(
      () => ({
        icon: 'fas fa-envelope',
        label: app.translator.trans('fof-mailing.admin.permissions.mail_individual'),
        permission: 'fof-mailing.mail-individual',
      }),
      'moderate'
    ),
];
