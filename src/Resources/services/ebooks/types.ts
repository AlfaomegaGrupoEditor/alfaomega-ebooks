import {
  AccessCodeInfoType,
  EbookAccessInfoType,
  EbookInfoType,
  ProductsInfoType,
} from '@/types';

type State = {
  ebooks: EbookInfoType;
  products: ProductsInfoType;
  access: EbookAccessInfoType;
  codes: AccessCodeInfoType;
}

export {
  State,
}
