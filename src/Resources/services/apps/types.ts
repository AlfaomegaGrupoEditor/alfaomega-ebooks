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
  error?: string,
  loading: boolean
};

type AppInitPayload = {
}

type State = {
  error: AppError | undefined;
  loading: boolean;
  config: AppConfig | undefined;
}

type CheckApiType = {
    user_id: string;
}

export {
  AppError,
  HttpHeader,
  AppConfig,
  AppInitPayload,
  State,
  CheckApiType
};
