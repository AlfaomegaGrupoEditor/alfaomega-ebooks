import {BooksQueryType, BookType, CatalogType} from '@/types';
import {SearchResultType} from '@/services/types';

type State = {
  books: BookType[] | null;
  query: BooksQueryType;
  meta: SearchResultType;
  catalog: CatalogType;
}

export {
  State,
}
