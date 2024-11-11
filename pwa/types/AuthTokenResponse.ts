export interface AuthTokenResponse {
  token: string
  userIri: string
  mode: "login" | "connect"
}
