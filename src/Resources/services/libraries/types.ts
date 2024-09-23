import {BooksQueryType, BookType} from '@/types';
import {SearchResultType} from '@/services/types';

type State = {
  books: BookType[];
  query: BooksQueryType;
  meta: SearchResultType;
}

export {
  State,
}
