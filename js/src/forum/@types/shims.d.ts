import 'flarum/common/models/Forum';

declare module 'flarum/common/models/Forum' {
  export default interface Forum {
    fofMailingCanMailAll: () => boolean;
    fofMailingCanMailIndividual: () => boolean;
  }
}
