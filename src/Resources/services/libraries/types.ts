import {BookType} from '@/types';

export type SearchResultType = {
  total: number;
  page: number;
  pageSize: number;
}

export type State = {
  books: BookType[];
  meta: SearchResultType;
}
