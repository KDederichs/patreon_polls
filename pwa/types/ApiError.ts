export interface ApiError<T> {
  '@context': string,
  '@type': string,
  description: string
  title: string
  violations?: Array<ApiViolation<T>>
}

export interface ApiViolation<T> {
  propertyPath: string
  message: string
  code: string
}
