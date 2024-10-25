import {ButtonVariant, ColorVariant} from 'bootstrap-vue-next';

type ColorVariantType = 'primary'
    | 'secondary'
    | 'success'
    | 'danger'
    | 'warning'
    | 'info'
    | 'light'
    | 'dark'
    | 'muted'
    | 'white'
    | 'black';

type SizeVariantType = 'sm'
    | 'md'
    | 'lg';

type AccessType = 'purchase'
    | 'sample';

type StatusType = 'created'
    | 'active'
    | 'expired'
    | 'cancelled' ;

type OrderType = {
    field: 'title'
        | 'created_at'
        | 'status'
        | 'valid_until'
        | 'access_at';
    direction: 'asc'
        | 'desc';
};

type BooksFilterType = {
    category?: string | null;
    searchKey?: string | null;
    accessType?: AccessType | null;
    accessStatus?: StatusType | null;
    order: OrderType,
    perPage: number;
    currentPage: number;
};

type BooksQueryType = {
    filter: BooksFilterType;
    userId?: string | null;
}

type BookType = {
    id: string;
    title: string;
    categories: string;
    cover: string;
    download: boolean;
    downloadUrl: string | null;
    read: boolean;
    readUrl: string | null;
    accessType: AccessType;
    status: StatusType;
    addedAt: string;
    validUntil?: string;
    url: string;
}

type CatalogItemsType = {
    id: string;
    title: string;
    children: number[];
};

type CatalogType = {
    root: string[];
    tree: TreeType;
};

type ToastType = {
    title?: string | null;
    content: string;
    variant: ColorVariant;
}

type RedeemStatusType = {
    status: 'success' | 'error';
    message: string;
    data: null;
}

type EbookInfoType = {
    catalog: number;
    imported: number;
}

type ProductsInfoType = {
    catalog: number;
    unlinked: number;
    linked: number;
}

type EbookAccessInfoType = {
    sample: number;
    purchase: number;
    created: number;
    active: number;
    expired: number;
    cancelled: number;
    total: number;
}

type AccessCodeInfoType = {
    samples: number;
    import: number;
    created: number;
    sent: number;
    redeemed: number;
    expired: number;
    cancelled: number;
    total: number;
}

type ProcessType = 'import-new-ebooks'
    | 'update-ebooks'
    | 'link-products'
    | 'setup-prices';

type ProcessNameType = 'import'
    | 'update'
    | 'link'
    | 'setup';

type ProcessStatusType = 'idle'
    | 'processing'
    | 'failed'
    | 'completed'
    | 'pending'
    | 'excluded';

type AsyncProcessType = {
    status: ProcessStatusType;
    completed: Number,
    processing: Number,
    pending: Number,
    failed: Number,
    excluded?: Number
}

type ProcessDataType = {
    [key: string]: any;
}

type ActionType = 'action'
    | 'import'
    | 'exclude'
    | 'retry'
    | 'delete'
    | 'view'
    | 'primary';

interface ProcessItem {
    id: number
    isbn: string
    title: string
    status: string
    type?: ActionType,
    schedule_date: string
    last_attend_date: string,
    data: ProcessDataType,
    logs: ProcessDataType,
}

type MetaType = {
    total: number;
    current_page: number;
    pages: number;
}

type QueueType = 'import-new-ebooks'
    | 'update-ebooks'
    | 'link-products'
    | 'setup-prices';

type SetupPriceFactorType = 'page_count'
    | 'fixed_number'
    | 'percent'
    | 'price_update'
    | 'undefined';

type TreeNodeStateType = {
    opened: boolean;
    checked: boolean;
}

type TreeNodeType = {
    id?: string;
    text?: string;
    title?: string;
    state?: TreeNodeStateType
    children?: string[];
}

type TreeType = {
    [key: string]: TreeNodeType;
}

type CategorySelectedType = {
    categories: string | null | undefined;
    text: string | null | undefined;
    id: string | null | undefined;
}

export {
    AccessCodeInfoType,
    AccessType,
    ActionType,
    AsyncProcessType,
    BookType,
    BooksFilterType,
    BooksQueryType,
    CatalogItemsType,
    CatalogType,
    ColorVariantType,
    EbookAccessInfoType,
    EbookInfoType,
    MetaType,
    OrderType,
    ProcessDataType,
    ProcessItem,
    ProcessNameType,
    ProcessStatusType,
    ProcessType,
    ProductsInfoType,
    QueueType,
    SizeVariantType,
    StatusType,
    ToastType,
    SetupPriceFactorType,
    TreeNodeStateType,
    TreeNodeType,
    TreeType,
    CategorySelectedType
};
