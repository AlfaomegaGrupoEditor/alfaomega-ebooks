import { AsyncProcessType } from '@/types';

type State = {
  importNewEbooks: AsyncProcessType;
  updateEbooks: AsyncProcessType;
  linkProducts: AsyncProcessType;
  setupPrices: AsyncProcessType;
}

export {
  State,
}
