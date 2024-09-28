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
    page: number;
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

};
