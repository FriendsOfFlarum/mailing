import Extend from 'flarum/common/extenders';
import Forum from 'flarum/common/models/Forum';
import Email from './models/Email';

export default [
  new Extend.Store() //
    .add('fof-mailing-emails', Email),
  new Extend.Model(Forum) //
    .attribute<boolean>('fofMailingCanMailAll')
    .attribute<boolean>('fofMailingCanMailIndividual'),
];
