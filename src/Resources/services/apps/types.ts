export type AppError = any;

export type AppFlag = boolean;

export type HttpHeader = {
  Authorization: string;
  'Access-Control-Allow-Origin': '*';
  'Content-Type': 'application/json;x-www-form-urlencoded';
  'Cache-Control': 'no-cache';
  Accept: string;
}

export type AppConfig = {
  baseURL: string;
  mode: 'no-cors';
  headers: HttpHeader;
  withCredentials: boolean,
  credentials: 'same-origin'
};

export type AppInitPayload = {
}

export type State = {
  error: AppError | undefined;
  loading: AppFlag;
  config: AppConfig | undefined;
}
