import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Button from 'flarum/common/components/Button';
import UserDirectoryPage from 'ext:fof/user-directory/forum/components/UserDirectoryPage';

export default function () {
  extend(UserDirectoryPage.prototype, 'actionItems', (items) => {
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
