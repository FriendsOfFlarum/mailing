/// <reference types="flarum/@types/translator-icu-rich" />
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
interface SentModalAttrs extends IInternalModalAttrs {
    recipientsCount: number;
}
export default class SentModal extends Modal<SentModalAttrs> {
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): any[];
}
export {};
