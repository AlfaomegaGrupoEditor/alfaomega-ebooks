import {AsyncProcessType, MetaType, ProcessItem} from '@/types';

type State = {
  importNewEbooks: AsyncProcessType;
  updateEbooks: AsyncProcessType;
  linkProducts: AsyncProcessType;
  setupPrices: AsyncProcessType;
  processData: {
    actions: ProcessItem[];
    meta: MetaType
  }
  queueStatus: AsyncProcessType;
}

export {
  State,
}
