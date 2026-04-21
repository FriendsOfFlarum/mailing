import app from 'flarum/forum/app';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Icon from 'flarum/common/components/Icon';

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
      m(
        '.MailingShipping',
        Icon.component({
          name: 'fas fa-shipping-fast',
        })
      ),
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
