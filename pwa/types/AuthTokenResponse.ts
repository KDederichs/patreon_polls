export interface AuthTokenResponse {
  token: string
  isPatreonCreator: boolean
  isSubscribestarCreator: boolean
  subscribestarUsername?: string|null
  patreonUsername?: string|null
}
