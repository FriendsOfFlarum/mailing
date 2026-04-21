import { Vnode } from 'mithril';
import { IFormModalAttrs } from 'flarum/common/components/FormModal';
import FormModal from 'flarum/common/components/FormModal';
import Group from 'flarum/common/models/Group';
import User from 'flarum/common/models/User';
import KeyboardNavigatable from 'flarum/common/utils/KeyboardNavigatable';
import Email from '../models/Email';
interface EmailUserModalAttrs extends IFormModalAttrs {
    user?: User;
    forAll?: boolean;
}
type Recipient = Group | User | Email;
export default class EmailUserModal extends FormModal<EmailUserModalAttrs> {
    sending: boolean;
    recipients: Recipient[];
    subject: string;
    messageText: string;
    searchIndex: number;
    navigator: KeyboardNavigatable;
    filter: string;
    focused: boolean;
    loadingResults: boolean;
    searchResults: any[];
    searchTimeout: number;
    oninit(vnode: Vnode): void;
    className(): string;
    title(): string | any[];
    onready(): void;
    recipientLabel(recipient: Recipient): any;
    searchResultKind(recipient: Recipient): string | any[];
    selectResult(result: Recipient | null): void;
    content(): any;
    performNewSearch(): void;
    buildSearchResults(query: string): void;
    onsubmit(event: SubmitEvent): void;
}
export {};
