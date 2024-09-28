import {BooksQueryType, BookType, CatalogType,RedeemType} from '@/types';
import {SearchResultType} from '@/services/types';

type State = {
  books: BookType[];
  query: BooksQueryType;
  meta: SearchResultType;
  catalog: CatalogType;
}

export {
  State,
}
