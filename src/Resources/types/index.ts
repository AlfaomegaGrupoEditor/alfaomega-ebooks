type ColorVariantType =
    | 'primary'
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

type SizeVariantType = 'sm' | 'md' | 'lg';

type AccessType = 'purchase' | 'sample';

type StatusType = 'created' | 'active' | 'expired' | 'cancelled' ;

type OrderType = {
    field: 'title' | 'created_at' | 'status'| 'valid_until' | 'access_at';
    direction: 'asc' | 'desc';
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
    validUntil?: string | null;
    url: string;
}

type CatalogItemsType = {
    id: string;
    title: string;
    children: number[];
};

type CatalogType = {
    root: number[];
    items: CatalogItemsType[];
};

type ToastType = {
    title?: string | null;
    content: string;
    variant: ColorVariantType;
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

type ProcessType = 'import-new-ebooks' | 'update-ebooks' | 'link-products' | 'setup-prices';

type ProcessNameType = 'import' | 'update' | 'link' | 'setup';

type ProcessStatusType = 'idle' | 'processing' | 'failed' | 'completed' | 'pending';

type AsyncProcessType = {
    status: ProcessStatusType;
    completed: Number,
    processing: Number,
    pending: Number,
    failed: Number
}

type ProcessDataType = {
    [key: string]: any;
}

interface ProcessItem {
    id: number
    isbn: string
    title: string
    status: string
    schedule_date: string
    last_attend_date: string,
    data: ProcessDataType
}

type MetaType = {
    total: number;
    current_page: number;
    pages: number;
}

type QueueType = 'import-new-ebooks' | 'update-ebooks' | 'link-products' | 'setup-prices';

export {
    ColorVariantType,
    SizeVariantType,
    AccessType,
    StatusType,
    BooksFilterType,
    BooksQueryType,
    OrderType,
    BookType,
    ToastType,
    CatalogType,
    CatalogItemsType,
    EbookInfoType,
    ProductsInfoType,
    EbookAccessInfoType,
    AccessCodeInfoType,
    ProcessType,
    ProcessNameType,
    ProcessStatusType,
    AsyncProcessType,
    ProcessItem,
    MetaType,
    ProcessDataType,
    QueueType
};
