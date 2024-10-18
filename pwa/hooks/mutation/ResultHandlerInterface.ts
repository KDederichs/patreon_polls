export interface ResultHandlerInterface<T, K> {
  onSuccess: (response: T) => void
  onError: (error: K) => void
}
