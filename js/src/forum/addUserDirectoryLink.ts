import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';

export default function () {
  // Extended by import path rather than by prototype: the user directory page
  // is lazy loaded, so the module does not exist yet when this runs. Passing
  // the path defers the extension until the chunk is loaded.
  extend('ext:fof/user-directory/forum/components/UserDirectoryPage', 'actionItems', (items) => {
    if (app.forum.fofMailingCanMailAll()) {
      items.add(
        'fof-mailing',
        Button.component(
          {
            className: 'Button',
            icon: 'fas fa-envelope',
            onclick() {
              app.modal.show(() => import('./components/EmailUserModal'), { forAll: true });
            },
          },
          app.translator.trans('fof-mailing.forum.links.mail_all')
        ),
        10
      );
    }
  });
}
