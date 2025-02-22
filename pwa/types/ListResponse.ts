export interface ListResponse<T> {
  'member': Array<T>
  'totalItems': number
  'view': {
    '@id': string
    type: string
    'first': string
    'last'?: string
    'previous'?: string
    'next'?: string
  }
  'search'?: {
    '@type': string
    'template': string
    'variableRepresentation': string
    'mapping': Array<{
      '@type': string
      variable: string
      property: string
      required: boolean
    }>
  }
}
