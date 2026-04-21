/// <reference types="flarum/@types/translator-icu-rich" />
import { Vnode } from 'mithril';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Group from 'flarum/common/models/Group';
import User from 'flarum/common/models/User';
import KeyboardNavigatable from 'flarum/common/utils/KeyboardNavigatable';
import Email from '../models/Email';
interface EmailUserModalAttrs extends IInternalModalAttrs {
    user?: User;
    forAll?: boolean;
}
type Recipient = Group | User | Email;
export default class EmailUserModal extends Modal<EmailUserModalAttrs> {
    sending: boolean;
    recipients: Recipient[];
    subject: string;
    messageText: string;
    asHtml: boolean;
    searchIndex: number;
    navigator: KeyboardNavigatable;
    filter: string;
    focused: boolean;
    loadingResults: boolean;
    searchResults: any[];
    searchTimeout: number;
    oninit(vnode: Vnode): void;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    onready(): void;
    recipientLabel(recipient: Recipient): any;
    searchResultKind(recipient: Recipient): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    selectResult(result: Recipient | null): void;
    content(): any;
    performNewSearch(): void;
    buildSearchResults(query: string): void;
    onsubmit(event: SubmitEvent): void;
}
export {};
