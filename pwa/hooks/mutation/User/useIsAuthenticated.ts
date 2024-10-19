export const useIsAuthenticated = (): boolean => window.localStorage.getItem('token') !== null
