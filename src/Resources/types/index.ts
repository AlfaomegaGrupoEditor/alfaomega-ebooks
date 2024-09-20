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
    searchKey: string;
    accessType: AccessType;
    accessStatus: AccessStatus;
};

export {
    ColorVariant,
    SizeVariant,
    AccessType,
    AccessStatus,
    EbooksFilter,
};
