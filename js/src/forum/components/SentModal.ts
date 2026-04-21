import app from 'flarum/forum/app';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import icon from 'flarum/common/helpers/icon';

interface SentModalAttrs extends IInternalModalAttrs {
  recipientsCount: number;
}

export default class SentModal extends Modal<SentModalAttrs> {
  className() {
    return 'FofMailingSentModal Modal--small';
  }

  title() {
    return app.translator.trans('fof-mailing.forum.modal_sent.title_text');
  }

  content() {
    return [
      m('.MailingShipping', icon('fas fa-shipping-fast')),
      m('.Modal-body', [
        m(
          'h1',
          app.translator.trans('fof-mailing.forum.modal_sent.on_its_way', {
            recipientsCount: this.attrs.recipientsCount,
          })
        ),
      ]),
    ];
  }
}
