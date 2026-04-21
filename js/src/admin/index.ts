import app from 'flarum/admin/app';

app.initializers.add('fof-mailing', () => {
  app.registry
    .for('fof-mailing')
    .registerPermission(
      {
        icon: 'fas fa-envelope',
        label: app.translator.trans('fof-mailing.admin.permissions.mail_all'),
        permission: 'fof-mailing.mail-all',
      },
      'moderate'
    )
    .registerPermission(
      {
        icon: 'fas fa-envelope',
        label: app.translator.trans('fof-mailing.admin.permissions.mail_individual'),
        permission: 'fof-mailing.mail-individual',
      },
      'moderate'
    );
});
