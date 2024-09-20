type ColorVariant =
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

type SizeVariant = 'sm' | 'md' | 'lg';

type AccessType = 'purchase' | 'sample';

type AccessStatus = 'created' | 'active' | 'expired' | 'cancelled' ;

type EbooksFilter = {
    searchKey: ?string;
    accessType: ?AccessType;
    accessStatus: ?AccessStatus;
};

type Order = {
    'field': 'title' | 'created_at' | 'valid_until';
    'direction': 'asc' | 'desc';
};

type EbooksQuery = {
    'category': ?string;
    'filter': EbooksFilter;
    'page': number;
    'pageSize': number;
    'userId': ?string;
    'order': Order;
}

export {
    ColorVariant,
    SizeVariant,
    AccessType,
    AccessStatus,
    EbooksFilter,
    EbooksQuery
};
