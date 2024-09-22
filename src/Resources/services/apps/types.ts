type AppError = any;

type HttpHeader = {
  Authorization: string;
  'Access-Control-Allow-Origin': '*';
  'Content-Type': 'application/json;x-www-form-urlencoded';
  'Cache-Control': 'no-cache';
  Accept: string;
}

type AppConfig = {
  baseURL: string;
  mode: 'no-cors';
  headers: HttpHeader;
  withCredentials: boolean,
  credentials: 'same-origin'
};

type AppInitPayload = {
}

type State = {
  error: AppError | undefined;
  loading: AppFlag;
  config: AppConfig | undefined;
}

type CheckApiType = {
    status: 'success' | 'fail';
    message?: string;
}

export {
  AppError,
  HttpHeader,
  AppConfig,
  AppInitPayload,
  State,
  CheckApiType
};
