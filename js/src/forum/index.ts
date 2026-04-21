import app from 'flarum/forum/app';
import addMailingLinks from './addMailingLinks';
import addUserDirectoryLink from './addUserDirectoryLink';

export { default as extend } from './extend';

app.initializers.add('fof-mailing', () => {
  addMailingLinks();

  if ('fof-user-directory' in flarum.extensions) {
    addUserDirectoryLink();
  }
});
