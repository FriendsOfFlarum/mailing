import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
interface SentModalAttrs extends IInternalModalAttrs {
    recipientsCount: number;
}
export default class SentModal extends Modal<SentModalAttrs> {
    className(): string;
    title(): string | any[];
    content(): any[];
}
export {};
