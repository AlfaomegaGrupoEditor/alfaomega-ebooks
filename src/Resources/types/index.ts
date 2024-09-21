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

type EbooksFilterType = {
    searchKey?: string | null;
    accessType?: AccessType | null;
    accessStatus?: StatusType | null;
};

type OrderType = {
    field: 'title' | 'created_at' | 'valid_until';
    direction: 'asc' | 'desc';
};

type EbooksQueryType = {
    category?: string | null;
    filter: EbooksFilterType;
    page: number;
    pageSize: number;
    userId?: string | null;
    order: OrderType;
}

type BookType = {
    id: string;
    title: string;
    cover: string;
    download: boolean;
    read: boolean;
    accessType: AccessType;
    status: StatusType;
    addedAt: string;
    validUntil?: string | null;
    url: string;
}

export {
    ColorVariantType,
    SizeVariantType,
    AccessType,
    StatusType,
    EbooksFilterType,
    EbooksQueryType,
    OrderType,
    BookType
};
